<?php
/** @noinspection PhpCSValidationInspection */
declare(strict_types=1);

namespace Beljic\Brevo\Plugin\Email;

use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Sender\EmailSender;
use Beljic\Brevo\Api\BrevoServiceInterface;
use Psr\Log\LoggerInterface;

class PreventBlacklistedShipmentSender
{
    /**
     * Constructor for the PreventBlacklistedShipmentSender plugin
     *
     * @param BrevoServiceInterface $brevoApi
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly BrevoServiceInterface  $brevoApi,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Around plugin for the send () method of EmailSender shipment
     *
     * @param EmailSender $subject
     * @param callable $proceed
     * @param Shipment $shipment
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSend(
        EmailSender $subject,
        callable $proceed,
        Shipment $shipment,
        bool $forceSyncMode = false
    ): bool {
        $email = $shipment->getOrder()?->getCustomerEmail();

        if ($email && $this->brevoApi->isBlacklisted($email)) {
            $this->logger->debug('Shipment email send blocked for: ' . $email);
            return false;
        }

        return $proceed($shipment, $forceSyncMode);
    }
}
