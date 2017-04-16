<?php
declare(strict_types=1);

namespace TvArchive\Logger\Adapter;

use Phalcon\Logger\Adapter;
use Psr\Log\LoggerInterface;

class File extends Adapter\File implements LoggerInterface {}