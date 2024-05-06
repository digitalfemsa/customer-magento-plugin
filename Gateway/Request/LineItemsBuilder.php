<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class LineItemsBuilder implements BuilderInterface
{

    private DigitalFemsaLogger $_digitalFemsaLogger;

    protected DigitalFemsaHelper $_digitalFemsaHelper;

    public function __construct(
        DigitalFemsaHelper $digitalFemsaHelper,
        DigitalFemsaLogger $digitalFemsaLogger
    ) {
        $this->_digitalFemsaLogger = $digitalFemsaLogger;
        $this->_digitalFemsaLogger->info('Request LineItemsBuilder :: __construct');
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
    }

    public function build(array $buildSubject)
    {
        $this->_digitalFemsaLogger->info('Request LineItemsBuilder :: build');

        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $payment = $buildSubject['payment'];
        $order = $payment->getOrder();
        $items = $order->getItems();
        $request['line_items'] = $this->_digitalFemsaHelper->getLineItems($items, false);

        $this->_digitalFemsaLogger->info('Request LineItemsBuilder :: build : return request', $request);

        return $request;
    }
}
