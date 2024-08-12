<?php
namespace DigitalFemsa\Payments\Model\ResourceModel\DigitalFemsaQuote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DigitalFemsa\Payments\Model\DigitalFemsaQuote;
use DigitalFemsa\Payments\Model\ResourceModel\DigitalFemsaQuote as ResourceFemsaQuote;

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
            DigitalFemsaQuote::class,
            ResourceFemsaQuote::class
        );
    }
}
