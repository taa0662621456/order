<?php
declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegistrationService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly LoggerInterface $logger
    ) {}

    public function register(string $email, string $plainPassword): User
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address.');
        }
        if (strlen($plainPassword) < 6) {
            throw new \InvalidArgumentException('Password too short.');
        }

        $user = new User();
        $user->setEmail($email);
        $hash = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($hash);

        $this->em->persist($user);
        $this->em->flush();

        $this->logger->info('User registered', ['email' => $email]);
        return $user;
    }
}
