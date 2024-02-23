<?php
namespace Femsa\Payments\Gateway\Request;

use Femsa\Payments\Helper\Data as FemsaHelper;
use Femsa\Payments\Logger\Logger as FemsaLogger;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class DiscountLinesBuilder implements BuilderInterface
{
    private FemsaLogger $_logger;

    private SubjectReader $subjectReader;

    protected CartRepositoryInterface $_cartRepository;

    private FemsaHelper $_femsaHelper;

    public function __construct(
        FemsaLogger             $femsaLogger,
        SubjectReader           $subjectReader,
        CartRepositoryInterface $cartRepository,
        FemsaHelper $femsaHelper
    ) {
        $this->_logger = $femsaLogger;
        $this->_logger->info('Request DiscountLinesBuilder :: __construct');
        $this->subjectReader = $subjectReader;
        $this->_cartRepository = $cartRepository;
        $this->_femsaHelper = $femsaHelper;
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
            $totalDiscount = $this->_femsaHelper->convertToApiPrice($totalDiscount);
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
