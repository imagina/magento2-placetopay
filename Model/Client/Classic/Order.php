<?php
/**
 * @copyright Copyright (c) 2017 Imagina Colombia (https://www.imaginacolombia.com)
 */

namespace Imagina\Placetopay\Model\Client\Classic;

class Order implements \Imagina\Placetopay\Model\Client\OrderInterface
{
    const STATUS_PRE_NEW            = 0;
    const STATUS_NEW                = 1;
    const STATUS_CANCELLED          = 2;
    const STATUS_REJECTED           = 3;
    const STATUS_PENDING            = 4;
    const STATUS_WAITING            = 5;
    const STATUS_REJECTED_CANCELLED = 7;
    const STATUS_COMPLETED          = 99;
    const STATUS_ERROR              = 888;

    /**
     * @var string[]
     */
    protected $statusDescription = [
        self::STATUS_PRE_NEW => 'New',
        self::STATUS_NEW => 'New',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_WAITING => 'Waiting for acceptance',
        self::STATUS_REJECTED_CANCELLED => 'Rejected',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_ERROR => 'Error'
    ];

    /**
     * @var Order\DataValidator
     */
    protected $dataValidator;

    /**
     * @var Order\DataGetter
     */
    protected $dataGetter;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Imagina\Placetopay\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Imagina\Placetopay\Logger\Logger
     */
    protected $logger;

    /**
     * @var Order\Notification
     */
    protected $notificationHelper;

    /**
     * @var MethodCaller
     */
    protected $methodCaller;

    /**
     * @var \Imagina\Placetopay\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var Order\Processor
     */
    protected $orderProcessor;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $rawResultFactory;

    /**
     * @param \Magento\Framework\View\Context $context
     * @param Order\DataValidator $dataValidator
     * @param Order\DataGetter $dataGetter
     * @param \Imagina\Placetopay\Model\Session $session
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Imagina\Placetopay\Logger\Logger $logger
     * @param Order\Notification $notificationHelper
     * @param MethodCaller $methodCaller
     * @param \Imagina\Placetopay\Model\ResourceModel\Transaction $transactionResource
     * @param Order\Processor $orderProcessor
     * @param \Magento\Framework\Controller\Result\RawFactory $rawResultFactory
     */
    public function __construct(
        \Magento\Framework\View\Context $context,
        Order\DataValidator $dataValidator,
        Order\DataGetter $dataGetter,
        \Imagina\Placetopay\Model\Session $session,
        \Magento\Framework\App\RequestInterface $request,
        \Imagina\Placetopay\Logger\Logger $logger,
        Order\Notification $notificationHelper,
        MethodCaller $methodCaller,
        \Imagina\Placetopay\Model\ResourceModel\Transaction $transactionResource,
        Order\Processor $orderProcessor,
        \Magento\Framework\Controller\Result\RawFactory $rawResultFactory
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->dataValidator = $dataValidator;
        $this->dataGetter = $dataGetter;
        $this->session = $session;
        $this->request = $request;
        $this->logger = $logger;
        $this->notificationHelper = $notificationHelper;
        $this->methodCaller = $methodCaller;
        $this->transactionResource = $transactionResource;
        $this->orderProcessor = $orderProcessor;
        $this->rawResultFactory = $rawResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function validateCreate(array $data = [])
    {
        return
            $this->dataValidator->validateEmpty($data) &&
            $this->dataValidator->validateBasicData($data);
    }

    /**
     * @inheritDoc
     */
    public function validateRetrieve($placetopayOrderId)
    {
        return $this->dataValidator->validateEmpty($placetopayOrderId);
    }

    /**
     * @inheritDoc
     */
    public function validateCancel($placetopayOrderId)
    {
        return $this->dataValidator->validateEmpty($placetopayOrderId);
    }

    /**
     * @inheritDoc
     */
    public function validateStatusUpdate(array $data = [])
    {
        // TODO: Implement validateStatusUpdate() method.
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        $this->session->setOrderCreateData($data);
        return [
            'orderId' => md5($data['referenceCode']),
            'extOrderId' => $data['referenceCode'],
            'redirectUri' => $this->urlBuilder->getUrl('placetopay/classic/form')
        ];
    }

    /**
     * @inheritDoc
     */
    public function retrieve($placetopayOrderId)
    {
        $posId = $this->dataGetter->getPosId();
        $ts = $this->dataGetter->getTs();
        $sig = $this->dataGetter->getSigForOrderRetrieve([
            'pos_id' => $posId,
            'referenceCode' => $placetopayOrderId,
            'ts' => $ts
        ]);
        $result = $this->methodCaller->call('orderRetrieve', [
            $posId,
            $placetopayOrderId,
            $ts,
            $sig
        ]);
        if ($result) {
            return [
                'status' => $result->transStatus,
                'amount' => $result->transAmount / 100
            ];
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function cancel($placetopayOrderId)
    {
        // TODO: Implement cancel() method.
    }

    /**
     * @inheritDoc
     */
    public function statusUpdate(array $data = [])
    {
        // TODO: Implement statusUpdate() method.
    }

    /**
     * @inheritDoc
     */
    public function consumeNotification(\Magento\Framework\App\Request\Http $request)
    {
        $placetopayOrderId = $this->notificationHelper->getPlacetpplOrderId($request);
        $orderData = $this->retrieve($placetopayOrderId);
        if ($orderData) {
            return [
                'placetopayOrderId' => md5($placetopayOrderId),
                'status' => $orderData['status'],
                'amount' => $orderData['amount']
            ];
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getDataForOrderCreate(\Magento\Sales\Model\Order $order)
    {
        return $this->dataGetter->getBasicData($order);
    }

    /**
     * @inheritDoc
     */
    public function addSpecialDataToOrder(array $data = [])
    {
        $data['merchantId'] = $this->dataGetter->getMerchantId();
        $data['accountId'] = $this->dataGetter->getAccountId();
        $data['signature'] = $this->dataGetter->getSigForOrderCreate($data);
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getNewStatus()
    {
        return Order::STATUS_PRE_NEW;
    }

    /**
     * @inheritDoc
     */
    public function paymentSuccessCheck()
    {
        $errorCode = $this->request->getParam('error');
        if ($errorCode) {
            $extOrderId = $this->request->getParam('referenceCode');
            $this->logger->error('Payment error ' . $errorCode . ' for transaction ' . $extOrderId . '.');
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canProcessNotification($placetopayOrderId)
    {
        return !in_array(
            $this->transactionResource->getStatusByPlacetpplOrderId($placetopayOrderId),
            [self::STATUS_COMPLETED, self::STATUS_CANCELLED]
        );
    }

    /**
     * @inheritDoc
     */
    public function processNotification($placetopayOrderId, $status, $amount)
    {
        /**
         * @var $result \Magento\Framework\Controller\Result\Raw
         */
        $newest = $this->transactionResource->checkIfNewestByPlacetpplOrderId($placetopayOrderId);
        $this->orderProcessor->processStatusChange($placetopayOrderId, $status, $amount, $newest);
        $result = $this->rawResultFactory->create();
        $result
            ->setHttpResponseCode(200)
            ->setContents('OK');
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getStatusDescription($status)
    {
        if (isset($this->statusDescription[$status])) {
            return (string) __($this->statusDescription[$status]);
        }
        return false;
    }
}
