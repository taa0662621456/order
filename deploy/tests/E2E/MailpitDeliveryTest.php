<?php
declare(strict_types=1);
namespace App\Tests\E2E;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MailpitDeliveryTest extends WebTestCase
{
    public function testEmailDeliveryViaMailpit(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/test-email', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'to' => 'test@example.com',
            'subject' => 'Mailpit E2E Test',
            'message' => 'Hello from Order!'
        ]));
        $this->assertResponseIsSuccessful();

        $json = @file_get_contents('http://mailpit:8025/api/v1/messages');
        $data = json_decode($json ?: "{}", true) ?: [];
        $found = false;
        foreach (($data['messages'] ?? []) as $msg) {
            if (($msg['Subject'] ?? '') === 'Mailpit E2E Test') { $found = true; break; }
        }
        $this->assertTrue($found, 'Test email not found in Mailpit inbox');
    }
}
