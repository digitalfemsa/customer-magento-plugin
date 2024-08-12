<?php
namespace DigitalFemsa\Payments\Api\Data;

interface DigitalFemsaQuoteInterface
{
    public const QUOTE_ID = 'quote_id';
    public const DIGITALFEMSA_ORDER_ID = 'digitalfemsa_order_id';
    public const MINIMUM_AMOUNT_PER_QUOTE = 20;

    /**
     * Get QuoteId
     *
     * @return int
     */
    public function getQuoteId();

    /**
     * Set Quote Id
     *
     * @param int $value
     * @return void
     */
    public function setQuoteId($value);

    /**
     * Get DigitalFemsa Order Id
     *
     * @return string
     */
    public function getFemsaOrderId();

    /**
     * Set DigitalFemsa Order Id
     *
     * @param string $value
     * @return void
     */
    public function setFemsaOrderId($value);
}
