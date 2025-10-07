<?php
declare(strict_types=1);

namespace App\Console\Traits;

use App\Console\Progress\ProgressBarFactory;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

trait CreatesProgressBarTrait
{
    private ProgressBarFactory $progressBarFactory;

    public function setProgressBarFactory(ProgressBarFactory $factory): void
    {
        $this->progressBarFactory = $factory;
    }

    protected function createProgressBar(OutputInterface $output, int $length = 10): ProgressBar
    {
        return $this->progressBarFactory->create($output, $length);
    }
}
