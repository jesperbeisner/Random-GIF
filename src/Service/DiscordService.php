<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use DateTimeZone;
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

        $dateAndTime = $this->getDateAndTime();
        $ipAddresses = $this->getIpAddresses();
        $userAgent = $request->headers->get('User-Agent') ?? 'Unknown';

        $description = "**Datum und Uhrzeit**: $dateAndTime" . PHP_EOL;
        $description .= "**IP-Adressen**: $ipAddresses" . PHP_EOL;
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

    private function getDateAndTime(): string
    {
        $dateAndTime = new DateTime('now', new DateTimeZone('Europe/Berlin'));
        return $dateAndTime->format('d.m.Y - H:i') . 'Uhr';
    }

    public function getIpAddresses(): string
    {
        $result = 'Unknown';

        $request = $this->requestStack->getCurrentRequest();
        $clientIps = $request->getClientIps();

        if (count($clientIps) > 0) {
            $result = '';
            foreach ($clientIps as $clientIp) {
                $result .= $clientIp . ' - ';
            }
        }

        if ($result !== 'Unknown') {
            $result = substr($result, 0, -3);
        }

        return $result;
    }
}
