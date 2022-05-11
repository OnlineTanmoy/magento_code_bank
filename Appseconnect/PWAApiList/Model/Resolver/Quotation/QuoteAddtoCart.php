<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Quotation;

use Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\Item\Search\Grid\Renderer\Product;
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
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Catalog\Model\Product as CatalogProduct;
use Magento\Catalog\Model\Product\Type as ProductType;

/**
 * Add Quote Items resolver
 */
class QuoteAddtoCart implements ResolverInterface
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
     * @var \Appseconnect\B2BMage\Model\CustomCart
     */
    public $customCart;

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
     * Product
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    protected $formKey;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * View Quote Items constructor.
     *
     * @param Context $context
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param FormKey $formKey
     * @param \Appseconnect\B2BMage\Model\CustomCart $customCart
     * @param QuotationRepositoryInterface $quotationRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory,
        ProductRepositoryInterface $productRepository,
        FormKey $formKey,
        \Appseconnect\B2BMage\Model\CustomCart $customCart,
        QuotationRepositoryInterface $quotationRepository,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        CartRepositoryInterface $quoteRepository
    )
    {
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->quoteFactory = $quoteFactory;
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;
        $this->customCart = $customCart;
        $this->quotationRepository = $quotationRepository;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->quoteRepository = $quoteRepository;
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
    )
    {
        $currentCustomerId = $context->getUserId();
        $customer = $this->customerFactory->create()->load($currentCustomerId);

        if ($currentCustomerId) {
            if ($this->helperContactPerson->isContactPerson($customer)) {
                $quote = $this->quotationRepository->get($args['input']['quote_id']);
                $cartQuote = $this->quoteRepository->getForCustomer($currentCustomerId);
                $quoteItem = $quote->getItemsCollection();

                if ($quote->getStatus() == 'approved') {
                    foreach ($quoteItem as $item) {
                        $product = $this->productRepository->get($item->getProductSku());

                        $newQuoteItem = $this->buildQuoteItem(
                            $item->getProductSku(),
                            (float)$item->getQty(),
                            (int)$cartQuote->getId()
                        );

                        $result = $cartQuote->addProduct($product, $this->prepareAddItem(
                            $product,
                            $newQuoteItem
                        ));
                        if (is_string($result)) {
                            throw new GraphQlInputException(new Phrase($result));
                        }

                        $this->quoteRepository->save($cartQuote);

                        $cartQuote = $this->quoteRepository->getActive($cartQuote->getId());
                        $cartQuote->setTotalsCollectedFlag(false)->collectTotals();
                        $this->quoteRepository->save($cartQuote);

                        $cartPrice[$result->getId()] = $item->getPrice();

                    }

                    foreach ($cartQuote->getAllItems() as $cartitem) {
                        if (isset($cartPrice[$cartitem->getId()])) {
                            $cartitem->setCustomPrice($cartPrice[$cartitem->getId()]);
                            $cartitem->setOriginalCustomPrice($cartPrice[$cartitem->getId()]);
                            $cartitem->getProduct()->setIsSuperMode(true);
                            $cartitem->save();
                        }

                        $cartQuoteLast = $this->quoteRepository->getActive($cartQuote->getId());
                        $cartQuoteLast->setTotalsCollectedFlag(false)->collectTotals();
                        $this->quoteRepository->save($cartQuoteLast);
                    }


                    $status = 'Product added to cart';

                } else {
                    $status = "Product can't added to cart";
                }

                $data[] = [
                    'status' => $status,
                ];

                return ['status' => $status];
            } else {
                throw new GraphQlInputException(__("Access Denied"));
            }
        } else {
            throw new GraphQlInputException(__("Access Denied"));
        }
    }

    private function prepareAddItem(CatalogProduct $product, array $options): DataObject
    {
        if (isset($options['product_option']['buy_request'])) {
            $data = $this->getOptionsFromBuyRequest($product, $options);
        } else {
            $data = $this->getOptionsFromExtensions($product, $options);
        }

        $request = new DataObject();
        $request->setData($data);

        return $request;
    }

    protected function buildQuoteItem(string $sku, float $qty, int $quoteId, array $options = []): array
    {
        return [
            'quantity' => $qty,
            'sku' => $sku,
            'quote_id' => $quoteId,
            'product_option' => $options
        ];
    }

    /**
     * @param Product $product
     * @param array $options
     * @return array
     */
    private function getOptionsFromExtensions(CatalogProduct $product, array $options): array
    {
        $options = $this->prepareOptions($options);
        $data = [
            'product' => $product->getEntityId(),
            'qty' => $options['quantity']
        ];

        switch ($product->getTypeId()) {
            case ProductType::TYPE_SIMPLE:
            case ProductType::TYPE_VIRTUAL:
            case Configurable::TYPE_CODE:
                $this->setCustomizableOptions($options, $data);
                $data = $this->setConfigurableRequestOptions($options, $data);
                break;
            case Type::TYPE_CODE:
                $data = $this->setBundleRequestOptions($options, $data);
                break;
            case DownloadableType::TYPE_DOWNLOADABLE:
                $data = $this->setDownloadableRequestLinks($options, $data);
                break;
        }

        return $data;
    }

    /**
     * @param array $options
     * @return array
     */
    private function prepareOptions(array $options): array
    {
        if (isset ($options['product_option']['extension_attributes']['configurable_item_options'])) {
            $configurableOptions = &$options['product_option']['extension_attributes']['configurable_item_options'];
            $stringifiedOptionValues = array_map(function ($item) {
                $item['option_value'] = (string)$item['option_value'];
                return $item;
            }, $configurableOptions);
            $configurableOptions = $stringifiedOptionValues;
        }

        return $options;
    }

    /**
     * @param string $request
     * @param Product $product
     * @param array $options
     * @return array
     */
    private function getOptionsFromBuyRequest(CatalogProduct $product, array $options): array
    {
        $request = $options['product_option']['buy_request'];
        $data = json_decode($request, true);
        $data['product'] = $product->getEntityId();
        $data['qty'] = $options['quantity'];

        return $data;
    }

    /**
     * @param array $options
     * @param array $data
     * @return array
     */
    private function setConfigurableRequestOptions(array $options, array $data): array
    {
        $configurableOptions = $options['product_option']['extension_attributes']['configurable_item_options'] ?? [];
        $superAttributes = [];

        foreach ($configurableOptions as $option) {
            $superAttributes[$option['option_id']] = $option['option_value'];
        }

        $data['super_attribute'] = $superAttributes;
        return $data;
    }

    /**
     * @param array $options
     * @param array $data
     */
    private function setCustomizableOptions(array $options, array &$data): void
    {
        $customizableOptionsData = $options['product_option']['extension_attributes']['customizable_options'] ?? [];
        $customizableOptions = $this->getCustomizableOptions($customizableOptionsData);
        // Necessary for multi selections, i.e., checkboxes which have same parent option_id
        $customizableOptionsArrayData = $options['product_option']['extension_attributes']['customizable_options_multi'] ?? [];
        $customizableOptionsMulti = $this->getCustomizableOptions($customizableOptionsArrayData, true);

        if (count($customizableOptions)) {
            foreach ($customizableOptions as $key => $value) {
                $data['options'][$key] = $value;
            }
        }

        if (count($customizableOptionsMulti)) {
            foreach ($customizableOptionsMulti as $key => $value) {
                $data['options'][$key] = $value;
            }
        }
    }

    /**
     * @param $customizableOptions
     * @param bool $isMulti
     * @return array
     */
    private function getCustomizableOptions($customizableOptions, $isMulti = false): array
    {
        $data = [];

        if (count($customizableOptions)) {
            foreach ($customizableOptions as $customizableOption) {
                if ($isMulti) {
                    $data[$customizableOption['option_id']][] = $customizableOption['option_value'];
                } else {
                    $data[$customizableOption['option_id']] = $customizableOption['option_value'];
                }
            }
        }

        return $data;
    }

    /**
     * @param array $options
     * @param array $data
     * @return array
     */
    private function setBundleRequestOptions(array $options, array $data): array
    {
        $data['bundle_option'] = [];
        $data['bundle_option_qty'] = [];
        $bundleOptions = $options['product_option']['extension_attributes']['bundle_options'] ?? [];

        foreach ($bundleOptions as $bundleOption) {
            $optionId = $bundleOption['id'];
            $data['bundle_option'][$optionId][] = $bundleOption['value'];
            $data['bundle_option_qty'][$optionId] = $bundleOption['quantity'];
        }

        return $data;
    }

    /**
     * @param array $options
     * @param array $data
     * @return array
     */
    private function setDownloadableRequestLinks(array $options, array $data): array
    {
        $data['links'] = [];
        $linkOptions = $options['product_option']['extension_attributes']['downloadable_product_links'] ?? [];
        foreach ($linkOptions as $link) {
            $data['links'][] = $link['link_id'];
        }
        return $data;
    }
}
