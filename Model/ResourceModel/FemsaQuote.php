<?php
namespace Femsa\Payments\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FemsaQuote extends AbstractDb
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('femsa_quote', 'quote_id');
        $this->_isPkAutoIncrement = false;
    }
}
