<?php

namespace DigitalFemsa\Payments\Helper;

use DigitalFemsa\ApiException;
use DigitalFemsa\Payments\Api\FemsaApiClient;
use DigitalFemsa\Payments\Exception\FemsaException;
use DigitalFemsa\Payments\Helper\Data as FemsaHelper;
use DigitalFemsa\Payments\Logger\Logger as FemsaLogger;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\CartInterface;

class FemsaOrder extends Util
{
    /**
     * @var FemsaLogger
     */
    protected FemsaLogger $femsaLogger;

    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;
    /**
     * @var Data
     */
    protected Data $_femsaHelper;
    /**
     * @var Session
     */
    protected Session $_checkoutSession;
    /**
     * @var Quote|null
     */
    protected $quote = null;
    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @var FemsaApiClient
     */
    private FemsaApiClient $femsaApiClient;

    /**
     * FemsaOrder constructor.
     *
     * @param Context $context
     * @param FemsaHelper $femsaHelper
     * @param FemsaLogger $femsaLogger
     * @param FemsaApiClient $femsaApiClient
     * @param CustomerSession $customerSession
     * @param Session $_checkoutSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context                     $context,
        FemsaHelper                 $femsaHelper,
        FemsaLogger                 $femsaLogger,
        FemsaApiClient              $femsaApiClient,
        CustomerSession             $customerSession,
        Session                     $_checkoutSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->femsaApiClient = $femsaApiClient;
        $this->femsaLogger = $femsaLogger;
        $this->customerSession = $customerSession;
        $this->_femsaHelper = $femsaHelper;
        $this->_checkoutSession = $_checkoutSession;
        $this->customerRepository = $customerRepository;
    }


    /**
     * Generate Order Params
     *
     * @param mixed $guestEmail
     * @return array
     * @throws FemsaException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputMismatchException
     */
    public function generateOrderParams($guestEmail): array
    {
        $this->femsaLogger->info('FemsaOrder.generateOrderParams init');

        $customerRequest = [];
        try {
            $customer = $this->customerSession->getCustomer();
            $femsaCustomerId = $customer->getFemsaCustomerId();
            $this->femsaLogger->info('looking customer id', ["femsa_customer_id"=>$femsaCustomerId]);
            if(!empty($femsaCustomerId)) {
                try {
                    $this->femsaApiClient->findCustomerByID($femsaCustomerId);
                } catch (Exception $error) {
                    $this->femsaLogger->info('Create Order. Find Customer: ' . $error->getMessage());
                    $femsaCustomerId = '';
                }
            }

            //Customer Info for API
            $billingAddress = $this->getQuote()->getBillingAddress();
            $customerId = $customer->getId();
            if ($customerId) {
                //name without numbers
                $customerRequest['name'] = $customer->getName();
                $customerRequest['email'] = $customer->getEmail();
            } else {
                //name without numbers
                $customerRequest['name'] = $billingAddress->getName();
                $customerRequest['email'] = $guestEmail;
            }
            $customerRequest['custom_reference'] = $customerId;
            $customerRequest['name'] = $this->removeNameSpecialCharacter($customerRequest['name']);
            $customerRequest['phone'] = $this->removePhoneSpecialCharacter($billingAddress->getTelephone());
            
            if (strlen($customerRequest['phone']) < 10) {
                $this->femsaLogger->info('Helper.CreateOrder phone validation error', $customerRequest);
                throw new FemsaException(__('Télefono no válido. 
                    El télefono debe tener al menos 10 carácteres. 
                    Los caracteres especiales se desestimaran, solo se puede ingresar como 
                    primer carácter especial: +'));
            }
            
            if (empty($femsaCustomerId)) {
                $femsaCustomer = $this->femsaApiClient->createCustomer($customerRequest);
                $femsaCustomerId = $femsaCustomer->getId();
                if ($customerId) {
                    $customer = $this->customerRepository->getById($customerId);
                    $customer->setCustomAttribute('femsa_customer_id', $femsaCustomerId);
                    $this->customerRepository->save($customer);
                }
            } else {
                //If customer API exists, always update error
                $this->femsaApiClient->updateCustomer($femsaCustomerId, $customerRequest);
            }
        } catch (ApiException $e) {
            $this->femsaLogger->info($e->getMessage(), $customerRequest);
            throw new FemsaException(__($e->getMessage()));
        }
        $orderItems = $this->getQuote()->getAllItems();

        $validOrderWithCheckout = [];
        $validOrderWithCheckout['line_items'] = $this->_femsaHelper->getLineItems($orderItems);
        $validOrderWithCheckout['discount_lines'] = $this->_femsaHelper->getDiscountLines();
        $validOrderWithCheckout['tax_lines'] = $this->_femsaHelper->getTaxLines($orderItems);
        $validOrderWithCheckout['shipping_lines'] = $this->_femsaHelper->getShippingLines(
            $this->getQuote()->getId()
        );

        //always needs shipping due to api does not provide info about merchant type (drop_shipping, virtual)
        $validOrderWithCheckout['shipping_contact'] = $this->_femsaHelper->getShippingContact(
            $this->getQuote()->getId()
        );
        $validOrderWithCheckout['fiscal_entity'] = $this->_femsaHelper->getBillingAddress(
            $this->getQuote()->getId()
        );

        $validOrderWithCheckout['customer_info'] = [
            'customer_id' => $femsaCustomerId
        ];
        
        $validOrderWithCheckout['checkout']    = [
            'allowed_payment_methods'      => $this->getAllowedPaymentMethods(),
            'expires_at'                   => $this->_femsaHelper->getExpiredAt(),
            'needs_shipping_contact'       => true
        ];
        $validOrderWithCheckout['currency']= $this->_femsaHelper->getCurrencyCode();
        $validOrderWithCheckout['metadata'] = $this->getMetadataOrder($orderItems);
        
        return $validOrderWithCheckout;
    }



    /**
     * Get allowed payments methods
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAllowedPaymentMethods(): array
    {
        $methods = [];

        $total = $this->getQuote()->getSubtotal();
        if ($this->_femsaHelper->isCashEnabled() &&
            $total <= 10000
        ) {
            $methods[] = 'cash';
        }
        return $methods;
    }

    /**
     * Get active quote
     *
     * @return Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuote(): Quote
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Get quote ID
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuoteId(): array
    {
        $quote = $this->getQuote();
        $quoteId = $quote->getId();
        return ['quote_id' => $quoteId];
    }

    /**
     * Get Metadata Order
     *
     * @param mixed $orderItems
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getMetadataOrder($orderItems): array
    {
        return array_merge(
            $this->_femsaHelper->getMagentoMetadata(),
            [
                'quote_id'                     => $this->getQuote()->getId(),
                 CartInterface::KEY_IS_VIRTUAL => $this->getQuote()->isVirtual()
            ],
            $this->_femsaHelper->getMetadataAttributesFemsa($orderItems)
        );
    }
}
