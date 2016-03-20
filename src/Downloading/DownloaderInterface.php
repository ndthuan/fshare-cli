<?php

namespace Ndthuan\FshareCli\Downloading;

interface DownloaderInterface
{
    /**
     * @param \GearmanJob $gearmanJob Job's workload is a serialized \Ndthuan\FshareCli\Downloading\Job
     */
    public function downloadFshareFile(\GearmanJob $gearmanJob);
}
