<?php

declare(strict_types=1);

namespace App\Service;

use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordService
{
    public function __construct(
        private RequestStack          $requestStack,
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface   $httpClient,
    ) {
    }

    public function sendInfoMessage(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ('123' === $webhook = $this->parameterBag->get('app.discord-webhook')) {
            throw new RuntimeException('You forgot to set the discord webhook in your .env.local file');
        }

        $dateAndTime = date('d.m.Y - H:i') . 'Uhr';
        $ipAddress = $request->getClientIp() ?? 'Unknown';
        $userAgent = $request->headers->get('User-Agent') ?? 'Unknown';

        $description = "**Datum und Uhrzeit**: $dateAndTime" . PHP_EOL;
        $description .= "**IP-Adresse**: $ipAddress" . PHP_EOL;
        $description .= "**User-Agent**: $userAgent";

        $this->httpClient->request('POST', $webhook, [
            'json' => [
                'embeds' => [
                    [
                        'author' => [
                            'name' => 'Neustadt-Website',
                        ],
                        'description' => $description,
                        "color" => 2353933,
                    ]
                ]
            ],
        ]);
    }
}
