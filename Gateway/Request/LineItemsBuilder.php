<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as FemsaLogger;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class LineItemsBuilder implements BuilderInterface
{

    private FemsaLogger $_femsaLogger;

    protected FemsaHelper $_femsaHelper;

    public function __construct(
        FemsaHelper $femsaHelper,
        FemsaLogger $femsaLogger
    ) {
        $this->_femsaLogger = $femsaLogger;
        $this->_femsaLogger->info('Request LineItemsBuilder :: __construct');
        $this->_femsaHelper = $femsaHelper;
    }

    public function build(array $buildSubject)
    {
        $this->_femsaLogger->info('Request LineItemsBuilder :: build');

        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $payment = $buildSubject['payment'];
        $order = $payment->getOrder();
        $items = $order->getItems();
        $request['line_items'] = $this->_femsaHelper->getLineItems($items, false);

        $this->_femsaLogger->info('Request LineItemsBuilder :: build : return request', $request);

        return $request;
    }
}
