<?php

namespace Ndthuan\FshareCli\Downloading\History;

use Ndthuan\FshareLib\Api\DTO\FshareFile;

/**
 * Interface for working with queueing history.
 */
interface HistoryProviderInterface
{
    /**
     * Checks if a file is waiting in queue or successfully downloaded.
     *
     * @param FshareFile $fshareFile
     *
     * @return boolean
     */
    public function hasInQueue(FshareFile $fshareFile);

    /**
     * Saves a file to history.
     *
     * @param FshareFile $fshareFile
     */
    public function saveHistory(FshareFile $fshareFile);
}
