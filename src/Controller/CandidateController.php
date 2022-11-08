<?php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Skills;
use App\Repository\SkillsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CandidateController extends AbstractController
{

    #[Route('/candidate/create', name: 'create_candidate')]
    public function createCandidate(Request $request, ManagerRegistry $managerRegistry): Response
    {

        $candidate = new Candidate();
        $entityManager = $managerRegistry->getManager();

        $form = $this->createFormBuilder($candidate)
            ->add('name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('mail', TextType::class)
            ->add('skills', EntityType::class, [
                'class' => Skills::class,
                'query_builder' => function (SkillsRepository $skillsRepository) {
                    return $skillsRepository->createQueryBuilder('s');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Create your account'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidate = $form->getData();

            $entityManager->persist($candidate);

            $entityManager->flush();

            return $this->redirectToRoute('home');
        }


        return $this->renderForm('candidate/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/candidate/connect', name: 'connect_candidate')]
    public function connectCandidate(Request $request, ManagerRegistry $managerRegistry): Response
    {

        $entityManager = $managerRegistry->getManager();

        $candidateRepository = $entityManager->getRepository(Candidate::class);
        $allCandidates = $candidateRepository->findAll();


        return $this->render('candidate/connect.html.twig', [
            'candidates' => $allCandidates,
        ]);
    }

    #[Route('/candidate/connected/{candidate_id}', name: 'connected_candidate')]
    public function connectedCandidate(ManagerRegistry $managerRegistry, int $candidate_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $candidateRepository = $entityManager->getRepository(Candidate::class);
        $candidate = $candidateRepository->find($candidate_id);

        return $this->render('candidate/connected.html.twig', [
            'candidate' => $candidate
        ]);
    }



    #[Route('/candidate/{candidate_id}', name: 'edit_candidate')]
    public function Editcandidate(Request $request, ManagerRegistry $managerRegistry, int $candidate_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $candidateRepository = $entityManager->getRepository(Candidate::class);
        $candidate = $candidateRepository->find($candidate_id);

        $form = $this->createFormBuilder($candidate)
            ->add('name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('mail', TextType::class)
            ->add('skills', EntityType::class, [
                'class' => Skills::class,
                'query_builder' => function (SkillsRepository $skillsRepository) {
                    return $skillsRepository->createQueryBuilder('s');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Edit your account'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $candidate = $form->getData();

            $entityManager->persist($candidate);

            $entityManager->flush();

            return $this->redirectToRoute('home');
        }


        return $this->renderForm('candidate/edit.html.twig', [
            'form' => $form,
            'candidate_id' => $candidate_id
        ]);
    }


}