<?php
namespace DigitalFemsa\Payments\Gateway\Request\EmbedForm;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use DigitalFemsa\Payments\Model\Config;
use DigitalFemsa\Payments\Model\Ui\EmbedForm\ConfigProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class CaptureRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;
    /**
     * @var SubjectReader
     */
    protected SubjectReader $subjectReader;
    /**
     * @var DigitalFemsaFemsaHelper
     */
    protected DigitalFemsaFemsaHelper $_digitalFemsaHelper;
    /**
     * @var DigitalFemsaLogger
     */
    protected DigitalFemsaLogger $_femsaLogger;
    /**
     * @var Config
     */
    protected Config $femsaConfig;
    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;
    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * CaptureRequest constructor.
     * @param ConfigInterface $config
     * @param SubjectReader $subjectReader
     * @param DigitalFemsaFemsaHelper $digitalFemsaHelper
     * @param DigitalFemsaLogger $digitalFemsaLogger
     * @param Config $femsaConfig
     * @param CustomerSession $session
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        ConfigInterface             $config,
        SubjectReader               $subjectReader,
        DigitalFemsaFemsaHelper                 $digitalFemsaHelper,
        DigitalFemsaLogger                 $digitalFemsaLogger,
        Config                      $femsaConfig,
        CustomerSession             $session,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
        $this->_femsaLogger = $digitalFemsaLogger;
        $this->_femsaLogger->info('EMBED Request CaptureRequest :: __construct');
        $this->config = $config;
        $this->subjectReader = $subjectReader;
        $this->femsaConfig = $femsaConfig;
        $this->customerSession = $session;
        $this->customerRepository = $customerRepository;
    }

    public function build(array $buildSubject)
    {
        $this->_femsaLogger->info('Request CaptureRequest :: build');

        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $this->_femsaLogger->info('Request CaptureRequest :: build additional', $payment->getAdditionalInformation());
        $iframePayment = $payment->getAdditionalInformation('iframe_payment');
        $iframeOrderId = $payment->getAdditionalInformation('order_id');
        $txnId = $payment->getAdditionalInformation('txn_id');
        $amount = (int)($order->getGrandTotalAmount() * 100);

        $request['metadata'] = [
            'plugin' => 'Magento',
            'plugin_version' => $this->_digitalFemsaHelper->getMageVersion(),
            'order_id'       => $order->getOrderIncrementId(),
            'soft_validations'  => 'true'
        ];
        $request['payment_method_details'] = $this->getCharge($payment, $amount);
        $request['CURRENCY'] = $order->getCurrencyCode();
        $request['TXN_TYPE'] = 'A';
        $request['INVOICE'] = $order->getOrderIncrementId();
        $request['AMOUNT'] = number_format($order->getGrandTotalAmount(), 2);
        $request['iframe_payment'] = $iframePayment;
        $request['order_id'] = $iframeOrderId;
        $request['txn_id'] = $txnId;

        $request['digitalfemsa_customer_id'] = $payment->getAdditionalInformation('digitalfemsa_customer_id');

        $this->_femsaLogger->info('Request CaptureRequest :: build : return request', $request);

        return $request;
    }

    private function getCharge($payment, $orderAmount): array
    {

        $paymentMethod = $payment->getAdditionalInformation('payment_method');

        $charge = [
            'payment_method' => [
                'type' => $paymentMethod
            ],
            'amount' => $orderAmount
        ];
        if ($paymentMethod == ConfigProvider::PAYMENT_METHOD_CASH) {
            $reference = $payment->getAdditionalInformation('reference');
            $expireAt = $this->_digitalFemsaHelper->getExpiredAt();
            $charge['payment_method']['reference'] = $reference;
            $charge['payment_method']['expires_at'] = $expireAt;
        }

        return $charge;
    }
}
