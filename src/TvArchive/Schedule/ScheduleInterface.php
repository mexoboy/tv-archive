<?php
declare(strict_types=1);

namespace TvArchive\Schedule;

use DateTimeInterface;

interface ScheduleInterface
{
    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return ScheduleProgramInterface[]
     */
    public function getPrograms(DateTimeInterface $from, DateTimeInterface $to): array;
}