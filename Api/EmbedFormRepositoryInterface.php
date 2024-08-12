<?php
namespace DigitalFemsa\Payments\Api;

use DigitalFemsa\Model\OrderResponse;

interface EmbedFormRepositoryInterface
{
    /**
     * Generate form repository interface
     *
     * @param int $quoteId
     * @param $orderParams
     * @param float $orderTotal
     * @return OrderResponse
     */
    public function generate($quoteId, $orderParams, $orderTotal): OrderResponse;
}
