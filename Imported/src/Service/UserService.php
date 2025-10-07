<?php
declare(strict_types=1);
namespace App\Service;
use App\DTO\UserDTO;
final class UserService {
    public function create(UserDTO $dto): string { return 'usr_'.uniqid(); }
}