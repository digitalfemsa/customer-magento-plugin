<?php
namespace Femsa\Payments\Gateway\Config\Cash;

use Femsa\Payments\Helper\Data as FemsaHelper;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;

class PaymentActionValueHandler implements ValueHandlerInterface
{
    /**
     * @var FemsaHelper
     */
    protected FemsaHelper $_femsaHelper;

    /**
     * @param FemsaHelper $femsaHelper
     */
    public function __construct(
        FemsaHelper $femsaHelper
    ) {
        $this->_femsaHelper = $femsaHelper;
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
