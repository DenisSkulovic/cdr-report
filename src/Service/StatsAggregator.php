<?php

namespace App\Service;

use App\Entity\CallRecord;
use Doctrine\ORM\EntityManagerInterface;

class StatsAggregator
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getStats(): array
    {
        $records = $this->em->getRepository(CallRecord::class)->findAll();

        $stats = [];

        /** @var CallRecord $record */
        foreach ($records as $record) {
            $customerId = $record->getCustomerId();

            if (!isset($stats[$customerId])) {
                $stats[$customerId] = [
                    'total_calls' => 0,
                    'total_duration' => 0,
                    'same_continent_calls' => 0,
                    'same_continent_duration' => 0,
                ];
            }

            $stats[$customerId]['total_calls'] += 1;
            $stats[$customerId]['total_duration'] += $record->getDuration();

            if ($record->getIpContinent() === $record->getPhoneContinent()) {
                $stats[$customerId]['same_continent_calls'] += 1;
                $stats[$customerId]['same_continent_duration'] += $record->getDuration();
            }
        }

        return $stats;
    }
}
