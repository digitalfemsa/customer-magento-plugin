<?php
namespace DigitalFemsa\Payments\Api\Data;

interface FemsaQuoteInterface
{
    public const QUOTE_ID = 'quote_id';
    public const FEMSA_ORDER_ID = 'femsa_order_id';
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
     * Get Femsa Order Id
     *
     * @return string
     */
    public function getFemsaOrderId();

    /**
     * Set Femsa Order Id
     *
     * @param string $value
     * @return void
     */
    public function setFemsaOrderId($value);
}
