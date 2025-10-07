<?php
declare(strict_types=1);

namespace App\Service\Review;

interface ReviewableInterface
{
    public function setAverageRating(int|float $rating): void;
}
