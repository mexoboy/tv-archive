<?php
declare(strict_types=1);

namespace SuperChannel\Api;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use SoapClient;

/**
 * @method string[] getInfoByTime(string $startTime, string $endTime)
 */
class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var SoapClient
     */
    private $soapClient;

    public function __construct(string $wsdl, LoggerInterface $logger = null)
    {
        $this->wsdl = $wsdl;

        if ($logger) {
            $this->setLogger($logger);
        }
    }

    private function getSoapClient(): SoapClient
    {
        if (!$this->soapClient) {
            $this->soapClient = new SoapClient($this->wsdl);
        }

        return $this->soapClient;
    }

    public function __call($name, $arguments)
    {
        if ($this->logger) {
            $this->logger->info("Call: {$name}", [
                "arguments" => json_encode($arguments)
            ]);
        }

        $response = $this->getSoapClient()->$name(...$arguments);

        if ($this->logger) {
            $this->logger->debug('Response', [
                "response" => json_encode($response),
            ]);
        }

        return $response;
    }
}