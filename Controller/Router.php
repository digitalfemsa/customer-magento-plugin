<?php

namespace DigitalFemsa\Payments\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use DigitalFemsa\Payments\Helper\Data as DigitalFemsaHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Url;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected ActionFactory $actionFactory;

    /**
     * @var ResponseInterface
     */
    protected ResponseInterface $_response;

    /**
     * @var DigitalFemsaHelper
     */
    private DigitalFemsaHelper $_digitalFemsaHelper;


    /**
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param DigitalFemsaHelper $digitalFemsaHelper
     */
    public function __construct(
        ActionFactory       $actionFactory,
        ResponseInterface   $response,
        DigitalFemsaHelper  $digitalFemsaHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->_digitalFemsaHelper = $digitalFemsaHelper;
    }

    /**
     * Validate and Match
     *
     * @param RequestInterface $request
     * @throws NoSuchEntityException
     */
    public function match(RequestInterface $request)
    {
        if ($request->getModuleName() === 'digitalfemsa') {
            return;
        }
        
        $pathRequest = trim($request->getPathInfo(), '/');

        $urlWebhook = $this->_digitalFemsaHelper->getUrlWebhookOrDefault();
        $urlWebhook = trim($urlWebhook, '/');
        $pathWebhook = substr($urlWebhook, -strlen($pathRequest));

        //If paths are identical, then redirects to webhook controller
        if ($pathRequest === $pathWebhook) {
            $request->setModuleName('digitalfemsa')->setControllerName('webhook')->setActionName('index');
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $pathRequest);
        }
    }
}
