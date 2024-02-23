<?php
namespace Femsa\Payments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Checkout\Model\Session;

class DataAssignObserver extends AbstractDataAssignObserver
{
    public const PAYMENT_METHOD = 'payment_method';
    public const IFRAME_PAYMENT = 'iframe_payment';
    public const ORDER_ID = 'order_id';
    public const TXN_ID = 'txn_id';
    public const REFERENCE = 'reference';
    /**
     * @var string[]
     */
    protected array $additionalInformationList = [
        self::PAYMENT_METHOD,
        self::IFRAME_PAYMENT,
        self::ORDER_ID,
        self::TXN_ID,
        self::REFERENCE,
    ];
    /**
     * @var Session
     */
    protected Session $_checkoutSession;

    /**
     * @param Session $checkoutSession
     */
    public function __construct(
        Session $checkoutSession
    ) {
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);
        $quote = $this->_checkoutSession->getQuote();

        $paymentInfo->setAdditionalInformation(
            'quote_id',
            $quote->getId()
        );

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
