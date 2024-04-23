<?php
namespace DigitalFemsa\Payments\Model\Ui\EmbedForm;

use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as FemsaLogger;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Payment method code
     */
    public const CODE = 'femsa_ef';
    public const PAYMENT_METHOD_CASH = 'cash';

    public const URL_PANEL_PAYMENTS = "https://panel.digitalfemsa.io/transactions/payments";
    /**
     * Create Order Controller Path
     */
    public const CREATEORDER_URL = 'digitalfemsa/index/createorder';
    /**
     * @var FemsaHelper
     */
    protected FemsaHelper $_femsaHelper;
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var FemsaLogger
     */
    protected FemsaLogger $femsaLogger;
    /**
     * @var UrlInterface
     */
    protected UrlInterface $url;

    /**
     * ConfigProvider constructor.
     *
     * @param FemsaHelper $femsaHelper
     * @param Session $checkoutSession
     * @param FemsaLogger $femsaLogger
     * @param UrlInterface $url
     */
    public function __construct(
        FemsaHelper  $femsaHelper,
        Session      $checkoutSession,
        FemsaLogger  $femsaLogger,
        UrlInterface $url
    ) {
        $this->_femsaHelper = $femsaHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->femsaLogger = $femsaLogger;
        $this->url = $url;
    }

    /**
     * Get config
     *
     * @return array|\array[][]
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'hasVerification' => true,
                    'total' => $this->getQuote()->getGrandTotal(),
                    'createOrderUrl' => $this->url->getUrl(self::CREATEORDER_URL),
                    'paymentMethods' => $this->getPaymentMethodsActive(),
                    'sessionExpirationTime' => $this->_checkoutSession->getCookieLifetime()
                ]
            ]
        ];
    }


    /**
     * Get Quote
     *
     * @return CartInterface|Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Get active payment methods
     *
     * @return array
     */
    public function getPaymentMethodsActive(): array
    {
        $methods = [];
        if ($this->_femsaHelper->isCashEnabled()) {
            $methods[] = 'Cash';
        }
        return $methods;
    }
}
