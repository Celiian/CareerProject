<?php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Candidature;
use App\Entity\Company;
use App\Entity\JobOffer;
use App\Service\MailSender;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{

    #[Route('/company/connect', name: 'connect_company')]
    public function connectCompany(ManagerRegistry $managerRegistry): Response
    {

        $entityManager = $managerRegistry->getManager();

        $companyRepository = $entityManager->getRepository(Company::class);
        $allCompanies = $companyRepository->findAll();


        return $this->render('company/connect.html.twig', [
            'companies' => $allCompanies,
            'link' => "offers"
        ]);
    }


    #[Route('/company/{company_id}', name: 'edit_company')]
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
            'company' => $company,
            'link' => "edit"
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
            'offerList' => $candidaturesInfo,
            'company' => $company,
            'link' => 'offers'
        ]);
    }

    #[Route('/company/{company_id}/validate/{offer_id}{candidate_id}', name: 'validate_candidate')]
    public function validateCandidateCompany(MailSender $mailer,MailerInterface $mailerInterface, ManagerRegistry $managerRegistry, int $offer_id, int $candidate_id): Response
    {
        $entityManager = $managerRegistry->getManager();
        $jobOffersRepository = $entityManager->getRepository(JobOffer::class);
        $jobOffer = $jobOffersRepository->find($offer_id);
        $candidate = $entityManager->getRepository(Candidate::class)->find($candidate_id);
        $company = $jobOffer->getCompany();

        $subject = "Your Candidature to " . $jobOffer->getName();
        $html ="<p>We are glad to announce you that your candidature to the post : " . $jobOffer->getName() . " have been accepted !  
                <br>
                You can contact us via e-mail here : ". $company->getMail()
                ."</p>";
        $mailer->sendMail($mailerInterface, $company->getMail(), $candidate->getMail(), $subject, "", $html );

        $entityManager->remove($jobOffer);
        $entityManager->flush();

        return $this->redirectToRoute('company_candidate', [
            'company_id' => $company->getId(),
        ]);
    }

}