<?php

namespace App\Controller;

use App\Form\UploadCsvType;
use App\Service\CsvProcessor;
use App\Service\StatsAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UploadController extends AbstractController
{
    #[Route('/upload', name: 'upload_csv')]
    public function upload(Request $request, CsvProcessor $csvProcessor, StatsAggregator $statsAggregator): Response
    {
        $form = $this->createForm(UploadCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('csv_file')->getData();

            if ($csvFile) {
                try {
                    $csvProcessor->process($csvFile);

                    // Redirect with flash message to avoid form resubmission!
                    $this->addFlash('success', 'File processed and records saved!');
                    return $this->redirectToRoute('upload_csv');
                } catch (\Throwable $e) {
                    $this->addFlash('error', 'Error: ' . $e->getMessage());
                    return $this->redirectToRoute('upload_csv');
                }
            }
        }

        return $this->render('upload/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/stats', name: 'fetch_stats')]
    public function fetchStats(StatsAggregator $statsAggregator): JsonResponse
    {
        return $this->json($statsAggregator->getStats());
    }
}