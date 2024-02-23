<?php
namespace Femsa\Payments\Model;

use Femsa\Payments\Api\Data\FemsaSalesOrderInterface;
use Magento\Framework\Model\AbstractModel;
use Femsa\Payments\Model\ResourceModel\FemsaSalesOrder as ResourceFemsaSalesOrder;

class FemsaSalesOrder extends AbstractModel implements FemsaSalesOrderInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceFemsaSalesOrder::class);
    }

    /**
     * setFemsaOrderId
     *
     * @param mixed $value
     * @return mixed|void
     */
    public function setFemsaOrderId($value)
    {
        $this->setData(FemsaSalesOrderInterface::FEMSA_ORDER_ID, $value);
    }

    /**
     * getFemsaOrderId
     *
     * @return array|mixed|null
     */
    public function getFemsaOrderId()
    {
        return $this->getData(FemsaSalesOrderInterface::FEMSA_ORDER_ID);
    }

    /**
     * SetIncrementOrderId
     *
     * @param mixed $value
     * @return mixed|void
     */
    public function setIncrementOrderId($value)
    {
        $this->setData(FemsaSalesOrderInterface::INCREMENT_ORDER_ID, $value);
    }

    /**
     * GetIncrementOrderId
     *
     * @return array|mixed|string|null
     */
    public function getIncrementOrderId()
    {
        return $this->getData(FemsaSalesOrderInterface::INCREMENT_ORDER_ID);
    }

    /**
     * loadByFemsaOrderId
     *
     * @param mixed $femsaOrderId
     * @return $this
     */
    public function loadByFemsaOrderId($femsaOrderId)
    {
        return $this->loadByAttribute(FemsaSalesOrderInterface::FEMSA_ORDER_ID, $femsaOrderId);
    }

    /**
     * Load order by custom attribute value. Attribute value should be unique
     *
     * @param string $attribute
     * @param string $value
     * @return $this
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }
}
