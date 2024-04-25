<?php

namespace DigitalFemsa\Payments\Model;

use DigitalFemsa\Payments\Api\FemsaApiClient;
use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as FemsaLogger;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validator\Exception;

class Config
{

    /**
     * @var FemsaHelper
     */
    protected FemsaHelper $_femsaHelper;
    /**
     * @var FemsaLogger
     */
    private FemsaLogger $_femsaLogger;

    /**
     * @var FemsaApiClient
     */
    protected FemsaApiClient $femsaApiClient;

    /**
     * @param FemsaHelper $femsaHelper
     * @param FemsaLogger $femsaLogger
     * @param FemsaApiClient $femsaApiClient
     */
    public function __construct(
        FemsaHelper    $femsaHelper,
        FemsaLogger    $femsaLogger,
        FemsaApiClient $femsaApiClient
    )
    {
        $this->_femsaHelper = $femsaHelper;
        $this->_femsaLogger = $femsaLogger;
        $this->femsaApiClient = $femsaApiClient;
    }

    /**
     * Create Webhook
     *
     * @return void
     * @throws Exception|NoSuchEntityException
     */
    public function createWebhook()
    {
        $urlWebhook = $this->_femsaHelper->getUrlWebhookOrDefault();
        try {
            $different = true;
            $webhooks = $this->femsaApiClient->getWebhooks();
            $data = $webhooks->getData();
            foreach ($data as $webhook) {
                if (strpos($webhook->getUrl(), $urlWebhook) !== false) {
                    $different = false;
                }
            }
            if ($different) {
                $this->femsaApiClient->createWebhook([
                    'url' => $urlWebhook
                ]);
            } else {
                $this->_femsaLogger->info('[DigitalFemsa]: El webhook ' . $urlWebhook . ' ya se encuentra en DigitalFemsa!');
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->_femsaLogger->info('[DigitalFemsa]: Webhook error, Message: ' . $errorMessage . ' URL: ' . $urlWebhook);

            throw new Exception(
                __('Can not register this webhook ' . $urlWebhook . '<br>'
                    . 'Message: ' . $errorMessage)
            );
        }
    }
}
