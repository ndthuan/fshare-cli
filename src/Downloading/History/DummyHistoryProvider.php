<?php

namespace Ndthuan\FshareCli\Downloading\History;

use Ndthuan\FshareLib\Api\DTO\FshareFile;

/**
 * This class provides a dummy support for history manipulation.
 *
 * saveHistory() does not actually save and hasInQueue() always returns false.
 */
class DummyHistoryProvider implements HistoryProviderInterface
{
    /**
     * @inheritDoc
     */
    public function hasInQueue(FshareFile $fshareFile)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function saveHistory(FshareFile $fshareFile)
    {
    }
}
