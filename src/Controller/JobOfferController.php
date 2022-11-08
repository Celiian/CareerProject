<?php
// src/Controller/CompanyController.php
// src/Controller/CompanyController.php
namespace App\Controller;

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

class JobOfferController extends AbstractController
{

    #[Route('/job_offer/create/{id}', name: 'create_job_offer')]
    public function createJobOffer(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $jobOffer = new JobOffer();
        $entityManager = $doctrine->getManager();

        $form = $this->createFormBuilder($jobOffer)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('skills', EntityType::class, [
                'class' => Skills::class,
                'query_builder' => function (SkillsRepository $skillsRepository) {
                    return $skillsRepository->createQueryBuilder('s');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('salary', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create a new job offer'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $companyRepository = $entityManager->getRepository(Company::class);
            $company = $companyRepository->find($id);

            $jobOffer->setCompany($company);
            $jobOffer = $form->getData();

            $entityManager->persist($jobOffer);

            $entityManager->flush();

            return $this->redirectToRoute('company');
        }


        return $this->renderForm('jobOffer/create.html.twig', [
            'form' => $form,
            'id' => $id,
        ]);
    }


    #[Route('/job_offer/company/{id}', name: 'company_job_offer')]
    public function jobOffersCompany(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $jobOffersRepository = $entityManager->getRepository(JobOffer::class);
        $jobs = $jobOffersRepository->findAll();

        return $this->renderForm('jobOffer/company.html.twig', [
            'jobs' => $jobs,
            'id' => $id
        ]);
    }


    #[Route('/job_offer/modify/{id}', name: 'modify_job_offer')]
    public function jobOffersModify(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $jobOffersRepository = $entityManager->getRepository(JobOffer::class);
        $jobOffer = $jobOffersRepository->find($id);

        $form = $this->createFormBuilder($jobOffer)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('skills', EntityType::class, [
                'class' => Skills::class,
                'query_builder' => function (SkillsRepository $skillsRepository) {
                    return $skillsRepository->createQueryBuilder('s');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('salary', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Modify this job offer'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $jobOffer = $form->getData();

            $entityManager->persist($jobOffer);

            $entityManager->flush();

            return $this->redirectToRoute('company_job_offer', [
                'id' => $jobOffer->getCompany()->getId()
            ]);
        }

        return $this->renderForm('jobOffer/modify.html.twig', [
            'form'=> $form,
            'id' => $jobOffer->getCompany()->getId()
        ]);
    }
}