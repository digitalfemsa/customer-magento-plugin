<?php

namespace DigitalFemsa\Payments\Service;

use DigitalFemsa\Payments\Api\DigitalFemsaApiClient;
use DigitalFemsa\Payments\Helper\Data as DigitalFemsaData;
use DigitalFemsa\Payments\Helper\Util;
use DigitalFemsa\Payments\Logger\Logger as DigitalFemsaLogger;
use DigitalFemsa\Payments\Model\Ui\EmbedForm\ConfigProvider;
use DigitalFemsa\Payments\Model\WebhookRepository;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Customer\Model\CustomerFactory;
use Exception;

class MissingOrders
{
    /**
     * @var WebhookRepository
     */
    private WebhookRepository $webhookRepository;

    private DigitalFemsaLogger $_digitalFemsaLogger;
    private StoreManagerInterface $_storeManager;

    private QuoteFactory $quote;
    /**
     * @var DigitalFemsaData|mixed
     */
    private Util $utilHelper;
    private Product $_product;
    private CustomerFactory $customerFactory;
    private CustomerRepositoryInterface $customerRepository;
    private QuoteManagement $quoteManagement;
    private DigitalFemsaApiClient $femsaApiClient;

    public function __construct(
        WebhookRepository           $webhookRepository,
        DigitalFemsaLogger          $digitalFemsaLogger,
        StoreManagerInterface       $storeManager,
        QuoteFactory                $quote,
        Product                     $product,
        CustomerFactory             $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        QuoteManagement             $quoteManagement,
        DigitalFemsaApiClient       $femsaApiClient
    ){
        $this->webhookRepository = $webhookRepository;
        $this->_digitalFemsaLogger = $digitalFemsaLogger;
        $this->_storeManager = $storeManager;
        $this->quote = $quote;
        $this->_product = $product;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->quoteManagement = $quoteManagement;
        $this->femsaApiClient = $femsaApiClient;

        $objectManager = ObjectManager::getInstance();
        $this->utilHelper = $objectManager->create(DigitalFemsaData::class);
    }

    /**
     * @throws LocalizedException
     */
    public function recover_order($event){
        try {
            //check order en order with external id
            $femsaOrderFound = $this->webhookRepository->findByMetadataOrderId($event);

            if ($femsaOrderFound->getId() != null || !empty($femsaOrderFound->getId()) ) {
                $this->_digitalFemsaLogger->info('order is ready', ['order' => $femsaOrderFound, 'is_set', isset($femsaOrderFound)]);
                return;
            }
            $femsaOrder = $event['data']['object'];
            $femsaCustomer = $femsaOrder['customer_info'];
            $metadata = $femsaOrder['metadata'];

            $store = $this->_storeManager->getStore(intval($metadata["store"]));

            $quoteCreated=$this->quote->create(); //Create object of quote

            $quoteCreated->setStore($store); //set store for which you create quote
            $quoteCreated->setIsVirtual($metadata[CartInterface::KEY_IS_VIRTUAL]);

            $quoteCreated->setCurrency();
            $customerName = $this->utilHelper->splitName($femsaCustomer['name']);

            $quoteCreated->setCustomerEmail($femsaCustomer['email']);
            $quoteCreated->setCustomerFirstname($customerName["firstname"]);
            $quoteCreated->setCustomerLastname($customerName["lastname"]);
            $quoteCreated->setCustomerIsGuest(true);
            if (isset($femsaCustomer['customer_custom_reference']) && !empty($femsaCustomer['customer_custom_reference'])){
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($store->getWebsiteId());
                $customer->load($femsaCustomer['customer_custom_reference']);// load customer by id
                $this->_digitalFemsaLogger->info('end customer', ['email' =>$femsaCustomer['email'] ]);

                $customer= $this->customerRepository->getById($customer->getEntityId());
                $quoteCreated->assignCustomer($customer); //Assign quote to customer
            }


            //add items in quote
            foreach($femsaOrder['line_items']["data"] as $item){
                $product=$this->_product->load($item["metadata"]['product_id']);
                $product->setPrice($this->utilHelper->convertFromApiPrice($item['unit_price']));
                $quoteCreated->addProduct(
                    $product,
                    intval($item['quantity'])
                );
            }

            $shippingNameReceiver = $this->utilHelper->splitName($femsaOrder["shipping_contact"]["receiver"]);
            $shipping_address = [
                'firstname'    => $shippingNameReceiver["firstname"],
                'lastname'     => $shippingNameReceiver["lastname"],
                'street' => [ $femsaOrder["shipping_contact"]["address"]["street1"], $femsaOrder["shipping_contact"]["address"]["street2"] ?? ""],
                'city' => $femsaOrder["shipping_contact"]["address"]["city"],
                'country_id' => strtoupper($femsaOrder["fiscal_entity"]["address"]["country"]),
                'region' => $femsaOrder["shipping_contact"]["address"]["state"],
                'postcode' => $femsaOrder["shipping_contact"]["address"]["postal_code"],
                'telephone' =>  $femsaOrder["shipping_contact"]["phone"],
                'save_in_address_book' => intval( $femsaOrder["shipping_contact"]["metadata"]["save_in_address_book"]),
                'region_id' => $femsaOrder["shipping_contact"]["metadata"]["region_id"],
                'company'  => $femsaOrder["shipping_contact"]["metadata"]["company"],
            ];
            $billingAddressName = $this->utilHelper->splitName($femsaOrder["fiscal_entity"]["name"]);
            $billing_address = [
                'firstname'    => $billingAddressName["firstname"], //address Details
                'lastname'     => $billingAddressName["lastname"],
                'street' => [ $femsaOrder["fiscal_entity"]["address"]["street1"] , $femsaOrder["fiscal_entity"]["address"]["street2"] ?? "" ],
                'city' => $femsaOrder["fiscal_entity"]["address"]["city"],
                'country_id' => strtoupper($femsaOrder["fiscal_entity"]["address"]["country"]),
                'region' => $femsaOrder["fiscal_entity"]["address"]["state"],
                'postcode' => $femsaOrder["fiscal_entity"]["address"]["postal_code"],
                'telephone' =>  $femsaCustomer["phone"],
                'save_in_address_book' =>  intval($femsaOrder["fiscal_entity"]["metadata"]["save_in_address_book"]),
                'region_id' =>$femsaOrder["fiscal_entity"]["metadata"]["region_id"],
                'company'  => $femsaOrder["fiscal_entity"]["metadata"]["company"]
            ];

            //Set Address to quote
            $quoteCreated->getBillingAddress()->addData($billing_address);

            $quoteCreated->getShippingAddress()->addData($shipping_address);

            // Collect Rates and Set Shipping & Payment Method
            $shippingAddress=$quoteCreated->getShippingAddress();

            $femsaShippingLines = $femsaOrder["shipping_lines"]["data"];

            $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingAmount($this->utilHelper->convertFromApiPrice($femsaShippingLines[0]["amount"]))
                ->setShippingMethod($femsaShippingLines[0]["method"]);

            $this->_digitalFemsaLogger->info('end $femsaShippingLines');


            //discount lines
            if (isset($femsaOrder["discount_lines"]) && isset($femsaOrder["discount_lines"]["data"])) {
                $quoteCreated->setCustomDiscount($this->getDiscountAmount($femsaOrder["discount_lines"]["data"]));
                $this->applyCoupon($femsaOrder["discount_lines"]["data"],$quoteCreated);
            }

            $quoteCreated->setPaymentMethod(ConfigProvider::CODE);
            $quoteCreated->setInventoryProcessed(false);
            $quoteCreated->save();
            $this->_digitalFemsaLogger->info('end save quote');


            // Set Sales Order Payment
            $quoteCreated->getPayment()->importData(['method' => ConfigProvider::CODE]);
            $additionalInformation = [
                'order_id' =>  $femsaOrder["id"],
                'txn_id' =>  $femsaOrder["charges"]["data"][0]["id"],
                'quote_id'=> $quoteCreated->getId(),
                'payment_method' => $this->getPaymentMethod($femsaOrder["charges"]["data"][0]["payment_method"]["object"]),
                'digitalfemsa_customer_id' => $femsaCustomer["customer_id"]
            ];
            $quoteCreated->getPayment()->setAdditionalInformation(   $additionalInformation);
            // Collect Totals & Save Quote
            $quoteCreated->collectTotals()->save();
            $this->_digitalFemsaLogger->info('Collect Totals & Save Quote');

            // Create Order From Quote
            $order = $this->quoteManagement->submit($quoteCreated);
            $this->_digitalFemsaLogger->info('end submit');


            $increment_id = $order->getRealOrderId();
            if (isset($metadata['remote_ip']) && $metadata['remote_ip']!=null) {
                $order->setRemoteIp($metadata['remote_ip'])->save();
            }
            $order->addCommentToStatusHistory("Missing Order from femsa ". "<a href='". ConfigProvider::URL_PANEL_PAYMENTS ."/".$femsaOrder["id"]. "' target='_blank'>".$femsaOrder["id"]."</a>")
                ->setIsCustomerNotified(true)
                ->save();
            $this->updateFemsaReference($femsaOrder["charges"]["data"][0]["id"],  $increment_id);

        } catch (Exception $e) {
            $this->_digitalFemsaLogger->error('creating order '.$e->getMessage());
            throw  $e;
        }
    }

    private function getAdditionalInformation(array $femsaOrder) :array{
        return [];
    }
    private function updateFemsaReference(string $chargeId, string $orderId){
        $chargeUpdate= [
            "reference_id"=> $orderId,
        ];
        try {
            $this->femsaApiClient->updateCharge($chargeId,  $chargeUpdate);
        }catch (Exception $e) {
            $this->_digitalFemsaLogger->error("updating femsa charge". $e->getMessage(), ["charge_id"=> $chargeId, "reference_id"=> $orderId]);
        }
    }
    private function applyCoupon(array $discountLines, Quote $quote)  {
        foreach ($discountLines as $discountLine){
            if ($discountLine["type"] == "coupon"){
                $quote->setCouponCode($discountLine["code"]);
            }
        }
    }

    private function getDiscountAmount(array $discountLines) :float {
        $discountValue = 0;
        foreach ($discountLines as $discountLine){
            $discountValue += $this->utilHelper->convertFromApiPrice($discountLine["amount"]);
        }
        return $discountValue * -1;
    }

    private function getPaymentMethod(string $type) :string {
        if ($type == "cash_payment") {
            return ConfigProvider::PAYMENT_METHOD_CASH;
        }
        return "";
    }
}
