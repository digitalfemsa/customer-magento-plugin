<?php
namespace Femsa\Payments\Model\ResourceModel\FemsaQuote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Femsa\Payments\Model\FemsaQuote;
use Femsa\Payments\Model\ResourceModel\FemsaQuote as ResourceFemsaQuote;

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
