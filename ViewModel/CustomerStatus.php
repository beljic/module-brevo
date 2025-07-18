<?php
/** @noinspection PhpCSValidationInspection */
declare(strict_types=1);

namespace Beljic\Brevo\ViewModel;

use Beljic\Brevo\Api\BrevoServiceInterface;
use Beljic\Brevo\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomerStatus implements ArgumentInterface
{
    /**
     * @param Session $customerSession
     * @param BrevoServiceInterface $brevoApi
     * @param Data $helper
     */
    public function __construct(
        private readonly Session     $customerSession,
        private readonly BrevoServiceInterface $brevoApi,
        private readonly Data $helper
    ) {
    }

    /**
     * Check if the customer is logged in.
     *
     * @return boolean
     */
    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Check if the customer is blacklisted in Brevo.
     *
     * @return boolean
     */
    public function isBlacklisted(): bool|null
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->brevoApi->isBlacklisted($this->customerSession?->getCustomerData()?->getEmail() ?? '');
    }

    /**
     * Check if the Brevo API integration is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->helper->isEnabled();
    }
}
