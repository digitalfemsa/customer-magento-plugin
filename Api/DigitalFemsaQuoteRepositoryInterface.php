<?php
namespace DigitalFemsa\Payments\Api;

use DigitalFemsa\Payments\Api\Data\DigitalFemsaQuoteInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface DigitalFemsaQuoteRepositoryInterface
{
     /**
      * Get DigitalFemsa quote by ID
      *
      * @param int $id
      * @return DigitalFemsaQuoteInterface
      * @throws NoSuchEntityException
      */
    public function getById($id);

    /**
     * Save DigitalFemsa quote
     *
     * @param DigitalFemsaQuoteInterface $femsaQuote
     * @return DigitalFemsaQuoteInterface
     */
    public function save(DigitalFemsaQuoteInterface $femsaQuote);
}
