<?php
namespace DigitalFemsa\Payments\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private TransferBuilder $transferBuilder;

    private DigitalFemsaLogger $_digitalFemsaLogger;

    /**
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        DigitalFemsaLogger $digitalFemsaLogger
    ) {
        $this->_digitalFemsaLogger = $digitalFemsaLogger;
        $this->_digitalFemsaLogger->info('HTTP TransferFactory :: __construct');

        $this->transferBuilder = $transferBuilder;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $request
     * @return TransferInterface
     */
    public function create(array $request)
    {
        $this->_digitalFemsaLogger->info('HTTP TransferFactory :: create');

        return $this->transferBuilder
            ->setBody($request)
            ->build();
    }
}
