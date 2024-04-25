<?php
namespace DigitalFemsa\Payments\Api;

use DigitalFemsa\Payments\Api\Data\FemsaQuoteInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface FemsaQuoteRepositoryInterface
{
     /**
      * Get DigitalFemsa quote by ID
      *
      * @param int $id
      * @return FemsaQuoteInterface
      * @throws NoSuchEntityException
      */
    public function getById($id);

    /**
     * Save DigitalFemsa quote
     *
     * @param FemsaQuoteInterface $femsaQuote
     * @return FemsaQuoteInterface
     */
    public function save(FemsaQuoteInterface $femsaQuote);
}
