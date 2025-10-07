<?php

namespace App\Command;

 readonly class SendResetPasswordEmailCommand
{
    public function __construct(
        public string $email,
        public string $channelCode,
        public string $locale,
    ) {}
}
