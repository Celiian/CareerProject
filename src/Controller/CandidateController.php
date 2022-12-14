<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Candidature;
use App\Entity\JobOffer;
use App\Entity\Skills;
use App\Repository\SkillsRepository;
use App\Service\MatchingAlgorithm;
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
    public function connectedCandidate(MatchingAlgorithm $matchingAlgorithm, ManagerRegistry $managerRegistry, int $candidate_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $candidateRepository = $entityManager->getRepository(Candidate::class);
        $candidatureRepository = $entityManager->getRepository(Candidature::class);
        $candidate = $candidateRepository->find($candidate_id);

        $arrayOffers = $matchingAlgorithm->getExactAllOffers($candidate_id, $managerRegistry);

        $x = 0;
        $offers = array();
        shuffle($arrayOffers);
        foreach ($arrayOffers as $offer){
            if ($x < 2) {
                $offers[] = $offer;
                $x++;
            }
            else{
                break;
            }
        }

        $candidatures = $candidatureRepository->findAll();

        $appliedCandidatures = array();
        $i = 0;
        foreach ($candidatures as $candidature){
            if ($candidature->getCandidate()->getId() == $candidate_id) {
                $appliedCandidatures[] = $candidature->getJobOffer();
            }
            $i++;
        }


        return $this->render('candidate/connected.html.twig', [
            'candidate' => $candidate,
            'offers' => $offers,
            'candidatures' => $appliedCandidatures,
            'link' => "home"
        ]);
    }


    #[Route('/candidate/{candidate_id}', name: 'edit_candidate')]
    public function editCandidate(Request $request, ManagerRegistry $managerRegistry, int $candidate_id): Response
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


    #[Route('/candidate/delete/{candidate_id}', name: 'delete_candidate')]
    public function deleteCandidate(ManagerRegistry $managerRegistry, int $candidate_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $candidateRepository = $entityManager->getRepository(Candidate::class);
        $candidate = $candidateRepository->find($candidate_id);
        $entityManager->remove($candidate);
        $entityManager->flush();

        return $this->redirectToRoute('connect_candidate');

    }


    #[Route('/candidate/apply/{candidate_id}{offer_id}', name: 'apply_offer_candidate')]
    public function candidateApplyOffer(int $candidate_id, int $offer_id, ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();
        $candidate = $entityManager->getRepository(Candidate::class)->find($candidate_id);
        $offer = $entityManager->getRepository(JobOffer::class)->find($offer_id);


        $candidature = new Candidature();
        $candidature->setCandidate($candidate);
        $candidature->setJobOffer($offer);
        $candidature->setStatus("Waiting for an answer");
        $entityManager->persist($candidature);

        $entityManager->flush();

        return $this->redirectToRoute('connected_candidate', [
            'candidate_id' => $candidate_id
        ]);
    }

    #[Route('/candidate/cancel_apply/{candidate_id}{offer_id}', name: 'cancel_apply_offer_candidate')]
    public function candidateCancelApply(int $candidate_id, int $offer_id, ManagerRegistry $managerRegistry): Response
    {


        $entityManager = $managerRegistry->getManager();
        $candidatureRepository = $entityManager->getRepository(Candidature::class);
        $candidatures = $candidatureRepository->findAll();

        foreach ($candidatures as $candidature) {
            if ($candidature->getJobOffer()->getId() == $offer_id and $candidature->getCandidate()->getId() == $candidate_id) {
                $entityManager->remove($candidature);
            }
        }
        $entityManager->flush();


        return $this->redirectToRoute('connected_candidate', [
            'candidate_id' => $candidate_id
        ]);
    }

    #[Route('/candidate/offers/{candidate_id}', name: 'offers_candidate')]
    public function offersCandidate(MatchingAlgorithm $matchingAlgorithm, ManagerRegistry $managerRegistry, int $candidate_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $candidateRepository = $entityManager->getRepository(Candidate::class);
        $candidatureRepository = $entityManager->getRepository(Candidature::class);
        $candidate = $candidateRepository->find($candidate_id);

        $arrayOffers = $matchingAlgorithm->getExactAllOffers($candidate_id, $managerRegistry);

        $offers = array();
        shuffle($arrayOffers);
        foreach ($arrayOffers as $offer){
                $offers[] = $offer;
        }

        $candidatures = $candidatureRepository->findAll();

        $appliedCandidatures = array();
        $i = 0;
        foreach ($candidatures as $candidature){
            if ($candidature->getCandidate()->getId() == $candidate_id) {
                $appliedCandidatures[] = $candidature->getJobOffer();
            }
            $i++;
        }


        return $this->render('candidate/offers.html.twig', [
            'candidate' => $candidate,
            'offers' => $offers,
            'candidatures' => $appliedCandidatures,
            'link' => "offers"
        ]);
    }


    #[Route('/candidate/candidatures/{candidate_id}', name: 'candidatures_candidate')]
    public function candidaturesCandidate(MatchingAlgorithm $matchingAlgorithm, ManagerRegistry $managerRegistry, int $candidate_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $candidateRepository = $entityManager->getRepository(Candidate::class);
        $candidatureRepository = $entityManager->getRepository(Candidature::class);
        $candidate = $candidateRepository->find($candidate_id);

        $candidatures = $candidate->getCandidatures();

        $appliedCandidatures = array();
        foreach ($candidatures as $candidature){
                $appliedCandidatures[] = $candidature->getJobOffer();

        }


        return $this->render('candidate/offers.html.twig', [
            'candidate' => $candidate,
            'offers' => $appliedCandidatures,
            'candidatures'=> $appliedCandidatures,
            'link' => "candidatures"
        ]);
    }

}