<?php

namespace App\Service;

use App\Entity\CallRecord;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvProcessor
{
    private GeolocationService $geoService;
    private EntityManagerInterface $em;

    public function __construct(GeolocationService $geoService, EntityManagerInterface $em)
    {
        $this->geoService = $geoService;
        $this->em = $em;
    }

    public function process(UploadedFile $file): void
    {
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            throw new \RuntimeException('Unable to open file.');
        }

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($data) !== 5) {
                continue; // skip invalid lines
            }

            [$customerId, $callDate, $duration, $phoneNumber, $ipAddress] = $data;

            $ipContinent = $this->geoService->getContinentByIp($ipAddress);
            $phoneContinent = $this->geoService->getContinentByPhone($phoneNumber);

            $record = new CallRecord();
            $record->setCustomerId((int)$customerId);
            $record->setCallDate(new \DateTime($callDate));
            $record->setDuration((int)$duration);
            $record->setPhoneNumber($phoneNumber);
            $record->setIpAddress($ipAddress);
            $record->setIpContinent($ipContinent);
            $record->setPhoneContinent($phoneContinent);

            $this->em->persist($record);
        }

        fclose($handle);
        $this->em->flush();
    }
}
