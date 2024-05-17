<?php
namespace DigitalFemsa\Payments\Model\Ui\EmbedForm;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
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
    public const CODE = 'digitalfemsa_ef';
    public const PAYMENT_METHOD_CASH = 'cash';

    public const URL_PANEL_PAYMENTS = "https://panel.stg.digitalfemsa.io/transactions/payments";
    /**
     * Create Order Controller Path
     */
    public const CREATEORDER_URL = 'digitalfemsa/index/createorder';
    /**
     * @var DigitalFemsaHelper
     */
    protected DigitalFemsaHelper $_digitalFemsaHelper;
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var DigitalFemsaLogger
     */
    protected DigitalFemsaLogger $digitalFemsaLogger;
    /**
     * @var UrlInterface
     */
    protected UrlInterface $url;

    /**
     * ConfigProvider constructor.
     *
     * @param DigitalFemsaHelper $digitalFemsaHelper
     * @param Session $checkoutSession
     * @param DigitalFemsaLogger $digitalFemsaLogger
     * @param UrlInterface $url
     */
    public function __construct(
        DigitalFemsaHelper  $digitalFemsaHelper,
        Session      $checkoutSession,
        DigitalFemsaLogger  $digitalFemsaLogger,
        UrlInterface $url
    ) {
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->digitalFemsaLogger = $digitalFemsaLogger;
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
        if ($this->_digitalFemsaHelper->isCashEnabled()) {
            $methods[] = 'Cash';
        }
        return $methods;
    }
}
