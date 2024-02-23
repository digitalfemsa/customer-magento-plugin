<?php

namespace Femsa\Payments\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Femsa\Payments\Helper\Data as FemsaHelper;
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
     * @var FemsaHelper
     */
    private FemsaHelper $_femsaHelper;


    /**
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param FemsaHelper $femsaHelper
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        FemsaHelper $femsaHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->_femsaHelper = $femsaHelper;
    }

    /**
     * Validate and Match
     *
     * @param RequestInterface $request
     * @throws NoSuchEntityException
     */
    public function match(RequestInterface $request)
    {
        if ($request->getModuleName() === 'femsa') {
            return;
        }
        
        $pathRequest = trim($request->getPathInfo(), '/');

        $urlWebhook = $this->_femsaHelper->getUrlWebhookOrDefault();
        $urlWebhook = trim($urlWebhook, '/');
        $pathWebhook = substr($urlWebhook, -strlen($pathRequest));

        //If paths are identical, then redirects to webhook controller
        if ($pathRequest === $pathWebhook) {
            $request->setModuleName('femsa')->setControllerName('webhook')->setActionName('index');
            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $pathRequest);
        }
    }
}
