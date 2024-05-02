<?php

namespace DigitalFemsa\Payments\Model;

use DigitalFemsa\Payments\Api\DigitalFemsaApiClient;
use DigitalFemsa\Payments\Helper\Data as DigitalFemsaFemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validator\Exception;

class Config
{

    /**
     * @var DigitalFemsaFemsaHelper
     */
    protected DigitalFemsaFemsaHelper $_digitalFemsaHelper;
    /**
     * @var DigitalFemsaLogger
     */
    private DigitalFemsaLogger $_femsaLogger;

    /**
     * @var DigitalFemsaApiClient
     */
    protected DigitalFemsaApiClient $femsaApiClient;

    /**
     * @param DigitalFemsaFemsaHelper $digitalFemsaHelper
     * @param DigitalFemsaLogger $digitalFemsaLogger
     * @param DigitalFemsaApiClient $femsaApiClient
     */
    public function __construct(
        DigitalFemsaFemsaHelper    $digitalFemsaHelper,
        DigitalFemsaLogger    $digitalFemsaLogger,
        DigitalFemsaApiClient $femsaApiClient
    )
    {
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
        $this->_femsaLogger = $digitalFemsaLogger;
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
        $urlWebhook = $this->_digitalFemsaHelper->getUrlWebhookOrDefault();
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
