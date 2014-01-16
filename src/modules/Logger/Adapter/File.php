<?php

namespace Logger\Adapter;

use Logger\Adapter\Abstraction\AdapterInterface;

class File implements AdapterInterface
{
    /**
     * @var string
     */
    private $dirPath;

    /**
     * Default constructor
     *
     * @param string $dirPath
     */
    function __construct($dirPath)
    {
        $this->ratifyDirPath($dirPath);

        $this->dirPath = rtrim($dirPath, '/');
    }

    /**
     * Ensure the log dir is valid and if not, attempt to create it.
     *
     * @param string $logDirPath
     *
     * @throws \RuntimeException
     */
    private function ratifyDirPath($logDirPath)
    {
        if (file_exists($logDirPath) && is_dir($logDirPath)) {
            return;
        }

        $created = mkdir($logDirPath, 0755, true);

        if (!$created) {
            throw new \RuntimeException("Unable to create log dir '$logDirPath'");
        }
    }

    /**
     * @param string $level
     * @param string $log
     *
     * @return void
     */
    public function send($level, $log)
    {
        error_log(
            '[' . strtoupper($level) . '] ' . trim($log) . "\n",
            3,
            $this->dirPath . '/' . date('Y-m-d') . '.log'
        );
    }

    /**
     * Default destructor
     */
    public function __destruct()
    {
        $filePath = $this->dirPath . '/' . date('Y-m-d') . '.log';

        if (file_exists($filePath)) {
            chmod($filePath, 0666);
        }
    }
}