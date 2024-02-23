<?php
namespace Femsa\Payments\Gateway\Request;

use Femsa\Payments\Helper\Data as FemsaHelper;
use Femsa\Payments\Logger\Logger as FemsaLogger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class ShippingLinesBuilder implements BuilderInterface
{
    private SubjectReader $subjectReader;

    private FemsaLogger $_logger;

    private FemsaHelper $_femsaHelper;

    public function __construct(
        SubjectReader $subjectReader,
        FemsaLogger   $logger,
        FemsaHelper   $femsaHelper
    ) {
        $this->_logger = $logger;
        $this->_logger->info('Request ShippingLinesBuilder :: __construct');
        $this->subjectReader = $subjectReader;
        $this->_femsaHelper = $femsaHelper;
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
        
        $shippingLines = $this->_femsaHelper->getShippingLines($quote_id);

        if (empty($shippingLines)) {

            throw new LocalizedException(__('Shipment information should be provided'));
        }

        $request['shipping_lines'] = $shippingLines;

        $this->_logger->info('Request ShippingLinesBuilder :: build : return request', $request);
        
        return $request;
    }
}
