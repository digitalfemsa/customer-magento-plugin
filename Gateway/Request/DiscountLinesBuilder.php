<?php
namespace DigitalFemsa\Payments\Gateway\Request;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class DiscountLinesBuilder implements BuilderInterface
{
    private DigitalFemsaLogger $_logger;

    private SubjectReader $subjectReader;

    protected CartRepositoryInterface $_cartRepository;

    private DigitalFemsaFemsaHelper $_digitalFemsaHelper;

    public function __construct(
        DigitalFemsaLogger      $digitalFemsaLogger,
        SubjectReader           $subjectReader,
        CartRepositoryInterface $cartRepository,
        DigitalFemsaFemsaHelper $digitalFemsaHelper
    ) {
        $this->_logger = $digitalFemsaLogger;
        $this->_logger->info('Request DiscountLinesBuilder :: __construct');
        $this->subjectReader = $subjectReader;
        $this->_cartRepository = $cartRepository;
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        $this->_logger->info('Request DiscountLinesBuilder :: build');

        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $quote_id = $payment->getAdditionalInformation('quote_id');
        $quote = $this->_cartRepository->get($quote_id);
        $totalDiscount = $quote->getSubtotal() - $quote->getSubtotalWithDiscount();
        $totalDiscount = abs(round($totalDiscount, 2));

        if (!empty($totalDiscount)) {
            $totalDiscount = $this->_digitalFemsaHelper->convertToApiPrice($totalDiscount);
            $discountLine["code"] = $quote->getCouponCode() ?? "Discounts";
            $discountLine["type"] = $quote->getCouponCode() ? "coupon" : "Discounts";
            $discountLine["amount"] = $totalDiscount;
            $request['discount_lines'][] = $discountLine;
        } else {
            $request['discount_lines'] = [];
        }

        $this->_logger->info('Request DiscountLinesBuilder :: build : return request', $request);

        return $request;
    }
}
