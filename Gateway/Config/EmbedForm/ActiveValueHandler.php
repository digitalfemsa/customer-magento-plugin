<?php
namespace DigitalFemsa\Payments\Gateway\Config\EmbedForm;

use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;

class ActiveValueHandler implements ValueHandlerInterface
{
    /**
     * @var FemsaHelper
     */
    protected FemsaHelper $_femsaHelper;

    /**
     * @param FemsaHelper $femsaHelper
     */
    public function __construct(FemsaHelper $femsaHelper) {
        $this->_femsaHelper = $femsaHelper;
    }

    /**
     * Handle
     *
     * @param array $subject
     * @param mixed $storeId
     * @return bool
     */
    public function handle(array $subject, $storeId = null): bool
    {
        return $this->_femsaHelper->isCashEnabled();
    }
}
