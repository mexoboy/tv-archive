<?php
declare(strict_types=1);

use NinjaMutex\Mutex;
use Phalcon\Cli\Task;

class RecordTask extends Task
{
    public function mainAction()
    {
        echo "Use argument 'rt' or 'superchannel' for starting recording" . PHP_EOL;
    }

    public function superchannelAction()
    {
        $this->checkLock('sc.recorder.mutex');

        $recorder = $this->getDI()->get('sc.recorder');
        $recorder->run();
    }

    public function rtAction(array $params)
    {
        $this->checkLock('rt.recorder.mutex');

        $recorder = $this->getDI()->get('rt.recorder');
        $recorder->run();
    }

    private function checkLock($mutexName)
    {
        /** @var Mutex $mutex */
        $mutex = $this->getDI()->get($mutexName);

        if ($mutex->isLocked()) {
            throw new RuntimeException('Process already running');
        }

        $mutex->acquireLock();
    }
}