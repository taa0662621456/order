<?php
declare(strict_types=1);
namespace App\Service;
use App\DTO\WishlistItemDTO;
use App\DTO\WishlistDTO;
final class WishlistService {
    public function addItem(WishlistDTO $dto, WishlistItemDTO $item): string {
        return 'wish_'.uniqid();
    }
    public function removeItem(string $wishlistId, string $productId): bool {
        return true;
    }
}
