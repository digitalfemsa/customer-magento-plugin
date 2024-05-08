<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

use Magento\Payment\Gateway\Helper\SubjectReader;

class MetadataBuilder implements BuilderInterface
{
    private DigitalFemsaLogger $_digitalFemsaLogger;

    protected DigitalFemsaHelper $_digitalFemsaHelper;

    private SubjectReader $subjectReader;

    public function __construct(
        DigitalFemsaHelper  $digitalFemsaHelper,
        DigitalFemsaLogger  $digitalFemsaLogger,
        SubjectReader       $subjectReader
    ) {
        $this->_digitalFemsaLogger = $digitalFemsaLogger;
        $this->_digitalFemsaLogger->info('Request MetadataBuilder :: __construct');
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $this->_digitalFemsaLogger->info('Request MetadataBuilder :: build');

        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $payment = $this->subjectReader->readPayment($buildSubject);
        $order = $payment->getOrder();
        $items = $order->getItems();
        $request['metadata'] = $this->_digitalFemsaHelper->getMetadataAttributesFemsa($items);

        $this->_digitalFemsaLogger->info('Request MetadataBuilder :: build : return request', $request);

        return $request;
    }
}
