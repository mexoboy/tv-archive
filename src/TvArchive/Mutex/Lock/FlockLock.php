<?php
declare(strict_types=1);

namespace TvArchive\Mutex\Lock;

/**
 * Override unused lockInformation for speed increase performance
 */
class FlockLock extends \NinjaMutex\Lock\FlockLock
{
    protected function generateLockInformation()
    {
        return [];
    }
}