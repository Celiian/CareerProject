<?php
// src/Service/MessageGenerator.php
namespace App\Service;

use App\Entity\Candidate;
use App\Entity\JobOffer;
use Doctrine\Persistence\ManagerRegistry;

class MatchingAlgorithm
{
    public function getExactOffers(int $candidate_id, ManagerRegistry $managerRegistry): array
    {
        $entityManager = $managerRegistry->getManager();
        $candidateRepository = $entityManager->getRepository(Candidate::class);
        $candidate = $candidateRepository->find($candidate_id);

        $offerRepository = $entityManager->getRepository(JobOffer::class);
        $offers = $offerRepository->findAll();

        $skills_candidate = $candidate->getSkills();

        $arrayOffers = array();

        $arraySkillCandidate = array();
        for ($i = 0; $i < count($skills_candidate); ++$i) {
            $arraySkillCandidate[$i] = $skills_candidate[$i]->getName();
        }

        foreach ($offers as $offer) {
            $offerSkills = $offer->getSkills();

            $arraySkillOffer = array();
            for ($i = 0; $i < count($offerSkills); ++$i) {
                $arraySkillOffer[$i] = $offerSkills[$i]->getName();
            }

            if ($arraySkillCandidate == $arraySkillOffer) {
                $arrayOffers[] = $offer;
            }
        }



        return $arrayOffers;
    }
}