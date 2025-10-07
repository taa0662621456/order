<?php
declare(strict_types=1);

namespace App\Service\Review;

interface ReviewInterface
{
    public function getReviewSubject(): ReviewableInterface;
}
