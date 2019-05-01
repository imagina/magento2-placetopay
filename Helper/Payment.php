<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Payment extends AbstractHelper
{
    /**
     * @var \Imagina\Placetopay\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var \Imagina\Placetopay\Model\Order
     */
    protected $orderHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Imagina\Placetopay\Model\ResourceModel\Transaction $transactionResource
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Imagina\Placetopay\Model\ResourceModel\Transaction $transactionResource,
        \Imagina\Placetopay\Model\Order $orderHelper
    ) {
        parent::__construct($context);
        $this->transactionResource = $transactionResource;
        $this->orderHelper = $orderHelper;
    }

    /**
     * @param int $orderId
     * @return string|false
     */
    public function getStartPaymentUrl($orderId)
    {
        $order = $this->orderHelper->loadOrderById($orderId);
        if ($order && $this->orderHelper->canStartFirstPayment($order)) {
            return $this->_urlBuilder->getUrl('placetopay/payment/start', ['id' => $orderId]);
        }
        return false;
    }

    /**
     * @param int $orderId
     * @return string|false
     */
    public function getRepeatPaymentUrl($orderId)
    {
        $order = $this->orderHelper->loadOrderById($orderId);
        if ($order && $this->orderHelper->canRepeatPayment($order)) {
            return $this->_urlBuilder->getUrl(
                'placetopay/payment/repeat',
                ['id' => $this->transactionResource->getLastPlacetpplOrderIdByOrderId($orderId)]
            );
        }
        return false;
    }

    /**
     * @param string $placetopayOrderId
     * @return bool
     */
    public function getOrderIdIfCanRepeat($placetopayOrderId = null)
    {
        if ($placetopayOrderId && $this->transactionResource->checkIfNewestByPlacetpplOrderId($placetopayOrderId)) {
            return $this->transactionResource->getOrderIdByPlacetpplOrderId($placetopayOrderId);
        }
        return false;
    }
}
