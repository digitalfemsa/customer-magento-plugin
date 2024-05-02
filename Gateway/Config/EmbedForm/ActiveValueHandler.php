<?php
namespace DigitalFemsa\Payments\Gateway\Config\EmbedForm;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaFemsaHelper;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;

class ActiveValueHandler implements ValueHandlerInterface
{
    /**
     * @var DigitalFemsaFemsaHelper
     */
    protected DigitalFemsaFemsaHelper $_digitalFemsaHelper;

    /**
     * @param DigitalFemsaFemsaHelper $digitalFemsaHelper
     */
    public function __construct(DigitalFemsaFemsaHelper $digitalFemsaHelper) {
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
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
        return $this->_digitalFemsaHelper->isCashEnabled();
    }
}
