<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Quotation;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\Action\Context;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;
use Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterfaceFactory;
use Appseconnect\B2BMage\Api\Quotation\QuotationItemRepositoryInterfaceFactory;

/**
 * Add Quote Items resolver
 */
class AddQuotation implements ResolverInterface
{
    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var \Appseconnect\B2BMage\Model\QuoteFactory
     */
    public $quoteFactory;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;
    /**
     * @var \Appseconnect\B2BMage\Model\QuoteProductFactory
     */
    public $quoteProduct;

    /**
     * @var QuotationRepositoryInterface
     */
    public $quotationRepository;
    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    public $storeManager;
    /**
     *
     * @var QuoteProductInterfaceFactory
     */
    public $quoteProductInterfaceFactory;
    /**
     *
     * @var QuotationItemRepositoryInterfaceFactory
     */
    public $quotationItemRepositoryInterfaceFactory;

    protected $formKey;

    /**
     * View Quote Items constructor.
     *
     * @param Context $context
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param \Appseconnect\B2BMage\Model\QuoteProductFactory $quoteProduct
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory
     * @param ProductRepositoryInterface $productRepository
     * @param FormKey $formKey
     * @param QuotationRepositoryInterface $quotationRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param QuoteProductInterfaceFactory $quoteProductInterfaceFactory
     * @param QuotationItemRepositoryInterfaceFactory $quotationItemRepositoryInterfaceFactory
     */
    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Model\QuoteProductFactory $quoteProduct,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory,
        ProductRepositoryInterface $productRepository,
        FormKey $formKey,
        QuotationRepositoryInterface $quotationRepository,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        QuoteProductInterfaceFactory $quoteProductInterfaceFactory,
        QuotationItemRepositoryInterfaceFactory $quotationItemRepositoryInterfaceFactory
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->quoteFactory = $quoteFactory;
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;
        $this->quotationRepository = $quotationRepository;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->quoteProductInterfaceFactory = $quoteProductInterfaceFactory;
        $this->quoteProduct =$quoteProduct;
        $this->quotationItemRepositoryInterfaceFactory = $quotationItemRepositoryInterfaceFactory;
    }


    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $currentCustomerId = $context->getUserId();
        $customer = $this->customerFactory->create()->load($currentCustomerId);

        if ($currentCustomerId) {
            if ($this->helperContactPerson->isContactPerson($customer)) {
                $customerId = $this->helperContactPerson->getContactCustomerId($currentCustomerId);

                if (isset($args['input']['product_id'])) {
                    $product = $this->productRepository->getById($args['input']['product_id']);
                    if ($product->getData()) {
                        $args['input']['product_id'] = $args['input']['product_id'];
                    } else {
                        throw new GraphQlInputException(__("Product ID doesn't exist"));
                    }
                } else {
                    throw new GraphQlInputException(__('Product ID should be specified'));
                }

                if (!isset($args['input']['qty'])) {
                    throw new GraphQlInputException(__('Quantity should be specified'));
                } else {
                    $args['input']['qty'] = $args['input']['qty'];
                }

                if (!isset($args['input']['store_id'])) {
                    throw new GraphQlInputException(__('Store id should be specified'));
                } else {
                    $args['input']['store_id'] = $args['input']['store_id'];
                }

                $contactPerson = $this->customerRepository->getById($currentCustomerId);
                try {
                    $quote = $this->quotationRepository->getForContact($currentCustomerId);
                } catch (\Exception $e) {
                    $quote = $this->quoteFactory->create();
                    $quote->setCreatedAt(date("Y-m-d H:i:s"));
                    $quote->setStatus('open');
                    $quote->setStore($this->storeManager->getStore());
                    $quote->setCustomer($contactPerson);
                    $quote->setCustomerIsGuest(0);
                    $this->quotationRepository->save($quote);
                }
                if ($quote && $args['input']['qty'] == 0) {
                    $quoteProductItem = $this->quoteProduct->create()
                        ->getCollection()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('product_id', $args['input']['product_id'])
                        ->addFieldToFilter('quote_id', $quote->getId())
                        ->getFirstItem();
                    if (!$quoteProductItem->getData()) {
                        throw new GraphQlInputException(__('Please enter a quantity greater than 0.'));
                    }
                    $quote->removeItem($quoteProductItem->getId())->save();
                    if($quote->getGrandTotal()!=$quoteProductItem->getPrice()*$quoteProductItem->getQty()){
                        $quote->setGrandTotal($quote->getGrandTotal()-($quoteProductItem->getPrice()*$quoteProductItem->getQty()));
                        $this->quotationRepository->save($quote);
                    }

                } elseif ($args['input']['qty'] == 0) {
                    throw new GraphQlInputException(__('Please enter a quantity greater than 0.'));
                } else {
                    $quoteItems['quote_id'] = $quote->getId();
                    $quoteItems['customer_id'] = $customerId;
                    $quoteItems['product_id'] = $args['input']['product_id'];
                    $quoteItems['product_sku'] = $product->getSku();
                    $quoteItems['qty'] = $args['input']['qty'];
                    $quoteItems['store_id'] = $contactPerson->getStoreId();

                    $quoteItemsData = $this->quoteProductInterfaceFactory->create()
                        ->setQuoteId($quote->getId())
                        ->setCustomerId($customerId)
                        ->setProductId($args['input']['product_id'])
                        ->setProductSku($product->getSku())
                        ->setQty($args['input']['qty'])
                        ->setStoreId($contactPerson->getStoreId());

                    $this->quotationItemRepositoryInterfaceFactory->create()->save($quoteItemsData);

                }
                $quoteModel = $this->quoteFactory->create()->load($quote->getId());

                $quoteId = null;
                $status = null;
                $createdAt = null;
                $createdBy = null;
                $quoteTotal = null;
                if (isset($quoteModel)) {
                    $status = $quoteModel->getStatus();
                    $createdAt = $quoteModel->getCreatedAt();
                    $createdBy = $quoteModel->getContactName();
                    $quoteId = $quoteModel->getId();
                    $quoteTotal = $quoteModel->getBaseGrandTotal();
                }

                $data[] = [
                    'quote_id' => $quoteId,
                    'status' => $status,
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                    'quote_total' => $quoteTotal
                ];

                return ['quotationdata' => $data];
            } else {
                throw new GraphQlInputException(__("Access Denied"));
            }
        } else {
            throw new GraphQlInputException(__("Access Denied"));
        }
    }
}
