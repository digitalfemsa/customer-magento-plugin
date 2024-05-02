<?php
namespace DigitalFemsa\Payments\Model;

use DigitalFemsa\Payments\Api\Data\DigitalFemsaQuoteInterface;
use DigitalFemsa\Payments\Model\ResourceModel\DigitalFemsaQuote as ResourceFemsaQuote;
use Magento\Framework\Model\AbstractModel;

class DigitalFemsaQuote extends AbstractModel implements DigitalFemsaQuoteInterface
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceFemsaQuote::class);
    }

    /**
     * SetQuoteId
     *
     * @param mixed $value
     * @return void
     */
    public function setQuoteId($value)
    {
        $this->setData(DigitalFemsaQuoteInterface::QUOTE_ID, $value);
    }

    /**
     * GetQuoteId
     *
     * @return array|int|mixed|null
     */
    public function getQuoteId()
    {
        return $this->getData(DigitalFemsaQuoteInterface::QUOTE_ID);
    }

    /**
     * setFemsaOrderId
     *
     * @param mixed $value
     * @return void
     */
    public function setFemsaOrderId($value)
    {
        $this->setData(DigitalFemsaQuoteInterface::DIGITALFEMSA_ORDER_ID, $value);
    }

    /**
     * getFemsaOrderId
     *
     * @return array|mixed|string|null
     */
    public function getFemsaOrderId()
    {
        return $this->getData(DigitalFemsaQuoteInterface::DIGITALFEMSA_ORDER_ID);
    }

    /**
     * loadByFemsaOrderId
     *
     * @param mixed $femsaOrderId
     * @return $this
     */
    public function loadByFemsaOrderId($femsaOrderId)
    {
        return $this->loadByAttribute(DigitalFemsaQuoteInterface::FEMSA_ORDER_ID, $femsaOrderId);
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
