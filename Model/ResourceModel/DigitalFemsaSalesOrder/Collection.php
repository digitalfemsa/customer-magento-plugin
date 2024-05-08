<?php
namespace DigitalFemsa\Payments\Model\ResourceModel\DigitalFemsaSalesOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DigitalFemsa\Payments\Model\DigitalFemsaSalesOrder;
use DigitalFemsa\Payments\Model\ResourceModel\DigitalFemsaSalesOrder as ResourceFemsaSalesOrder;

class Collection extends AbstractCollection
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            DigitalFemsaSalesOrder::class,
            ResourceFemsaSalesOrder::class
        );
    }
}
