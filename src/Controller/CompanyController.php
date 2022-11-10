<?php
// src/Controller/CompanyController.php
// src/Controller/CompanyController.php
namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Company;
use App\Entity\JobOffer;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{

    #[Route('/company', name: 'company')]
    public function company(): Response
    {
        return $this->render('company/home.html.twig', [
        ]);
    }


    #[Route('/company/connect', name: 'connect_company')]
    public function connectCompany(ManagerRegistry $managerRegistry): Response
    {

        $entityManager = $managerRegistry->getManager();

        $companyRepository = $entityManager->getRepository(Company::class);
        $allCompanies = $companyRepository->findAll();


        return $this->render('company/connect.html.twig', [
            'companies' => $allCompanies,
        ]);
    }


    #[Route('/company/connected/{company_id}', name: 'connected_company')]
    public function connectedCompany(ManagerRegistry $managerRegistry, int $company_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $companyRepository = $entityManager->getRepository(Company::class);
        $company = $companyRepository->find($company_id);

        return $this->render('company/connected.html.twig', [
            'company' => $company
        ]);
    }


    #[Route('/company/new', name: 'new_company')]
    public function newCompany(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $company = new Company();
        $entityManager = $managerRegistry->getManager();

        $form = $this->createFormBuilder($company)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('mail', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Company'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();

            $entityManager->persist($company);

            $entityManager->flush();

            return $this->redirectToRoute('company');
        }

        return $this->renderForm('company/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/companies/{company_id}', name: 'edit_company')]
    public function editCompany(Request $request, ManagerRegistry $managerRegistry, int $company_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $companyRepository = $entityManager->getRepository(Company::class);
        $company = $companyRepository->find($company_id);

        $form = $this->createFormBuilder($company)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('mail', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Edit Company'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();

            $entityManager->persist($company);

            $entityManager->flush();

            return $this->redirectToRoute('company');
        }


        return $this->renderForm('company/edit.html.twig', [
            'form' => $form,
            'company_id' => $company_id
        ]);
    }


    #[Route('/company/delete/{company_id}', name: 'delete_company')]
    public function deleteCompany(ManagerRegistry $managerRegistry, int $company_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $companyRepository = $entityManager->getRepository(Company::class);
        $company = $companyRepository->find($company_id);
        $entityManager->remove($company);
        $entityManager->flush();

        return $this->redirectToRoute('connect_company');

    }


    #[Route('/company/{company_id}/offers', name: 'company_candidate')]
    public function candidatesCompany(ManagerRegistry $managerRegistry, int $company_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $candidatureRepository = $entityManager->getRepository(Candidature::class);
        $companyRepository = $entityManager->getRepository(Company::class);

        $company = $companyRepository->find($company_id);
        $offers = $company->getJobOffers();

        $candidaturesInfo = array();
        foreach ($offers as $offer) {
            $candidatureInfo = array();
            $candidatures = $candidatureRepository->findBy(["jobOffer" => $offer]);
            $list = array();
            foreach ($candidatures as $candidature) {
                $list[] = $candidature;
            }

            $candidatureInfo = [
                "offer" => $offer,
                "candidatures" => $list,
            ];

            $candidaturesInfo[] = $candidatureInfo;

        }


        return $this->renderForm('company/candidate.html.twig', [
            'candidateList' => $candidaturesInfo,
            'company_id' => $company_id
        ]);
    }

    #[Route('/mail', name: 'mail')]
    public function mail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('celian.opigez@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        try {
            $mailer->send($email);
            var_dump("succeed");

        } catch (TransportExceptionInterface $e) {
            var_dump($e);
            var_dump("bug");
        }


        return $this->renderForm("mail.html.twig");

    }


}