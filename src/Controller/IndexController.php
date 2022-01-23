<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DiscordService;
use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(DiscordService $discordService, ImageService $imageService): Response
    {
        $discordService->sendInfoMessage();

        return $this->render('index/index.html.twig', [
            'image' => $imageService->getRandomImageName(),
        ]);
    }
}
