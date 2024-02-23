<?php
namespace Femsa\Payments\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Femsa\Payments\Logger\Logger as FemsaLogger;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private TransferBuilder $transferBuilder;

    private FemsaLogger $_femsaLogger;

    /**
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        FemsaLogger $femsaLogger
    ) {
        $this->_femsaLogger = $femsaLogger;
        $this->_femsaLogger->info('HTTP TransferFactory :: __construct');

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
        $this->_femsaLogger->info('HTTP TransferFactory :: create');

        return $this->transferBuilder
            ->setBody($request)
            ->build();
    }
}
