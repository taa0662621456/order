<?php
declare(strict_types=1);

namespace App\Service\Review;

interface ReviewableRatingUpdaterInterface
{
    public function update(ReviewableInterface $reviewSubject): void;
    public function updateFromReview(ReviewInterface $review): void;
}
