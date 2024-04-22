<?php
namespace DigitalFemsa\Payments\Model\ResourceModel\FemsaQuote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DigitalFemsa\Payments\Model\FemsaQuote;
use DigitalFemsa\Payments\Model\ResourceModel\FemsaQuote as ResourceFemsaQuote;

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
            FemsaQuote::class,
            ResourceFemsaQuote::class
        );
    }
}
