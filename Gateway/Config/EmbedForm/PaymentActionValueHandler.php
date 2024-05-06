<?php
namespace DigitalFemsa\Payments\Gateway\Config\EmbedForm;

use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;

class PaymentActionValueHandler implements ValueHandlerInterface
{
    /**
     * @var DigitalFemsaHelper
     */
    protected DigitalFemsaHelper $_digitalFemsaHelper;

    /**
     * @param DigitalFemsaHelper $digitalFemsaHelper
     */
    public function __construct(
        DigitalFemsaHelper $digitalFemsaHelper
    ) {
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
    }

    /**
     * Handle
     *
     * @param array $subject
     * @param mixed $storeId
     * @return string
     */
    public function handle(array $subject, $storeId = null): string
    {
        return 'authorize';
    }
}
