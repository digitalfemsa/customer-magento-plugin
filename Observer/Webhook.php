<?php

namespace DigitalFemsa\Payments\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use DigitalFemsa\Payments\Model\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Validator\Exception;
use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;

/**
 * Class CreateWebhook
 */
class Webhook implements ObserverInterface
{
    /**
     * @var DigitalFemsaHelper
     */
    protected DigitalFemsaHelper $_digitalFemsaHelper;
    /**
     * @var Config
     */
    protected Config $config;
    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;

    /**
     * @param Config $config
     * @param ManagerInterface $messageManager
     * @param DigitalFemsaHelper $digitalFemsaHelper
     */
    public function __construct(
        Config $config,
        ManagerInterface $messageManager,
        DigitalFemsaHelper    $digitalFemsaHelper
    ) {
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
    }

    /**
     * Create Webhook
     *
     * @param Observer $observer
     * @throws Exception
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
         if (!$this->_digitalFemsaHelper->isCashEnabled()) {
            return;
         }
         $this->config->createWebhook();
    }
}
