<?php
declare(strict_types=1);

namespace Beljic\Brevo\Plugin\Email;

use Beljic\Brevo\Model\BrevoClient;
use Closure;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;

class PreventBlacklistedSender
{
    private BrevoClient $brevoClient;

    public function __construct(BrevoClient $brevoClient)
    {
        $this->brevoClient = $brevoClient;
    }

    /**
     * @param OrderSender $subject
     * @param Closure $proceed
     * @param Order $order
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSendOrder(
        OrderSender $subject,
        Closure     $proceed,
        Order       $order,
        bool        $forceSyncMode = false
    ): bool {
        return $this->shouldBlockEmail($order->getCustomerEmail(), fn() => $proceed($order, $forceSyncMode));
    }

    /**
     * @param InvoiceSender $subject
     * @param Closure $proceed
     * @param Invoice $invoice
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSendInvoice(
        InvoiceSender $subject,
        Closure       $proceed,
        Invoice       $invoice,
        bool          $forceSyncMode = false
    ): bool {
        $order = $invoice->getOrder();
        return $this->shouldBlockEmail($order->getCustomerEmail(), fn() => $proceed($invoice, $forceSyncMode));
    }

    /**
     * @param ShipmentSender $subject
     * @param Closure $proceed
     * @param Shipment $shipment
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSendShipment(
        ShipmentSender $subject,
        Closure        $proceed,
        Shipment       $shipment,
        bool           $forceSyncMode = false
    ): bool {
        $order = $shipment->getOrder();
        return $this->shouldBlockEmail($order->getCustomerEmail(), fn() => $proceed($shipment, $forceSyncMode));
    }

    /**
     * @param string $email
     * @param callable $proceed
     * @return bool
     */
    private function shouldBlockEmail(string $email, callable $proceed): bool
    {
        if (!$email || $this->brevoClient->isBlacklisted($email)) {
            return false;
        }
        return $proceed();
    }
}
