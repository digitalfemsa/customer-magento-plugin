<?php

namespace DigitalFemsa\Payments\Model\Source\Order\Status;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Config\Source\Order\Status;

class Pendingpayment extends Status
{
    /**
     * @var array
     */
    protected $_stateStatuses = [Order::STATE_PENDING_PAYMENT];
}
