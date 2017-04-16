<?php
declare(strict_types=1);

namespace RussiaToday;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use RussiaToday\Api\Client;
use TvArchive\Schedule\ScheduleProgram;
use TvArchive\Schedule\ScheduleInterface;

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
     * @inheritdoc
     */
    public function getPrograms(DateTimeInterface $from, DateTimeInterface $to): array
    {
        if ($from > $to) {
            throw new InvalidArgumentException("Invalid date range");
        }

        // Create modified DateTime for correct DatePeriod
        $toDate = (new DateTime())
            ->setTimestamp($to->getTimestamp())
            ->modify('23:59:59')
        ;

        $dateInterval = new DateInterval('P1D');
        $datePeriod   = new DatePeriod($from, $dateInterval, $toDate);
        $schedule     = [];

        foreach ($datePeriod as $date) {
            $schedule = array_merge($schedule, $this->getSchedule($date));
        }

        foreach ($schedule as $index => $program) {
            $schedule[$index]['dateTo'] = $schedule[$index + 1]['dateFrom']
                ?? (clone $schedule[$index]['dateFrom'])->modify('+1 day 00:00:00');
        }

        $filteredSchedule = array_filter($schedule, function($program) use ($from, $to) {
            return max($program['dateFrom'], $from) < min($program['dateTo'], $to);
        });

        return array_map(function($item) {
            return new ScheduleProgram($item['title']);
        }, array_values($filteredSchedule));
    }

    private function getSchedule(DateTimeInterface $date)
    {
        $now = (new DateTimeImmutable())
            ->setTimestamp($date->getTimestamp())
            ->modify('00:00:00')
        ;

        return array_map(function($item) use ($now) {
            return [
                'title' => $item['telecastTitle'] ?? $item['programTitle'],
                'dateFrom' => $now->modify("+{$item['time']} seconds")
            ];
        }, $this->client->getSchedule($date));
    }
}