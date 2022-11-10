<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Company;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConnexionController extends AbstractController
{

    #[Route('/inscription', name: 'inscription')]
    public function inscription(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();

        $candidate = new Candidate();
        $formCandidate = $this->createFormBuilder($candidate)
            ->add('name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('mail', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create'])
            ->getForm();

        $formCandidate->handleRequest($request);
        if ($formCandidate->isSubmitted() && $formCandidate->isValid()) {
            $candidate = $formCandidate->getData();

            $entityManager->persist($candidate);

            $entityManager->flush();

            return $this->redirectToRoute('connected_candidate', ['candidate_id' => $candidate->getId()]);
        }

        $company = new Company();
        $formCompany = $this->createFormBuilder($company)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('mail', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create'])
            ->getForm();

        $formCompany->handleRequest($request);
        if ($formCompany->isSubmitted() && $formCompany->isValid()) {
            $company = $formCompany->getData();

            $entityManager->persist($company);

            $entityManager->flush();


            return $this->redirectToRoute('connected_company', ['company_id' => $company->getId()]);
        }
        return $this->renderForm('connexions/inscription.html.twig', [
            'formCandidate' => $formCandidate,
            'formCompany' => $formCompany
        ]);

    }


    #[Route('/login', name: 'login')]
    public function login(Request $request, ManagerRegistry $managerRegistry): Response
    {

        $entityManager = $managerRegistry->getManager();

        $candidate = new Candidate();
        $formCandidate = $this->createFormBuilder($candidate)
            ->add('mail', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Sign in'])
            ->getForm();

        $formCandidate->handleRequest($request);
        if ($formCandidate->isSubmitted() && $formCandidate->isValid()) {
                $candidate = $formCandidate->getData();

            $candidate = $entityManager->getRepository(Candidate::class)->findOneBy(["mail" => $candidate->getMail()]);

            return $this->redirectToRoute('connected_candidate', ['candidate_id' => $candidate->getId()]);
        }

        $company = new Company();
        $formCompany = $this->createFormBuilder($company)
            ->add('mail', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Sign In'])
            ->getForm();

        $formCompany->handleRequest($request);
        if ($formCompany->isSubmitted() && $formCompany->isValid()) {
            $company = $formCompany->getData();

            $company = $entityManager->getRepository(Company::class)->findOneBy(["mail" => $company->getMail()]);

            return $this->redirectToRoute('connected_company', ['company_id' => $company->getId()]);
        }
        return $this->renderForm('connexions/login.html.twig', [
            'formCandidate' => $formCandidate,
            'formCompany' => $formCompany
        ]);

    }


}