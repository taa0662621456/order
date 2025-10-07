<?php
declare(strict_types=1);

namespace App\Command;

use App\Interface\CouponRepositoryInterface;
use App\Interface\CouponGeneratorInterface;
use App\Interface\PromotionInterface;
use App\Repository\Promotion\PromotionRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CouponGenerateCommand extends AbstractDomainCommand
{
    protected static $defaultName = 'app:coupon:generate';

    public function __construct(
        private PromotionRepository $promotions,
        private CouponGeneratorInterface $generator,
        private CouponRepositoryInterface $coupons
    ) { parent::__construct(); }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate coupon codes for a promotion')
            ->addStandardOptions()
            ->addArgument('promotion', InputArgument::REQUIRED, 'Promotion code or ID')
            ->addOption('amount', 'a', InputOption::VALUE_REQUIRED, 'How many codes to generate', 50)
            ->addOption('length', 'l', InputOption::VALUE_REQUIRED, 'Code length', 12)
            ->addOption('preview', null, InputOption::VALUE_NONE, 'Preview generated codes without persisting');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->io($input, $output);
        [$ok, $release] = $this->acquireLock('coupon_generate');
        if (!$ok) { $io->warning('Another run in progress'); return self::SUCCESS; }

        $promotionKey = (string)$input->getArgument('promotion');
        $amount = (int)$input->getOption('amount');
        $length = (int)$input->getOption('length');
        $preview = (bool)$input->getOption('preview');

        /** @var PromotionInterface|null $promotion */
        $promotion = is_numeric($promotionKey)
            ? $this->promotions->find((int)$promotionKey)
            : $this->promotions->findOneBy(['code' => $promotionKey]);

        if (!$promotion) {
            $io->warning('Promotion not found: '.$promotionKey);
            if ($release) { $release(); }
            return self::SUCCESS;
        }

        $instruction = $this->generator->getGeneratorInstructions($amount, $length);
        $codes = $this->generator->generate($promotion, $instruction);

        $stored = 0;
        if (!$preview && !$this->isDryRun()) {
            $stored = $this->coupons->store($promotion, $codes);
        }

        $io->successBox('Coupons generated', [
            'promotion: '.$promotionKey,
            'generated: '.count($codes),
            'stored: '.$stored,
            $preview || $this->isDryRun() ? 'DRY-RUN/preview (not persisted)' : 'persisted: yes',
        ]);

        if ($release) { $release(); }
        return self::SUCCESS;
    }
}
