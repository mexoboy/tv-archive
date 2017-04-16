<?php
declare(strict_types=1);

namespace SuperChannel;

use DateTimeInterface;
use RuntimeException;
use SuperChannel\Api\Client;
use TvArchive\Schedule\ScheduleInterface;
use TvArchive\Schedule\ScheduleProgram;

class Schedule implements ScheduleInterface
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return ScheduleProgram[]
     */
    public function getPrograms(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $timeFormat = 'Y-m-d H:i';
        $programs   = $this->client->getInfoByTime($from->format($timeFormat), $to->format($timeFormat));

        if (!$programs) {
            throw new RuntimeException("Cannot receive program list for superchannel");
        }

        return array_map(function($program) {
            return new ScheduleProgram($program);
        }, $programs);
    }
}