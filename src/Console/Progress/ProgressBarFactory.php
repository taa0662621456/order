<?php
declare(strict_types=1);

namespace App\Console\Progress;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final class ProgressBarFactory
{
    public function create(OutputInterface $output, int $length = 10): ProgressBar
    {
        $progress = new ProgressBar($output);
        $progress->setBarCharacter('<info>░</info>');
        $progress->setEmptyBarCharacter(' ');
        $progress->setProgressCharacter('<comment>░</comment>');
        $progress->start($length);
        return $progress;
    }
}
