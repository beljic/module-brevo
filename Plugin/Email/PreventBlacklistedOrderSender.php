<?php
/** @noinspection PhpCSValidationInspection */
declare(strict_types=1);

namespace Beljic\Brevo\Plugin\Email;

use Beljic\Brevo\Api\BrevoServiceInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class PreventBlacklistedOrderSender
{
    /**
     * Constructor for the PreventBlacklistedOrderSender plugin
     *
     * @param BrevoServiceInterface $brevoApi
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly BrevoServiceInterface $brevoApi,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Around plugin for the send() method of OrderSender
     *
     * @param OrderSender $subject
     * @param callable $proceed
     * @param Order $order
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSend(OrderSender $subject, callable $proceed, Order $order, bool $forceSyncMode = false): bool
    {
        $email = $order->getCustomerEmail();
        if ($email && $this->brevoApi->isBlacklisted($email)) {
            $this->logger->debug('Order email send blocked for: ' . $email);
            return false;
        }

        return $proceed($order, $forceSyncMode);
    }
}
