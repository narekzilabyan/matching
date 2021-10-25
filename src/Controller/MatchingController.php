<?php

namespace App\Controller;

use App\Service\MatchingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatchingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, MatchingService $matchingService): Response
    {
        $csvFile = $request->files->get('csv_file');

        if ($csvFile) {
            $result = $matchingService->getMatchingList($csvFile);
            return $this->redirectToRoute('list', ['matching' => $result]);
        }

        return $this->render('index.html.twig', []);
    }

    /**
     * @Route("/list", name="list")
     */
    public function list(Request $request): Response
    {
        $matching = $request->get('matching');

        return $this->render('list.html.twig', ['matching' => $matching]);
    }
}
