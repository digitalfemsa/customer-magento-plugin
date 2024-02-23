<?php
namespace Femsa\Payments\Api\Data;

interface FemsaSalesOrderInterface
{

    public const FEMSA_ORDER_ID = 'femsa_order_id';
    public const INCREMENT_ORDER_ID = 'increment_order_id';

    /**
     * Get Femsa ID
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get Femsa Order Id
     *
     * @return mixed
     */
    public function getFemsaOrderId();

    /**
     * Set Femsa Order Id
     *
     * @param mixed $value
     * @return mixed
     */
    public function setFemsaOrderId($value);

    /**
     * Gets the Sales Increment Order ID
     *
     * @return string|null Sales Increment Order ID.
     */
    public function getIncrementOrderId();

    /**
     * Set Increment Order Id
     *
     * @param mixed $value
     * @return mixed
     */
    public function setIncrementOrderId($value);
}
