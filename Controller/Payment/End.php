<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

class End extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session\SuccessValidator
     */
    protected $successValidator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Imagina\Placetopay\Model\Session
     */
    protected $session;

    /**
     * @var \Imagina\Placetopay\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Imagina\Placetopay\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Imagina\Placetopay\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session\SuccessValidator $successValidator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Imagina\Placetopay\Model\Session $session
     * @param \Imagina\Placetopay\Model\ClientFactory $clientFactory
     * @param \Imagina\Placetopay\Model\Order $orderHelper
     * @param \Imagina\Placetopay\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session\SuccessValidator $successValidator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Imagina\Placetopay\Model\Session $session,
        \Imagina\Placetopay\Model\ClientFactory $clientFactory,
        \Imagina\Placetopay\Model\Order $orderHelper,
        \Imagina\Placetopay\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->successValidator = $successValidator;
        $this->checkoutSession = $checkoutSession;
        $this->session = $session;
        $this->clientFactory = $clientFactory;
        $this->orderHelper = $orderHelper;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /**
         * @var $clientOrderHelper \Imagina\Placetopay\Model\Client\OrderInterface
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = '/';
        try {
            if ($this->successValidator->isValid()) {
                $redirectUrl = 'placetopay/payment/error';
                $this->session->setLastOrderId(null);
                $clientOrderHelper = $this->getClientOrderHelper();
                if ($this->orderHelper->paymentSuccessCheck() && $clientOrderHelper->paymentSuccessCheck()) {
                    $redirectUrl = 'checkout/onepage/success';
                }

            } else {
                if ($this->session->getLastOrderId()) {
                    $redirectUrl = 'placetopay/payment/repeat_error';
                    $clientOrderHelper = $this->getClientOrderHelper();
                    if ($this->orderHelper->paymentSuccessCheck() && $clientOrderHelper->paymentSuccessCheck()) {
                        $redirectUrl = 'placetopay/payment/repeat_success';
                    }
                }
            }
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
        $resultRedirect->setPath($redirectUrl);
        return $resultRedirect;
    }

    /**
     * @return \Imagina\Placetopay\Model\Client\OrderInterface
     */
    protected function getClientOrderHelper()
    {
        return $this->clientFactory->create()->getOrderHelper();
    }
}