<?php

namespace DigitalFemsa\Payments\Gateway\Http\Client\EmbedForm;

use DigitalFemsa\Model\ChargeResponse;
use DigitalFemsa\Payments\Api\DigitalFemsaApiClient;
use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use DigitalFemsa\Payments\Api\Data\DigitalFemsaSalesOrderInterface;
use DigitalFemsa\Payments\Model\DigitalFemsaSalesOrderFactory;
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

    protected $_digitalFemsaHelper;

    private $_digitalFemsaLogger;

    protected $femsaSalesOrderFactory;

    /**
     * @var DigitalFemsaApiClient
     */
    private $femsaApiClient;

    /**
     * @var ChargeResponse
     */
    private $charge;

    /**
     * @param Logger $logger
     * @param DigitalFemsaHelper $digitalFemsaHelper
     * @param DigitalFemsaLogger $DigitalFemsaLogger
     * @param DigitalFemsaApiClient $femsaApiClient
     * @param DigitalFemsaSalesOrderFactory $femsaSalesOrderFactory
     */
    public function __construct(
        Logger                  $logger,
        DigitalFemsaHelper      $digitalFemsaHelper,
        DigitalFemsaLogger      $DigitalFemsaLogger,
        DigitalFemsaApiClient   $femsaApiClient,
        DigitalFemsaSalesOrderFactory  $femsaSalesOrderFactory
    )
    {
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
        $this->_digitalFemsaLogger = $DigitalFemsaLogger;
        $this->_digitalFemsaLogger->info('HTTP Client TransactionCapture :: __construct');
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
        $this->_digitalFemsaLogger->info('HTTP Client TransactionCapture :: placeRequest', $request);

        $txnId = $request['txn_id'];

        $this->femsaSalesOrderFactory
            ->create()
            ->setData([
                DigitalFemsaSalesOrderInterface::DIGITALFEMSA_ORDER_ID => $request['order_id'],
                DigitalFemsaSalesOrderInterface::INCREMENT_ORDER_ID => $request['metadata']['order_id']
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
                $this->_digitalFemsaLogger->error(
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

        $this->_digitalFemsaLogger->info(
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
        $this->_digitalFemsaLogger->info('HTTP Client TransactionCapture :: generateResponseForCode');

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
        $this->_digitalFemsaLogger->info('HTTP Client TransactionCapture :: generateTxnId');

        return sha1(random_int(0, 1000));
    }
}
