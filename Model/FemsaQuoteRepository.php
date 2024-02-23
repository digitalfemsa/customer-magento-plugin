<?php
namespace Femsa\Payments\Model;

use Femsa\Payments\Api\FemsaQuoteRepositoryInterface;
use Femsa\Payments\Api\Data\FemsaQuoteInterface;
use Femsa\Payments\Model\ResourceModel\FemsaQuote as FemsaQuoteResource;
use Magento\Framework\Exception\NoSuchEntityException;

class FemsaQuoteRepository implements FemsaQuoteRepositoryInterface
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
     * @return FemsaQuoteInterface
     * @throws NoSuchEntityException
     */
    public function getById($id): FemsaQuoteInterface
    {
        $femsaQuote = $this->femsaQuoteFactory->create();
        $this->femsaQuoteResource->load($femsaQuote, $id);
        if (!$femsaQuote->getId()) {
            throw new NoSuchEntityException(__('Unable to find femsa quote with ID "%1"', $id));
        }
        return $femsaQuote;
    }

    public function save(FemsaQuoteInterface $femsaQuote): FemsaQuoteInterface
    {
        $this->femsaQuoteResource->save($femsaQuote);
        return $femsaQuote;
    }
}
