<?php
namespace DigitalFemsa\Payments\Model\Ui;

use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'femsa_global';
    /**
     * @var FemsaHelper
     */
    protected FemsaHelper $_femsaHelper;
    /**
     * @var mixed
     */
    private $_assetRepository;

    /**
     * @param FemsaHelper $femsaHelper
     * @param AssetRepository $assetRepository
     */
    public function __construct(
        FemsaHelper     $femsaHelper,
        AssetRepository $assetRepository
    ) {
        $this->_femsaHelper = $femsaHelper;
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
                    'publicKey' => $this->_femsaHelper->getPublicKey(),
                    'femsa_logo' => $this->_assetRepository->getUrl('Femsa_Payments::images/femsa.png')
                ]
            ]
        ];
    }
}
