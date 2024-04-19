<?php
namespace DigitalFemsa\Payments\Model\ResourceModel\FemsaSalesOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DigitalFemsa\Payments\Model\FemsaSalesOrder;
use DigitalFemsa\Payments\Model\ResourceModel\FemsaSalesOrder as ResourceFemsaSalesOrder;

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
            FemsaSalesOrder::class,
            ResourceFemsaSalesOrder::class
        );
    }
}
