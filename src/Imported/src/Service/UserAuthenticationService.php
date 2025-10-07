<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;

final class UserAuthenticationService
{
    /** @var array<string,int> */
    private array $tokenStorage = [];

    public function __construct(private readonly LoggerInterface $logger) {}

    public function authenticate(string $username, string $password): ?User
    {
        // В реальной жизни тут Doctrine+PasswordHasher. Пока заглушка.
        if ($username === 'admin' && $password === 'secret') {
            $user = new User();
            $user->setUsername($username);
            return $user;
        }
        $this->logger->warning('Authentication failed', ['username' => $username]);
        return null;
    }

    public function generateToken(User $user): string
    {
        $token = bin2hex(random_bytes(16));
        $this->tokenStorage[$token] = $user->getId();
        return $token;
    }

    public function validateToken(string $token): bool
    {
        return isset($this->tokenStorage[$token]);
    }
}
