<?php
declare(strict_types=1);
namespace App\Service\Review;
use App\DTO\ReviewDTO;
final class ReviewService {
    public function submit(ReviewDTO $dto): string { return 'rev_'.uniqid(); }
}