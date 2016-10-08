<?php

namespace Ndthuan\FshareCli\Command;

use Ndthuan\FshareCli\Downloading\History\HistoryProviderInterface;
use Ndthuan\FshareCli\Downloading\Job;
use Ndthuan\FshareLib\Api\DTO\FshareFile;
use Ndthuan\FshareLib\Api\DTO\FshareFolder;
use Ndthuan\FshareLib\Api\FshareClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommand extends Command
{
    /**
     * @var \GearmanClient
     */
    private $gearmanClient;

    /**
     * @var FshareClientInterface
     */
    private $fshareClient;

    /**
     * @var HistoryProviderInterface
     */
    private $historyProvider;

    /**
     * DownloadCommand constructor.
     *
     * @param \GearmanClient $gearmanClient
     * @param FshareClientInterface $fshareClient
     * @param HistoryProviderInterface $historyProvider
     */
    public function __construct(
        \GearmanClient $gearmanClient,
        FshareClientInterface $fshareClient,
        HistoryProviderInterface $historyProvider
    ) {
        $this->gearmanClient = $gearmanClient;
        $this->fshareClient = $fshareClient;
        $this->historyProvider = $historyProvider;

        parent::__construct('download');
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'The FShare URL (folder for file)')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Directory to save files');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $dir = $input->getArgument('dir');

        $fshareInfo = $this->fshareClient->fetchFolderOrFileInfo($url);

        if ($fshareInfo instanceof FshareFolder) {
            foreach ($fshareInfo->getFiles() as $file) {
                $this->submitJob($file, $dir);
            }
        } else {
            $this->submitJob($fshareInfo, $dir);
        }

        $output->writeln("Submitted download job for: $url");
    }

    /**
     * @param FshareFile $file
     * @param string $dir
     */
    private function submitJob(FshareFile $file, $dir)
    {
        if (!$this->historyProvider->hasInQueue($file)) {
            $this->gearmanClient->addTaskBackground(
                'downloadFshareFile',
                serialize(new Job($file, $dir))
            );
            $this->gearmanClient->runTasks();

            $this->historyProvider->saveHistory($file);
        }
    }
}
