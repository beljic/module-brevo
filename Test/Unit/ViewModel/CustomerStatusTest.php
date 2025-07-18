<?php
declare(strict_types=1);

namespace Beljic\Brevo\Test\Unit\ViewModel;

use PHPUnit\Framework\TestCase;
use Beljic\Brevo\ViewModel\CustomerStatus;
use Beljic\Brevo\Api\BrevoServiceInterface;
use Beljic\Brevo\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Data\Customer as CustomerData;

class CustomerStatusTest extends TestCase
{
    /**
     * @var Session
     */
    private Session $sessionMock;
    /**
     * @var BrevoServiceInterface
     */
    private BrevoServiceInterface $brevoServiceMock;
    /**
     * @var Data
     */
    private Data $helperMock;

    /**
     * Set up the test environment with mocks.
     */
    protected function setUp(): void
    {
        $this->sessionMock = $this->createMock(Session::class);
        $this->brevoServiceMock = $this->createMock(BrevoServiceInterface::class);
        $this->helperMock = $this->createMock(Data::class);
    }

    public function testIsLoggedInReturnsSessionValue(): void
    {
        $this->sessionMock->method('isLoggedIn')->willReturn(true);
        $vm = new CustomerStatus($this->sessionMock, $this->brevoServiceMock, $this->helperMock);
        $this->assertTrue($vm->isLoggedIn());
    }

    public function testIsBlacklistedReturnsFalseIfNotLoggedIn(): void
    {
        $this->sessionMock->method('isLoggedIn')->willReturn(false);
        $vm = new CustomerStatus($this->sessionMock, $this->brevoServiceMock, $this->helperMock);
        $this->assertFalse($vm->isBlacklisted());
    }

    public function testIsBlacklistedReturnsServiceResultWhenLoggedIn(): void
    {
        $email = 'customer@example.com';

        $customerDataMock = $this->createMock(CustomerData::class);
        $customerDataMock->method('getEmail')->willReturn($email);

        $this->sessionMock->method('isLoggedIn')->willReturn(true);
        $this->sessionMock->method('getCustomerData')->willReturn($customerDataMock);

        $this->brevoServiceMock->expects($this->once())
            ->method('isBlacklisted')
            ->with($email)
            ->willReturn(true);

        $vm = new CustomerStatus($this->sessionMock, $this->brevoServiceMock, $this->helperMock);

        $this->assertTrue($vm->isBlacklisted());
    }

    public function testIsEnabledReturnsHelperValue(): void
    {
        $this->helperMock->method('isEnabled')->willReturn(true);
        $vm = new CustomerStatus($this->sessionMock, $this->brevoServiceMock, $this->helperMock);
        $this->assertTrue($vm->isEnabled());
    }
}
