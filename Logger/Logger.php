<?php

namespace DigitalFemsa\Payments\Logger;

use DigitalFemsa\Payments\Helper\Data;
use Exception;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger =  $logger;
    }

    /**
     * @param int $level
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function addRecord(int $level, string $message, array $context = []): bool
    {
        $objectManager = ObjectManager::getInstance();
        $digitalFemsaHelper = $objectManager->create(Data::class);

        if ((int)$digitalFemsaHelper->getConfigData('digitalfemsa/femsa_global', 'debug')) {
            return $this->logger->addRecord($level, $message, $context);
        }

        return true;
    }

    /**
     * @param string $string
     * @param array $customerRequest
     * @return void
     */
    public function info(string $string, array $customerRequest = []): void
    {
        $this->logger->info($string, $customerRequest);
    }

    /**
     * @param string $string
     * @param array $array
     * @return void
     */
    public function error(string $string, array $array = []): void
    {
        $this->logger->error($string, $array);
    }

    /**
     * @param string $message
     * @param array $array
     * @return void
     */
    public function debug(string $message, array $array = [])
    {
        $this->logger->debug($message, $array);
    }

    /**
     * @param Exception $e
     * @param array $orderParams
     * @return void
     */
    public function critical(Exception $e, array $orderParams)
    {
        $this->logger->critical($e->getMessage(), $orderParams);
    }
}
