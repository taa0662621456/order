<?php

namespace App\Service;



use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderInterface;

class AuthCodeMailer implements AuthCodeMailerInterface
{
    public function sendAuthCode(TwoFactorProviderInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();

        // TODO: Send email
        // https://symfony.com/bundles/SchebTwoFactorBundle/current/providers/email.html#installation
    }

}
