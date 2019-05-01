<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Controller\Payment;

class Repeat extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Imagina\Placetopay\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * @var \Imagina\Placetopay\Model\Session
     */
    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Imagina\Placetopay\Helper\Payment $paymentHelper,
        \Imagina\Placetopay\Model\Session $session
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->paymentHelper = $paymentHelper;
        $this->session = $session;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $placetopayOrderId = $this->context->getRequest()->getParam('id');
        $orderId = $this->paymentHelper->getOrderIdIfCanRepeat($placetopayOrderId);
        if ($orderId) {
            $resultRedirect->setPath('orba_payupl/payment/repeat_start');
            $this->session->setLastOrderId($orderId);
        } else {
            $resultRedirect->setPath('orba_payupl/payment/repeat_error');
            $this->messageManager->addError(__('The repeat payment link is invalid.'));
        }
        return $resultRedirect;
    }
}
