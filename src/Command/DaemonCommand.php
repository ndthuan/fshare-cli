<?php

namespace Ndthuan\FshareCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DaemonCommand extends Command
{
    /**
     * Path to the executable command of this application. E.g. ./bin/fshare
     * @var string
     */
    private $appExecutable;

    /**
     * @var Process[]
     */
    private $workers;

    /**
     * @var int
     */
    private $numberOfWorkers;

    /**
     * DaemonCommand constructor.
     *
     * @param string $appExecutable
     * @param int $numberOfWorkers
     */
    public function __construct($appExecutable, $numberOfWorkers)
    {
        $this->appExecutable = $appExecutable;
        $this->numberOfWorkers = $numberOfWorkers;
        $this->workers = [];

        parent::__construct('daemon');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            for ($workerIndex = 1; $workerIndex <= $this->numberOfWorkers; $workerIndex++) {
                if (!isset($this->workers[$workerIndex]) || $this->workers[$workerIndex]->isTerminated()) {
                    $this->workers[$workerIndex] = $this->launchWorker();
                }
            }

            sleep(1);
        }
    }

    /**
     * @return Process
     */
    private function launchWorker()
    {
        $command = sprintf('%s worker', $this->appExecutable);

        $process = new Process($command);
        $process->start();

        return $process;
    }
}
