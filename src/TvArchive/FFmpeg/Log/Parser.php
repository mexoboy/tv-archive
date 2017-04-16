<?php
declare(strict_types=1);

namespace TvArchive\FFmpeg\Log;

use DateTimeImmutable;

class Parser
{
    public static function frameLog(string $line):? ProgressLog
    {
        $framePattern = '/frame=\s*(\d+).*time=([\d:.]+)/';

        if (preg_match($framePattern, $line, $matches)) {
            $log = new ProgressLog();
            $now = (new DateTimeImmutable())->modify('00:00:00');

            $log->frame   = (int) $matches[1];
            $log->time    = $now->modify($matches[2])->getTimestamp() - $now->getTimestamp();

            return $log;
        }

        return null;
    }
}