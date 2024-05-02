<?php
namespace DigitalFemsa\Payments\Model\Ui;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaFemsaHelper;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'digitalfemsa_global';
    /**
     * @var DigitalFemsaFemsaHelper
     */
    protected DigitalFemsaFemsaHelper $_digitalFemsaHelper;
    /**
     * @var mixed
     */
    private $_assetRepository;

    /**
     * @param DigitalFemsaFemsaHelper $digitalFemsaHelper
     * @param AssetRepository $assetRepository
     */
    public function __construct(
        DigitalFemsaFemsaHelper     $digitalFemsaHelper,
        AssetRepository $assetRepository
    ) {
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
        $this->_assetRepository = $assetRepository;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'publicKey' => $this->_digitalFemsaHelper->getPublicKey(),
                    'femsa_logo' => $this->_assetRepository->getUrl('DigitalFemsa_Payments::images/femsa.png')
                ]
            ]
        ];
    }
}
