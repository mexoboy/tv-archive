<?php
declare(strict_types=1);

namespace TvArchive\FFmpeg\Log;

class ProgressLog
{
    /**
     * @var int
     */
    public $frame;

    /**
     * @var int - Progress in seconds
     */
    public $time;
}