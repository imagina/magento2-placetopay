<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Block\Payment\Repeat;

class Fail extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Imagina\Placetopay\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * @var \Imagina\Placetopay\Model\Session
     */
    protected $session;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Imagina\Placetopay\Model\Session $session,
        \Imagina\Placetopay\Helper\Payment $paymentHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->session = $session;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @return string|false
     */
    public function getPaymentUrl()
    {
        $orderId = $this->session->getLastOrderId();
        if ($orderId) {
            $repeatPaymentUrl = $this->paymentHelper->getRepeatPaymentUrl($orderId);
            if (!$repeatPaymentUrl) {
                return $this->paymentHelper->getStartPaymentUrl($orderId);
            }
            return $repeatPaymentUrl;
        }
        return false;
    }
}
