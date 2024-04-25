<?php
namespace DigitalFemsa\Payments\Model;

use DigitalFemsa\Payments\Api\Data\FemsaQuoteInterface;
use DigitalFemsa\Payments\Model\ResourceModel\FemsaQuote as ResourceFemsaQuote;
use Magento\Framework\Model\AbstractModel;

class FemsaQuote extends AbstractModel implements FemsaQuoteInterface
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
        $this->setData(FemsaQuoteInterface::QUOTE_ID, $value);
    }

    /**
     * GetQuoteId
     *
     * @return array|int|mixed|null
     */
    public function getQuoteId()
    {
        return $this->getData(FemsaQuoteInterface::QUOTE_ID);
    }

    /**
     * setFemsaOrderId
     *
     * @param mixed $value
     * @return void
     */
    public function setFemsaOrderId($value)
    {
        $this->setData(FemsaQuoteInterface::DIGITALFEMSA_ORDER_ID, $value);
    }

    /**
     * getFemsaOrderId
     *
     * @return array|mixed|string|null
     */
    public function getFemsaOrderId()
    {
        return $this->getData(FemsaQuoteInterface::DIGITALFEMSA_ORDER_ID);
    }

    /**
     * loadByFemsaOrderId
     *
     * @param mixed $femsaOrderId
     * @return $this
     */
    public function loadByFemsaOrderId($femsaOrderId)
    {
        return $this->loadByAttribute(FemsaQuoteInterface::FEMSA_ORDER_ID, $femsaOrderId);
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
