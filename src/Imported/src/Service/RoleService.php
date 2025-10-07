<?php
declare(strict_types=1);
namespace App\Service;
use App\DTO\RoleDTO;
final class RoleService {
    public function create(RoleDTO $dto): string { return 'role_'.uniqid(); }
}