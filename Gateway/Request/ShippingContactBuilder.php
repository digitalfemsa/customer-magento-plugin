<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Logger\Logger as FemsaLogger;
use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class ShippingContactBuilder implements BuilderInterface
{
    private SubjectReader $subjectReader;

    private FemsaLogger $_logger;

    private FemsaHelper $_femsaHelper;

    public function __construct(
        SubjectReader $subjectReader,
        FemsaLogger   $femsaLogger,
        FemsaHelper $femsaHelper
    ) {
        $this->_logger = $femsaLogger;
        $this->_logger->info('Request ShippingContactBuilder :: __construct');
        $this->subjectReader = $subjectReader;
        $this->_femsaHelper = $femsaHelper;
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

        $request['shipping_contact'] = $this->_femsaHelper->getShippingContact($quoteId);

        if (empty($request['shipping_contact'])) {
            throw new LocalizedException(__('Missing shipping contact information'));
        }

        $this->_logger->info('Request ShippingContactBuilder :: build : return request', $request);

        return $request;
    }
}
