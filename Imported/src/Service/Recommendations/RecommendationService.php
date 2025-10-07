<?php
declare(strict_types=1);
namespace App\Service\Recommendations;
use App\DTO\RecommendationContextDTO;
final class RecommendationService {
    public function getPersonalized(RecommendationContextDTO $ctx): array {
        // stub personalized list
        return ['items'=>[]];
    }
    public function getSimilar(array $productIds): array {
        return ['items'=>[]];
    }
}