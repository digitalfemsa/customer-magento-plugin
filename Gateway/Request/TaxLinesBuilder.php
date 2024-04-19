<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as FemsaLogger;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class TaxLinesBuilder implements BuilderInterface
{
    private FemsaLogger $_logger;

    private FemsaHelper $_femsaHelper;

    public function __construct(
        FemsaLogger $femsaLogger,
        FemsaHelper $femsaHelper
    ) {
        $this->_logger = $femsaLogger;
        $this->_logger->info('Request TaxLinesBuilder :: __construct');
        $this->_femsaHelper = $femsaHelper;
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

        $request['tax_lines'] = $this->_femsaHelper->getTaxLines($order->getItems());

        $this->_logger->info('Request TaxLinesBuilder :: build : return request', $request);

        return $request;
    }
}
