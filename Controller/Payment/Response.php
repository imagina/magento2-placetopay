<?php

namespace Imagina\Placetopay\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

use Magento\Framework\App\Action\Action;

class Response extends Action
{

    protected $context;
    protected $clientFactory;
    protected $orderHelper;
    protected $session;
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Imagina\Placetopay\Model\ClientFactory $clientFactory,
        \Imagina\Placetopay\Model\Order $orderHelper,
        \Imagina\Placetopay\Model\Session $session,
        \Imagina\Placetopay\Logger\Logger $logger
    ) {

        parent::__construct($context);
        $this->context = $context;
        $this->clientFactory = $clientFactory;
        $this->orderHelper = $orderHelper;
        $this->session = $session;
        $this->logger = $logger;
    }

    public function execute()
    {

        $request = $this->context->getRequest();

        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = '/';
        $redirectParams = [];
        
        try {
            
            $orderId = $request->getParam('id');
            //$orderId = $this->session->getLastOrderId();
            
            //$orderId = 54; //==============Testing***
        
            $this->logger->info("Response OrderID: ".$orderId);

            $order = $this->orderHelper->loadOrderById($orderId);

            $client = $this->clientFactory->create();
            $clientOrderHelper = $client->getOrderHelper();
            $config = $client->getConfigHelper();

            //$requestId = $this->session->getRequestId();
            $requestId = $order->getData('request_id');

            //$requestId = 18598; //==============Testing***
        
            $statusCode = $this->getPlacetopayTransaction($orderId,$order,$config,$requestId,$clientOrderHelper);

            $redirectUrl = 'checkout/onepage/success';

        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            $redirectUrl = 'placetopay/payment/end';
            $redirectParams = ['exception' => '1'];
        }
        
        //print_r("Fin testing"); //==============Testing***
        //exit();

        if(isset($statusCode)){

            if($statusCode==0){
                $redirectUrl = 'checkout/onepage/success';
            }else{
                $redirectUrl = 'checkout/onepage/failure';
                if($statusCode==1){
                    $msj = "Status de la transaccion: Rechazada";
                    $this->messageManager->addError(__($msj)); 
                }
                if($statusCode==2){
                    $msj = "Status de la transaccion: Pendiente";
                    $this->messageManager->addWarning(__($msj));
                }
                if($statusCode==3){
                    $msj = "Status de la transaccion: Fallida";
                    $this->messageManager->addError(__($msj));
                }
                if($statusCode==4){
                    $msj = "Status de la transaccion: Error";
                    $this->messageManager->addError(__($msj));
                }
            } 
           
        }// End Status code
       
        $resultRedirect->setPath($redirectUrl);
        return $resultRedirect;

    }

    
    public function getPlacetopayTransaction($orderId,$order,$config,$requestId,$client){

        $login = $config->getConfig('login');
        $tranKey = $config->getConfig('secretKey');
        $url = $config->getConfig('url');

        $placetopay = new \Dnetix\Redirection\PlacetoPay([
            'login' => $login,
            'url' => $url,
            'tranKey' => $tranKey
        ]);

        $response = $placetopay->query($requestId);

        //$sig = sha1($response->requestId().$response->status()->status().$response->status()->date().$tranKey);
        
        /** Check the Response */
        if ($response->isSuccessful()) {

            //echo "<pre>";print_r($response);die("dead");//==============Testing***
          
            if ($response->status()->isApproved()) {
                $statusPTP = "APPROVED";
                $statusCode = 0;
                $status = \Magento\Sales\Model\Order::STATE_PROCESSING;

                /*
                if ($client->canProcessNotification($orderId)) {
                    
                }
                */
            }else{

                if ($response->status()->isRejected()) {
                    $statusPTP = "REJECTED";
                    $statusCode = 1;
                    $status = \Magento\Sales\Model\Order::STATE_CANCELED;
                }else{
                    $statusPTP = "PENDING";
                    $statusCode = 2;
                    $status = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
                }

            }

            /*
            if ($response->status()->isPending()) {
                $statusPTP = "PENDING";
                $statusCode = 2;
                $status = \Magento\Sales\Model\Order::STATE_PENDING;
            }
            */
            /*
            if ($response->status()->isFailed()) {
                $statusPTP = "FAILED";
                $statusCode = 3;
                $status = \Magento\Sales\Model\Order::STATE_CANCELED;
            }
            */

            $order->setStatus($status);
            $order->setState($status);
            $order->addStatusHistoryComment(__('Placetopay status') . ': ' . $statusPTP);
            $order->save();

            $this->session->setLastOrderId(null);
            $this->session->setRequestId(null);
            
            $msjLog = "OrderID: ".$orderId." - StatusPTP: ".$statusPTP;
            $this->logger->info($msjLog);

        } else {
            
            $statusPTP = "FALLIDA";
            $statusCode = 3;
            $status = \Magento\Sales\Model\Order::STATE_CANCELED;

            $order->setStatus($status);
            $order->setState($status);
            $order->addStatusHistoryComment(__('Placetopay status') . ': ' . $statusPTP);
            $order->save();

            //print_r($response->status()->message() . "\n");
            throw new LocalizedException(new Phrase($response->status()->message()));

        }

        return $statusCode;
       

    }// End Function

    
   
    
}
