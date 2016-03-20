<?php

namespace Ndthuan\FshareCli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * WorkerCommand class.
 */
class WorkerCommand extends Command
{
    /**
     * @var \GearmanWorker
     */
    private $gearmanWorker;

    /**
     * WorkerCommand constructor.
     *
     * @param \GearmanWorker $gearmanWorker
     */
    public function __construct(\GearmanWorker $gearmanWorker)
    {
        $this->gearmanWorker = $gearmanWorker;

        parent::__construct('worker');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while ($this->gearmanWorker->work()) {
            if ($this->gearmanWorker->returnCode() != GEARMAN_SUCCESS) {
                return 1;
            }
        }

        return 0;
    }
}
