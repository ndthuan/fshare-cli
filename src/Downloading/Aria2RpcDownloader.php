<?php

namespace Ndthuan\FshareCli\Downloading;

use Ndthuan\Aria2RpcAdapter\Adapter;
use Ndthuan\FshareLib\Api\DTO\FshareFile;
use Ndthuan\FshareLib\Api\FshareClientInterface;
use Ndthuan\FshareLib\FshareException;
use Ndthuan\FshareLib\HtmlClient\DownloadUrlNotFoundException;

class Aria2RpcDownloader implements DownloaderInterface
{
    /**
     * @var Adapter
     */
    private $aria2Adapter;

    /**
     * @var FshareClientInterface
     */
    private $fshareClient;

    /**
     * @var int
     */
    private $maxTries;

    /**
     * Aria2RpcDownloader constructor.
     *
     * @param Adapter $aria2Adapter
     * @param FshareClientInterface $fshareClient
     * @param int $maxTries
     */
    public function __construct(Adapter $aria2Adapter, FshareClientInterface $fshareClient, $maxTries = 10)
    {
        $this->aria2Adapter = $aria2Adapter;
        $this->fshareClient = $fshareClient;
        $this->maxTries = $maxTries;
    }

    /**
     * @inheritDoc
     */
    public function downloadFshareFile(\GearmanJob $gearmanJob)
    {
        $this->doDownload(unserialize($gearmanJob->workload()));
    }

    /**
     * @param Job $job
     *
     * @throws DownloadUrlNotFoundException
     * @throws \Exception
     */
    private function doDownload(Job $job)
    {
        $downloadUrl = $this->getDownloadUrl($job->getFshareFile());

        if ($downloadUrl) {
            $gid = $this->aria2Adapter->addUri([$downloadUrl], ['dir' => $job->getDirectory()]);

            $this->waitForDownload($gid);
        }
    }

    /**
     * @param string $gid Aria2 RPC GID
     */
    private function waitForDownload($gid)
    {
        do {
            sleep(1);

            $status = $this->aria2Adapter->tellStatus($gid, ['gid', 'status']);
        } while (!in_array($status['status'], ['error', 'complete', 'removed']));
    }

    /**
     * @param FshareFile $file
     *
     * @return null|string
     *
     * @throws DownloadUrlNotFoundException
     * @throws \Exception
     */
    private function getDownloadUrl(FshareFile $file)
    {
        $retries = 0;
        $downloadUrl = null;

        while (true) {
            try {
                $downloadableUrls = $this->fshareClient->fetchDownloadableUrls($file->getUrl());
                if (is_array($downloadableUrls) && isset($downloadableUrls[0])) {
                    $downloadUrl = $downloadableUrls[0]->getUrl();
                    break;
                }
            } catch (\Exception $ex) {
                if (!$ex instanceof FshareException || $retries++ >= $this->maxTries) {
                    throw $ex;
                }
            }
        };

        return $downloadUrl;
    }
}
