<?php
declare(strict_types=1);

namespace TvArchive\FFmpeg;

use Amp;
use Amp\Process;
use InvalidArgumentException;
use Program;
use Record;
use RecordProgram;
use TvArchive\FFmpeg\Log\Parser;
use TvArchive\Schedule\ScheduleInterface;

class Recorder
{
    /**
     * @var string
     */
    private $streamUrl;

    /**
     * @var ScheduleInterface
     */
    private $schedule;

    /**
     * @var string
     */
    private $storageDirectory;

    /**
     * @var int
     */
    private $recordLength;

    /**
     * @var int
     */
    private $preRecordLength;

    /**
     * @var string|null
     */
    private $ffmpegExtraOptions = null;

    public function __construct(
        string $streamingUrl,
        ScheduleInterface $schedule,
        string $storageDirectory,
        int $recordLength,
        int $preRecordLength,
        string $ffmpegExtraOptions = null
    ) {
        if ($recordLength < 0 || $preRecordLength < 0 || $recordLength <= $preRecordLength) {
            throw new InvalidArgumentException("Invalid record or preRecord values");
        }

        $this->streamUrl          = $streamingUrl;
        $this->schedule           = $schedule;
        $this->storageDirectory   = $storageDirectory;
        $this->recordLength       = $recordLength;
        $this->preRecordLength    = $preRecordLength;
        $this->ffmpegExtraOptions = $ffmpegExtraOptions;
    }

    public function run()
    {
        Amp\run(function() {
            Amp\repeat(function() {}, 1000); // Handle loop for lower preRecordLength arguments
            $this->record();
        });
    }

    private function record()
    {
        $record = new Record();
        $record->setRecordToByMovieLength($this->recordLength);
        $record->generateFileName('mp4');
        $recordPath = "{$this->storageDirectory}/{$record->file_name}";

        $cmd = "ffmpeg -i '{$this->streamUrl}' -c copy -f mp4 -t {$this->recordLength} {$this->ffmpegExtraOptions} {$recordPath}";

        $process = new Amp\Process($cmd);
        $promise = $process->exec(Process::BUFFER_ALL);

        echo "Start record: {$record->file_name}\n";

        $preRecordStarted = false;

        $promise->watch(function(array $data) use (&$preRecordStarted) {
            $log = Parser::frameLog($data[1]);

            if ($log && !$preRecordStarted) {
                if ($log->time > $this->recordLength - $this->preRecordLength) {
                    $preRecordStarted = true;

                    $this->record();
                }
            }
        });

        $promise->when(function() use ($record) {
            $record->save();

            $schedulePrograms = $this->schedule->getPrograms($record->getRecordFrom(), $record->getRecordTo());

            foreach ($schedulePrograms as $index => $scheduleProgram) {
                $program = Program::getByName($scheduleProgram->getTitle());

                if (!$program) {
                    $program = new Program();

                    $program->name = $scheduleProgram->getTitle();
                    $program->save();
                }

                $recordProgram = new RecordProgram();
                $recordProgram->record_id = $record->id;
                $recordProgram->program_id = $program->id;
                $recordProgram->position = $index;
                $recordProgram->save();
            }
        });
    }
}