<?php
namespace DigitalFemsa\Payments\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FemsaSalesOrder extends AbstractDb
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digitalfemsa_salesorder', 'id');
    }
}
