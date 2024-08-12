<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class ShippingContactBuilder implements BuilderInterface
{
    private SubjectReader $subjectReader;

    private DigitalFemsaLogger $_logger;

    private DigitalFemsaHelper $_digitalFemsaHelper;

    public function __construct(
        SubjectReader       $subjectReader,
        DigitalFemsaLogger  $digitalFemsaLogger,
        DigitalFemsaHelper  $digitalFemsaHelper
    ) {
        $this->_logger = $digitalFemsaLogger;
        $this->_logger->info('Request ShippingContactBuilder :: __construct');
        $this->subjectReader = $subjectReader;
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $this->_logger->info('Request ShippingContactBuilder :: build');

        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $quoteId = $payment->getAdditionalInformation('quote_id');

        $request['shipping_contact'] = $this->_digitalFemsaHelper->getShippingContact($quoteId);

        if (empty($request['shipping_contact'])) {
            throw new LocalizedException(__('Missing shipping contact information'));
        }

        $this->_logger->info('Request ShippingContactBuilder :: build : return request', $request);

        return $request;
    }
}
