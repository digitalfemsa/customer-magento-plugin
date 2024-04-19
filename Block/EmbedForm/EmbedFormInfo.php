<?php
namespace DigitalFemsa\Payments\Block\EmbedForm;

use DigitalFemsa\Payments\Model\Ui\EmbedForm\ConfigProvider;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Info;
use Magento\Payment\Model\Config;
use Magento\Framework\Exception\LocalizedException;

class EmbedFormInfo extends Info
{
    /**
     * @var Config
     */
    protected Config $_paymentConfig;

    /**
     * @var string
     */
    protected $_template = 'Femsa_Payments::info/embedform.phtml';

    /**
     * @param Context $context
     * @param Config $paymentConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $paymentConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_paymentConfig = $paymentConfig;
    }


    /**
     * Get additional Data
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getAdditionalData()
    {
        return $this->getInfo()->getAdditionalInformation();
    }

    /**
     * Get off-line info
     *
     * @return false|mixed
     * @throws LocalizedException
     */
    public function getOfflineInfo()
    {
        $additional_data = $this->getAdditionalData();
        if (isset($additional_data['offline_info']['data'])) {
            return $additional_data['offline_info']['data'];
        }

        return false;
    }

    /**
     * Get payment method type
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getPaymentMethodType()
    {
        return $this->getInfo()->getAdditionalInformation('payment_method');
    }

    /**
     * Get payment method title
     *
     * @return string
     * @throws LocalizedException
     */
    public function getPaymentMethodTitle(): string
    {
        $methodType = $this->getPaymentMethodType();
        $title = '';

        if ($methodType == ConfigProvider::PAYMENT_METHOD_CASH) {
            $title = 'Pago en Efectivo';
        }

        return $title;
    }


    /**
     * Is cash payment method
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isCashPaymentMethod(): bool
    {
        return $this->getPaymentMethodType() === ConfigProvider::PAYMENT_METHOD_CASH;
    }

}
