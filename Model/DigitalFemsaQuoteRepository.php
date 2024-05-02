<?php
namespace DigitalFemsa\Payments\Model;

use DigitalFemsa\Payments\Api\DigitalFemsaQuoteRepositoryInterface;
use DigitalFemsa\Payments\Api\Data\DigitalFemsaQuoteInterface;
use DigitalFemsa\Payments\Model\ResourceModel\DigitalFemsaQuote as FemsaQuoteResource;
use Magento\Framework\Exception\NoSuchEntityException;

class DigitalFemsaQuoteRepository implements DigitalFemsaQuoteRepositoryInterface
{
    /**
     * @var FemsaQuoteFactory
     */
    private FemsaQuoteFactory $femsaQuoteFactory;
    /**
     * @var FemsaQuoteResource
     */
    private  $femsaQuoteResource;

    /**
     * @param FemsaQuoteFactory $femsaQuoteFactory
     * @param FemsaQuoteResource $femsaQuoteResource
     */
    public function __construct(
        FemsaQuoteFactory $femsaQuoteFactory,
        FemsaQuoteResource $femsaQuoteResource
    ) {
        $this->femsaQuoteFactory = $femsaQuoteFactory;
        $this->femsaQuoteResource = $femsaQuoteResource;
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
        $femsaQuote = $this->femsaQuoteFactory->create();
        $this->femsaQuoteResource->load($femsaQuote, $id);
        if (!$femsaQuote->getId()) {
            throw new NoSuchEntityException(__('Unable to find femsa quote with ID "%1"', $id));
        }
        return $femsaQuote;
    }

    public function save(DigitalFemsaQuoteInterface $femsaQuote): DigitalFemsaQuoteInterface
    {
        $this->femsaQuoteResource->save($femsaQuote);
        return $femsaQuote;
    }
}
