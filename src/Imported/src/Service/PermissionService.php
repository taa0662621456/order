<?php
declare(strict_types=1);
namespace App\Service;
use App\DTO\PermissionDTO;
final class PermissionService {
    public function create(PermissionDTO $dto): string { return 'perm_'.uniqid(); }
}