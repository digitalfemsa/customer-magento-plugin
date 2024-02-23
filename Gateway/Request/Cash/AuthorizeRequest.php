<?php
namespace Femsa\Payments\Gateway\Request\Cash;

use Femsa\Payments\Helper\Data as FemsaHelper;
use Femsa\Payments\Logger\Logger as FemsaLogger;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class AuthorizeRequest implements BuilderInterface
{

    private SubjectReader $subjectReader;

    protected FemsaHelper $_femsaHelper;

    private FemsaLogger $_femsaLogger;

    public function __construct(
        SubjectReader $subjectReader,
        FemsaHelper   $femsaHelper,
        FemsaLogger $femsaLogger
    ) {
        $this->_femsaHelper = $femsaHelper;
        $this->_femsaLogger = $femsaLogger;
        $this->_femsaLogger->info('Request Cash AuthorizeRequest :: __construct');

        $this->subjectReader = $subjectReader;
    }

    public function build(array $buildSubject): array
    {
        $this->_femsaLogger->info('Request Cash AuthorizeRequest :: build');

        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $order = $paymentDO->getOrder();
        
        $expiry_date = $this->_femsaHelper->getExpiredAt();
        $amount = $this->_femsaHelper->convertToApiPrice($order->getGrandTotalAmount());

        $request['metadata'] = [
            'plugin' => 'Magento',
            'plugin_version' => $this->_femsaHelper->getMageVersion(),
            'plugin_femsa_version' => $this->_femsaHelper->pluginVersion(),
            'order_id'       => $order->getOrderIncrementId(),
            'soft_validations'  => 'true'
        ];

        $request['payment_method_details'] = $this->getChargeCash($amount, $expiry_date);
        $request['CURRENCY'] = $order->getCurrencyCode();
        $request['TXN_TYPE'] = 'A';

        return $request;
    }

    public function getChargeCash($amount, $expiry_date): array
    {
        return [
            'payment_method' => [
                'type' => 'cash',
                'expires_at' => $expiry_date
            ],
            'amount' => $amount
        ];
    }
}
