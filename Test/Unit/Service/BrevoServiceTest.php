<?php
declare(strict_types=1);

namespace Beljic\Brevo\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use Beljic\Brevo\Service\BrevoService;
use Beljic\Brevo\Api\BrevoClientInterface;

/**
 * Test class for BrevoService.
 *
 * This class tests the BrevoService to ensure it correctly delegates the
 * isBlacklisted method to the BrevoClientInterface.
 */
class BrevoServiceTest extends TestCase
{
    /**
     * Test that the isBlacklisted method calls the client method with the correct parameters.
     *
     * This test ensures that the BrevoService correctly delegates the call to
     * the BrevoClientInterface's isBlacklisted method.
     */
    public function testIsBlacklistedDelegatesToClient(): void
    {
        $email = 'test@example.com';

        $clientMock = $this->createMock(BrevoClientInterface::class);
        $clientMock->expects($this->once())
            ->method('isBlacklisted')
            ->with($email)
            ->willReturn(true);

        $service = new BrevoService($clientMock);

        $this->assertTrue($service->isBlacklisted($email));
    }
}
