<?php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Company;
use App\Entity\JobOffer;
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
    public function createCandidate(Request $request, ManagerRegistry $doctrine): Response
    {

        $candidate = new Candidate();
        $entityManager = $doctrine->getManager();

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




}