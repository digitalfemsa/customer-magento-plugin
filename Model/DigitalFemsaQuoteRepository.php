<?php
namespace DigitalFemsa\Payments\Model;

use DigitalFemsa\Payments\Api\DigitalFemsaQuoteRepositoryInterface;
use DigitalFemsa\Payments\Api\Data\DigitalFemsaQuoteInterface;
use DigitalFemsa\Payments\Model\ResourceModel\DigitalFemsaQuote as DigitalFemsaQuoteResource;
use Magento\Framework\Exception\NoSuchEntityException;

class DigitalFemsaQuoteRepository implements DigitalFemsaQuoteRepositoryInterface
{
    /**
     * @var DigitalFemsaQuoteFactory
     */
    private DigitalFemsaQuoteFactory $digitalDigitalFemsaQuoteFactory;
    /**
     * @var DigitalFemsaQuoteResource
     */
    private  $digitalFemsaQuoteResource;

    /**
     * @param DigitalFemsaQuoteFactory $digitalDigitalFemsaQuoteFactory
     * @param DigitalFemsaQuoteResource $digitalFemsaQuoteResource
     */
    public function __construct(
        DigitalFemsaQuoteFactory    $digitalDigitalFemsaQuoteFactory,
        DigitalFemsaQuoteResource   $digitalFemsaQuoteResource
    ) {
        $this->digitalDigitalFemsaQuoteFactory = $digitalDigitalFemsaQuoteFactory;
        $this->digitalFemsaQuoteResource = $digitalFemsaQuoteResource;
    }

    /**
     * Get by ID
     *
     * @param mixed $id
     * @return DigitalFemsaQuoteInterface
     * @throws NoSuchEntityException
     */
    public function getById($id): DigitalFemsaQuoteInterface
    {
        $femsaQuote = $this->digitalDigitalFemsaQuoteFactory->create();
        $this->digitalFemsaQuoteResource->load($femsaQuote, $id);
        if (!$femsaQuote->getId()) {
            throw new NoSuchEntityException(__('Unable to find femsa quote with ID "%1"', $id));
        }
        return $femsaQuote;
    }

    public function save(DigitalFemsaQuoteInterface $femsaQuote): DigitalFemsaQuoteInterface
    {
        $this->digitalFemsaQuoteResource->save($femsaQuote);
        return $femsaQuote;
    }
}
