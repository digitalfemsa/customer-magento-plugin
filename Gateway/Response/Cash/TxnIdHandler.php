<?php
namespace Femsa\Payments\Gateway\Response\Cash;

use Femsa\Payments\Logger\Logger as FemsaLogger;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;

class TxnIdHandler implements HandlerInterface
{
    const TXN_ID = 'TXN_ID';
    const ORD_ID = 'ORD_ID';

    private FemsaLogger $_femsaLogger;

    private SubjectReader $subjectReader;

    /**
     * TxnIdHandler constructor.
     * @param FemsaLogger $femsaLogger
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        FemsaLogger   $femsaLogger,
        SubjectReader $subjectReader
    ) {
        $this->_femsaLogger = $femsaLogger;
        $this->_femsaLogger->info('Response Cash TxnIdHandler :: __construct');

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
        $this->_femsaLogger->info('Response cash TxnIdHandler :: handle');

        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();

        $payment->setTransactionId($response[self::TXN_ID]);
        $payment->setAdditionalInformation('offline_info', $response['offline_info']);

        $order->setExtOrderId($response[self::ORD_ID]);

        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);
        $payment->setShouldCloseParentTransaction(false);
    }
}
