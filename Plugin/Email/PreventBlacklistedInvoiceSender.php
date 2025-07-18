<?php
/** @noinspection PhpCSValidationInspection */
declare(strict_types=1);

namespace Beljic\Brevo\Plugin\Email;

use Beljic\Brevo\Api\BrevoServiceInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Psr\Log\LoggerInterface;

/**
 * Plugin to prevent sending invoice emails to blacklisted addresses in Brevo.
 */
class PreventBlacklistedInvoiceSender
{
    /**
     * @param BrevoServiceInterface $brevoApi
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly BrevoServiceInterface  $brevoApi,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Around plugin for the send() method of InvoiceSender
     *
     * @param InvoiceSender $subject
     * @param callable $proceed
     * @param Invoice $invoice
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSend(
        InvoiceSender $subject,
        callable $proceed,
        Invoice $invoice,
        bool $forceSyncMode = false
    ): bool
    {
        $email = $invoice->getOrder()?->getCustomerEmail();
        if ($email && $this->brevoApi->isBlacklisted($email)) {
            $this->logger->debug('Invoice email send blocked for: ' . $email);
            return false;
        }
        return $proceed($invoice, $forceSyncMode);
    }
}
