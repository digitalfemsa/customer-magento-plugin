<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class TaxLinesBuilder implements BuilderInterface
{
    private DigitalFemsaLogger $_logger;

    private DigitalFemsaHelper $_digitalFemsaHelper;

    public function __construct(
        DigitalFemsaLogger $digitalFemsaLogger,
        DigitalFemsaHelper $digitalFemsaHelper
    ) {
        $this->_logger = $digitalFemsaLogger;
        $this->_logger->info('Request TaxLinesBuilder :: __construct');
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
    }

    public function build(array $buildSubject): array
    {
        $this->_logger->info('Request TaxLinesBuilder :: build');

        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $payment = $buildSubject['payment'];
        $order = $payment->getOrder();

        $request = [];

        $request['tax_lines'] = $this->_digitalFemsaHelper->getTaxLines($order->getItems());

        $this->_logger->info('Request TaxLinesBuilder :: build : return request', $request);

        return $request;
    }
}
