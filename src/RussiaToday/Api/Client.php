<?php
declare(strict_types=1);

namespace RussiaToday\Api;

use DateTimeInterface;
use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Response;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var HttpClient
     */
    private $client;

    public function __construct(string $baseUrl, LoggerInterface $logger = null)
    {
        $this->baseUrl = $baseUrl;

        if ($logger) {
            $this->setLogger($logger);
        }
    }

    private function getClient(): HttpClient
    {
        if (!$this->client) {
            $this->client = new HttpClient($this->baseUrl);
        }

        return $this->client;
    }

    /**
     * @param string $uri
     *
     * @return Response
     *
     * @throws RequestException
     */
    private function request(string $uri): Response
    {
        return $this->getClient()->get($uri)->send();
    }

    public function getSchedule(DateTimeInterface $dateTime): array
    {
        $date = $dateTime->format('d-m-Y');

        try {
            $response = $this->request("/schedulejson/news/{$date}");
        } catch (RequestException $e) {
            if ($this->logger) {
                $this->logger->error($e->getMessage(), ["date" => $date]);
            }

            throw $e;
        }

        return json_decode($response->getBody(true), true);
    }
}