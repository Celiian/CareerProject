<?php declare(strict_types=1);

namespace App\Tests;

use App\Entity\Candidate;
use App\Service\MatchingAlgorithm;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class MatchingAlgorithmTest extends TestCase
{
    public function testAlgorithm(MatchingAlgorithm $matchingAlgorithm, ManagerRegistry $managerRegistry): void
    {
        $offers = $matchingAlgorithm->getExactAllOffers(2, $managerRegistry);
        $candidate = $managerRegistry->getRepository(Candidate::class)->find(2);

        $candidateSkills = $candidate->getSkill();
        foreach ($offers as $offer) {
            $offerSkill = $offer->getSkill();
            $this->assertTrue($candidateSkills == $offerSkill or !array_diff($offerSkill, $candidateSkills));
        }
    }
}