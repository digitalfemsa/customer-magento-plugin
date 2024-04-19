<?php
namespace DigitalFemsa\Payments\Model\Ui\Cash;

use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Asset\Repository;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'femsa_cash';
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var Repository
     */
    protected $_assetRepository;
    /**
     * @var FemsaHelper
     */
    protected FemsaHelper $_femsaHelper;

    /**
     * @param Session $checkoutSession
     * @param Repository $assetRepository
     * @param FemsaHelper $femsaHelper
     */
    public function __construct(
        Session $checkoutSession,
        Repository $assetRepository,
        FemsaHelper $femsaHelper
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_assetRepository = $assetRepository;
        $this->_femsaHelper = $femsaHelper;
    }

    /**
     * Get config
     *
     * @return \array[][]
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'total' => $this->getQuote()->getGrandTotal()
                ]
            ]
        ];
    }

    /**
     * Get quote
     *
     * @return CartInterface|Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }
}
