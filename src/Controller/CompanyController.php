<?php
// src/Controller/CompanyController.php
// src/Controller/CompanyController.php
namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

        $objectManager = $managerRegistry->getManager();

        $companyRepository = $objectManager->getRepository(Company::class);
        $allCompanies = $companyRepository->findAll();


        return $this->render('company/connect.html.twig', [
            'companies' => $allCompanies,
        ]);
    }


    #[Route('/company/connected/{company_id}', name: 'connected_company')]
    public function connectedCompany(ManagerRegistry $managerRegistry, int $company_id): Response
    {
        $objectManager = $managerRegistry->getManager();
        $companyRepository = $objectManager->getRepository(Company::class);
        $company = $companyRepository->find($company_id);

        return $this->render('company/connected.html.twig', [
            'company' => $company
        ]);
    }


    #[Route('/company/new', name: 'new_company')]
    public function NewCompany(Request $request, ManagerRegistry $doctrine): Response
    {
        $company = new Company();
        $entityManager = $doctrine->getManager();

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
    public function EditCompany(Request $request, ManagerRegistry $managerRegistry, int $company_id): Response
    {
        $objectManager = $managerRegistry->getManager();
        $companyRepository = $objectManager->getRepository(Company::class);
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

            $objectManager->persist($company);

            $objectManager->flush();

            return $this->redirectToRoute('company');
        }


        return $this->renderForm('company/edit.html.twig', [
            'form' => $form,
            'company_id' => $company_id
        ]);
    }


    #[Route('/company/delete/{company_id}', name: 'delete_company')]
    public function DeleteCompany(Request $request, ManagerRegistry $managerRegistry, int $company_id): Response
    {
        $objectManager = $managerRegistry->getManager();
        $companyRepository = $objectManager->getRepository(Company::class);
        $company = $companyRepository->find($company_id);
        $objectManager->remove($company);
        $objectManager->flush();

        return $this->redirectToRoute('connect_company');

    }
}