<?php

namespace App\Service;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramNotifier
{
    public function __construct(
        private string $chatId,
        private string $apiKey,
        private HttpClientInterface $httpClient,
    ) {}

    public function send(string $message): void
    {
        try {
            $response = $this->httpClient->request('GET', sprintf(
                'https://api.telegram.org/bot%s/sendMessage',
                $this->apiKey,
            ), [
                'query' => [
                    'chat_id' => '-' . $this->chatId,
                    'text' => $message,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('Telegram API returned HTTP ' . $response->getStatusCode());
            }

        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException('Network error: ' . $e->getMessage());
        }
    }
}
