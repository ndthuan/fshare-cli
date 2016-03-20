<?php

namespace Ndthuan\FshareCli\Downloading;

use Ndthuan\FshareLib\Api\DTO\FshareFile;

class Job
{
    /**
     * @var FshareFile
     */
    private $fshareFile;

    /**
     * @var string
     */
    private $directory;

    /**
     * Job constructor.
     *
     * @param FshareFile $fshareFile
     * @param string $directory
     */
    public function __construct(FshareFile $fshareFile, $directory)
    {
        $this->fshareFile = $fshareFile;
        $this->directory = $directory;
    }

    /**
     * @return FshareFile
     */
    public function getFshareFile()
    {
        return $this->fshareFile;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
