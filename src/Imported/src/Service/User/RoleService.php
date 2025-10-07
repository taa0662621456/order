<?php
declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class RoleService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function assignRole(User $user, string $role): void
    {
        $roles = $user->getRoles();
        if (!in_array($role, $roles, true)) {
            $roles[] = $role;
            $user->setRoles($roles);
            $this->em->flush();
        }
    }

    public function revokeRole(User $user, string $role): void
    {
        $roles = array_filter($user->getRoles(), fn($r) => $r !== $role);
        $user->setRoles($roles);
        $this->em->flush();
    }

    public function hasRole(User $user, string $role): bool
    {
        return in_array($role, $user->getRoles(), true);
    }
}
