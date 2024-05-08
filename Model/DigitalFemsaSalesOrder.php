<?php
namespace DigitalFemsa\Payments\Model;

use DigitalFemsa\Payments\Api\Data\DigitalFemsaSalesOrderInterface;
use Magento\Framework\Model\AbstractModel;
use DigitalFemsa\Payments\Model\ResourceModel\DigitalFemsaSalesOrder as ResourceFemsaSalesOrder;

class DigitalFemsaSalesOrder extends AbstractModel implements DigitalFemsaSalesOrderInterface
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
        $this->setData(DigitalFemsaSalesOrderInterface::DIGITALFEMSA_ORDER_ID, $value);
    }

    /**
     * getFemsaOrderId
     *
     * @return array|mixed|null
     */
    public function getFemsaOrderId()
    {
        return $this->getData(DigitalFemsaSalesOrderInterface::DIGITALFEMSA_ORDER_ID);
    }

    /**
     * SetIncrementOrderId
     *
     * @param mixed $value
     * @return mixed|void
     */
    public function setIncrementOrderId($value)
    {
        $this->setData(DigitalFemsaSalesOrderInterface::INCREMENT_ORDER_ID, $value);
    }

    /**
     * GetIncrementOrderId
     *
     * @return array|mixed|string|null
     */
    public function getIncrementOrderId()
    {
        return $this->getData(DigitalFemsaSalesOrderInterface::INCREMENT_ORDER_ID);
    }

    /**
     * loadByFemsaOrderId
     *
     * @param mixed $femsaOrderId
     * @return $this
     */
    public function loadByFemsaOrderId($femsaOrderId)
    {
        return $this->loadByAttribute(DigitalFemsaSalesOrderInterface::DIGITALFEMSA_ORDER_ID, $femsaOrderId);
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
