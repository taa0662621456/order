<?php
declare(strict_types=1);

namespace App\Service\Security;
use App\Entity\Vendor\Vendor;

use App\Entity\Vendor\VendorSecurity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;

final class SecurityTwoFactorManager
{
    public function __construct(
        private readonly TotpAuthenticatorInterface $totpAuthenticator,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger
    ) {}

    public function enable(VendorSecurity $user): string
    {
        $secret = $this->totpAuthenticator->generateSecret();
        $user->setTotpSecret($secret);
        $this->em->flush();
        $this->logger->info('2FA enabled', ['user' => $user->getUserIdentifier()]);
        return $secret;
    }

    public function verifyCode(VendorSecurity $user, string $code): bool
    {
        return $this->totpAuthenticator->checkCode($user, $code);
    }

    public function disable(VendorSecurity $user): void
    {
        $user->setTotpSecret(null);
        $this->em->flush();
        $this->logger->info('2FA disabled', ['user' => $user->getUserIdentifier()]);
    }

    public function isEnabled(VendorSecurity $user): bool
    {
        return $user->isTotpAuthenticationEnabled();
    }
}
