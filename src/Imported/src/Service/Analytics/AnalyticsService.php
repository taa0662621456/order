<?php
declare(strict_types=1);
namespace App\Service\Analytics;
use App\DTO\AbTestDTO;
use App\DTO\EventDTO;
final class AnalyticsService {
    public function track(EventDTO $dto): string { return 'evt_'.uniqid(); }
    public function createAbTest(AbTestDTO $dto): string { return 'ab_'.uniqid(); }
}
