<?php
namespace DigitalFemsa\Payments\Gateway\Response\EmbedForm;

use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use DigitalFemsa\Payments\Model\Ui\EmbedForm\ConfigProvider;

class TxnIdHandler implements HandlerInterface
{
    const TXN_ID = 'TXN_ID';

    const ORD_ID = 'ORD_ID';

    private DigitalFemsaLogger $_digitalFemsaLogger;

    private SubjectReader $subjectReader;

    /**
     * TxnIdHandler constructor.
     * @param DigitalFemsaLogger $digitalFemsaLogger
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        DigitalFemsaLogger  $digitalFemsaLogger,
        SubjectReader       $subjectReader
    ) {
        $this->_digitalFemsaLogger = $digitalFemsaLogger;
        $this->_digitalFemsaLogger->info('Response TxnIdHandler :: __construct');

        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $this->_digitalFemsaLogger->info('Response TxnIdHandler :: handle', $response);

        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();
        $paymentMethod = $payment->getAdditionalInformation('payment_method');
        if ($paymentMethod == ConfigProvider::PAYMENT_METHOD_CASH) {
            $this->handleOffline($payment, $response);
        }
    }

    private function handleOffline($payment, $response)
    {
        $order = $payment->getOrder();

        $payment->setTransactionId($response[self::TXN_ID]);
        $payment->setAdditionalInformation('offline_info', $response['offline_info']);

        $order->setExtOrderId($response[self::ORD_ID]);

        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);
        $payment->setShouldCloseParentTransaction(false);
    }
}
