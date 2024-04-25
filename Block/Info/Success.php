<?php
namespace DigitalFemsa\Payments\Block\Info;

use Magento\Checkout\Block\Onepage\Success as CompleteCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class Success extends CompleteCheckout
{
    /**
     * GetInstructions getter
     *
     * @param string $type
     * @return mixed|void
     */
    public function getInstructions(string $type)
    {
        if ($type == 'cash') {
            return $this->_scopeConfig->getValue(
                'payment/digitalfemsa_cash/instructions',
                ScopeInterface::SCOPE_STORE
            );
        }
    }

    /**
     * GetMethod getter
     *
     * @return string Object
     */
    public function getMethod(): string
    {
        return $this->getOrder()->getPayment()->getMethod();
    }

    /**
     * GetOfflineInfo getter
     *
     * @throws LocalizedException
     */
    public function getOfflineInfo()
    {
        return $this->getOrder()
            ->getPayment()
            ->getMethodInstance()
            ->getInfoInstance()
            ->getAdditionalInformation("offline_info");
    }

    /**
     * GetOrder getter
     *
     * @return Order Object
     */
    public function getOrder(): Order
    {
        return $this->_checkoutSession->getLastRealOrder();
    }

}
