<?php

namespace Imagina\Placetopay\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

class Start extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Imagina\Placetopay\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Imagina\Placetopay\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Imagina\Placetopay\Model\Session
     */
    protected $session;

    /**
     * @var \Imagina\Placetopay\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Imagina\Placetopay\Model\ClientFactory $clientFactory
     * @param \Imagina\Placetopay\Model\Order $orderHelper
     * @param \Imagina\Placetopay\Model\Session $session
     * @param \Imagina\Placetopay\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Imagina\Placetopay\Model\ClientFactory $clientFactory,
        \Imagina\Placetopay\Model\Order $orderHelper,
        \Imagina\Placetopay\Model\Session $session,
        \Imagina\Placetopay\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->clientFactory = $clientFactory;
        $this->orderHelper = $orderHelper;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        /**
         * @var $clientOrderHelper \Imagina\Placetopay\Model\Client\OrderInterface
         * @var $resultRedirect \Magento\Framework\Controller\Result\Redirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = 'checkout/cart';
        $redirectParams = [];
        $orderId = $this->orderHelper->getOrderIdForPaymentStart();

        if ($orderId) {
            $order = $this->orderHelper->loadOrderById($orderId);
            if ($this->orderHelper->canStartFirstPayment($order)) {
                try {
                    $client = $this->clientFactory->create();

                    $clientOrderHelper = $client->getOrderHelper();
                    $orderData = $clientOrderHelper->getDataForOrderCreate($order);

                    $result = $client->orderCreate($orderData);

                    $this->orderHelper->addNewOrderTransaction(
                        $order,
                        $result['orderId'],
                        $result['extOrderId'],
                        $clientOrderHelper->getNewStatus()
                    );
                    $this->orderHelper->setNewOrderStatus($order);

                    $configHelper = $client->getConfigHelper();

                    $redirectUrl = $this->getPlacetopayReedirection($orderId,$order,$configHelper);

                } catch (LocalizedException $e) {

                    //echo "<pre>";print_r($e->getMessage());die("dead");

                    $this->logger->critical($e);
                    $redirectUrl = 'placetopay/payment/end';
                    $redirectParams = ['exception' => '1'];
                }
                $this->session->setLastOrderId($orderId);
            }
        }

        $resultRedirect->setPath($redirectUrl, $redirectParams);

        return $resultRedirect;
    }


    public function getPlacetopayReedirection($orderId,$order,$config){

        $login = $config->getConfig('login');
        $tranKey = $config->getConfig('secretKey');
        $url = $config->getConfig('url');

        $placetopay = new \Dnetix\Redirection\PlacetoPay([
            'login' => $login,
            'url' => $url,
            'tranKey' => $tranKey
        ]);
        
        /** Prepare the request */
        $returnUrl = $this->_url->getUrl('placetopay/payment/response/',array('id'=>$orderId));
        $cancelUrl = $this->_url->getUrl('/');
        $description = "Order #".$orderId." - ".$order->getCustomerEmail();
            
        $request = [
            'payment' => [
                'reference' => $orderId,
                'description' => $description,
                'amount' => [
                    'currency' => $order->getOrderCurrencyCode(),
                    'total' => number_format($order->getGrandTotal(),2,'.',''),
                ],
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' =>  $returnUrl,
            'cancelUrl' =>  $cancelUrl,
            'ipAddress' =>  $_SERVER['REMOTE_ADDR'],
            'userAgent' =>  $_SERVER['HTTP_USER_AGENT'],
        ];
        
        $response = $placetopay->request($request);

        /** Result the response */
        if ($response->isSuccessful()) {

            $requestId = $response->requestId();

            $status = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
            $order->setStatus($status);

            $order->setRequestId($requestId);
            $order->save();

            $this->session->setRequestId($requestId);
            return $response->processUrl();

        }else{

            throw new LocalizedException(new Phrase($response->status()->message()));
            //$response->status()->message();
            //print_r($response->status()->message() . "\n");
        }

        

    }// End Function

}
