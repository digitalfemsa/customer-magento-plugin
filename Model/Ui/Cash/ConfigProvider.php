<?php
namespace DigitalFemsa\Payments\Model\Ui\Cash;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Asset\Repository;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'digitalfemsa_cash';
    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var Repository
     */
    protected $_assetRepository;
    /**
     * @var DigitalFemsaHelper
     */
    protected DigitalFemsaHelper $_digitalFemsaHelper;

    /**
     * @param Session $checkoutSession
     * @param Repository $assetRepository
     * @param DigitalFemsaHelper $digitalFemsaHelper
     */
    public function __construct(
        Session             $checkoutSession,
        Repository          $assetRepository,
        DigitalFemsaHelper  $digitalFemsaHelper
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_assetRepository = $assetRepository;
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
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
