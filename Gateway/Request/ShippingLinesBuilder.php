<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class ShippingLinesBuilder implements BuilderInterface
{
    private SubjectReader $subjectReader;

    private DigitalFemsaLogger $_logger;

    private DigitalFemsaFemsaHelper $_digitalFemsaHelper;

    public function __construct(
        SubjectReader $subjectReader,
        DigitalFemsaLogger   $logger,
        DigitalFemsaFemsaHelper   $digitalFemsaHelper
    ) {
        $this->_logger = $logger;
        $this->_logger->info('Request ShippingLinesBuilder :: __construct');
        $this->subjectReader = $subjectReader;
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $this->_logger->info('Request ShippingLinesBuilder :: build');

        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        $quote_id = $payment->getAdditionalInformation('quote_id');
        
        $shippingLines = $this->_digitalFemsaHelper->getShippingLines($quote_id);

        if (empty($shippingLines)) {

            throw new LocalizedException(__('Shipment information should be provided'));
        }

        $request['shipping_lines'] = $shippingLines;

        $this->_logger->info('Request ShippingLinesBuilder :: build : return request', $request);
        
        return $request;
    }
}
