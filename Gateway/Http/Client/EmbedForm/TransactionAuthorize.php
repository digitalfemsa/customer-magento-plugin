<?php

namespace DigitalFemsa\Payments\Gateway\Http\Client\EmbedForm;

use DigitalFemsa\Model\ChargeResponse;
use DigitalFemsa\Payments\Api\FemsaApiClient;
use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as FemsaLogger;
use DigitalFemsa\Payments\Api\Data\FemsaSalesOrderInterface;
use DigitalFemsa\Payments\Model\FemsaSalesOrderFactory;
use DigitalFemsa\Payments\Model\Ui\EmbedForm\ConfigProvider;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Exception;

class TransactionAuthorize implements ClientInterface
{
    const SUCCESS = 1;
    const FAILURE = 0;

    /**
     * @var array
     */
    private $results = [
        self::SUCCESS,
        self::FAILURE
    ];

    /**
     * @var Logger
     */
    private $logger;

    protected $_femsaHelper;

    private $_femsaLogger;

    protected $femsaSalesOrderFactory;

    /**
     * @var FemsaApiClient
     */
    private $femsaApiClient;

    /**
     * @var ChargeResponse
     */
    private $charge;

    /**
     * @param Logger $logger
     * @param FemsaHelper $femsaHelper
     * @param FemsaLogger $FemsaLogger
     * @param FemsaApiClient $femsaApiClient
     * @param FemsaSalesOrderFactory $femsaSalesOrderFactory
     */
    public function __construct(
        Logger                   $logger,
        FemsaHelper              $femsaHelper,
        FemsaLogger              $FemsaLogger,
        FemsaApiClient           $femsaApiClient,
        FemsaSalesOrderFactory   $femsaSalesOrderFactory
    )
    {
        $this->_femsaHelper = $femsaHelper;
        $this->_femsaLogger = $FemsaLogger;
        $this->_femsaLogger->info('HTTP Client TransactionCapture :: __construct');
        $this->logger = $logger;
        $this->femsaSalesOrderFactory = $femsaSalesOrderFactory;
        $this->femsaApiClient = $femsaApiClient;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws Exception
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        $request = $transferObject->getBody();
        $this->_femsaLogger->info('HTTP Client TransactionCapture :: placeRequest', $request);

        $txnId = $request['txn_id'];

        $this->femsaSalesOrderFactory
            ->create()
            ->setData([
                FemsaSalesOrderInterface::FEMSA_ORDER_ID => $request['order_id'],
                FemsaSalesOrderInterface::INCREMENT_ORDER_ID => $request['metadata']['order_id']
            ])
            ->save();

        $paymentMethod = $request['payment_method_details']['payment_method']['type'];
        $response = [];
        
        //If is offline payment, added extra info needed
        if ($paymentMethod == ConfigProvider::PAYMENT_METHOD_CASH) {
            $response['offline_info'] = [];
            try {
                $femsaOrder = $this->femsaApiClient->getOrderByID($request['order_id']);
                $charge = $femsaOrder->getCharges()->getData()[0];

                $txnId = $charge->getID();
                $paymentMethodResponse = $charge->getPaymentMethod();
                $response['offline_info'] = [
                    "type" => $paymentMethodResponse->getType(),
                    "data" => [
                        "expires_at" => $paymentMethodResponse->getExpiresAt()
                    ]
                ];

                    $response['offline_info']['data']['barcode_url'] = $paymentMethodResponse->getBarcodeUrl();
                    $response['offline_info']['data']['reference'] = $paymentMethodResponse->getReference();

            } catch (Exception $e) {
                $this->_femsaLogger->error(
                    'EmbedForm :: HTTP Client TransactionCapture :: cannot get offline info. ',
                    ['exception' => $e]
                );
            }
        }

        $response = $this->generateResponseForCode(
            $response,
            1,
            $txnId,
            $request['order_id']
        );
        $response['error_code'] = '';
        $response['payment_method_details'] = $request['payment_method_details'];

        $this->_femsaLogger->info(
            'HTTP Client TransactionCapture Iframe Payment :: placeRequest',
            [
                'request' => $request,
                'response' => $response
            ]
        );

        return $response;
    }

    /**
     * @throws Exception
     */
    protected function generateResponseForCode($response, $resultCode, $txn_id, $ord_id): array
    {
        $this->_femsaLogger->info('HTTP Client TransactionCapture :: generateResponseForCode');

        if (empty($txn_id)) {
            $txn_id = $this->generateTxnId();
        }
        return array_merge(
            $response,
            [
                'RESULT_CODE' => $resultCode,
                'TXN_ID' => $txn_id,
                'ORD_ID' => $ord_id
            ]
        );
    }

    /**
     * @throws Exception
     */
    protected function generateTxnId(): string
    {
        $this->_femsaLogger->info('HTTP Client TransactionCapture :: generateTxnId');

        return sha1(random_int(0, 1000));
    }
}
