<?php

namespace DigitalFemsa\Payments\Observer;

use DigitalFemsa\Payments\Logger\Logger;
use DigitalFemsa\Payments\Model\Ui\EmbedForm\ConfigProvider;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class StatusObserver implements ObserverInterface
{
    public Logger $_logger ;

    public function __construct(Logger $logger)
    {
        $this->_logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->_logger->info("execute StatusObserver");
        $order = $observer->getEvent()->getOrder();
        if ($order->getPayment()->getMethod() != ConfigProvider::CODE ) {
            return;
        }

        $paymentMethodFemsa = $order->getPayment()->getAdditionalInformation('payment_method');
        $this->_logger->info("execute paymentMethodFemsa",["paymentMethodFemsa"=> $paymentMethodFemsa]);
        if ($paymentMethodFemsa != ConfigProvider::PAYMENT_METHOD_CASH) {
            return;
        }

        $order->setState(Order::STATE_PENDING_PAYMENT);
        $order->setStatus(Order::STATE_PENDING_PAYMENT);
        $order->save();
    }
}