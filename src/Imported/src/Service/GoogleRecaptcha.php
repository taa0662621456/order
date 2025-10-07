<?php
declare(strict_types=1);

namespace App\Service;

use ReCaptcha\Response;
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod;
use Symfony\Component\HttpFoundation\Request;

final class GoogleRecaptcha
{
    public function recaptchaResponse(Request $request, string $secret, RequestMethod $requestMethod): Response
    {
        $recaptcha = new ReCaptcha($secret, $requestMethod);
        return $recaptcha->verify(
            (string) $request->request->get('g-recaptcha-response'),
            (string) $request->getClientIp()
        );
    }
}
