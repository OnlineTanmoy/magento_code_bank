<?php
namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Create extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    public $product;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quote;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    public $quoteManagement;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var \Magento\Sales\Model\Service\OrderService
     */
    public $orderService;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    public $_addressFactory;

    /**
     * @var \Appseconnect\ServiceRequest\Model\RequestPostFactory
     */
    public $serviceRequestPostFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Appseconnect\ServiceRequest\Model\RequestPostFactory $serviceRequestPostFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->product = $product;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->_addressFactory = $addressFactory;
        $this->serviceRequestPostFactory = $serviceRequestPostFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $serviceRequestId = $this->getRequest()->getParam('id');
        $serviceRequest = $this->serviceRequestPostFactory->create()->load($serviceRequestId);
        $customer=$this->customerFactory->create();
        $customer->load($serviceRequest->getCustomerId());// load customet by email address

        //billing
        $billingAddressId = $customer->getDefaultBilling();
        $billingAddress = $this->_addressFactory->create()->load($billingAddressId);


        //shipping
        $shippingAddressId = $customer->getDefaultShipping();
        $shippingAddress = $this->_addressFactory->create()->load($shippingAddressId);

        $store = $customer->getStore();


        $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer

        //add items in quote

        $product=$this->product->load(1051);
        $product->setPrice(199);
        $quote->addProduct(
            $product,
            intval(1)
        );





        //Set Address to quote
        $quote->getBillingAddress()->addData($billingAddress->getData());
        $quote->getShippingAddress()->addData($shippingAddress->getData());

        // Collect Rates and Set Shipping & Payment Method

        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('tablerate_bestway'); //shipping method
        $quote->setPaymentMethod('creditlimit'); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        $quote->save(); //Now Save quote and your quote is ready

        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'creditlimit']);

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);

        $order->setEmailSent(0);
        $increment_id = $order->getRealOrderId();

        $this->messageManager->addSuccessMessage(__('You have successfully generate order ['.$increment_id.'] for this service request.'));
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;

    }
}
