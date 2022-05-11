<?php
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model\Catalog\Pricing;

use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Customer\Model\Session;
/**
 * Model Render
 *
 * @category Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Render extends \Magento\Catalog\Pricing\Render
{

    public $divisionHelper;

    /**
     * Render constructor.
     *
     * @param Template\Context                                       $context                         context
     * @param Registry                                               $registry                        registry
     * @param Session                                                $session                         session
     * @param CollectionFactory                                      $pricelistPriceCollectionFactory pricelist collection
     * @param \Magento\Customer\Model\CustomerFactory                $customerFactory                 customer
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data        $helperContactPerson             contact person helper
     * @param \Appseconnect\B2BMage\Helper\CategoryDiscount\Data     $helperCategory                  category helper
     * @param \Appseconnect\B2BMage\Helper\Pricelist\Data            $helperPricelist                 pricelist helper
     * @param \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data    $helperTierprice                 tierprice helper
     * @param \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice      customer special price helper
     * @param \Appseconnect\B2BMage\Helper\PriceRule\Data            $helperPriceRule                 price rule helper
     * @param \Magento\Catalog\Model\ProductFactory                  $productFactory                  product
     * @param \Magento\Framework\Pricing\Helper\Data                 $priceHelper                     price helper
     * @param array                                                  $data                            data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Session $session,
        CollectionFactory $pricelistPriceCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        \Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data $helperCustomerSpecialPrice,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $helperPriceRule,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->customerFactory = $customerFactory;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
        $this->customerSession = $session;
        $this->productFactory = $productFactory;
        $this->helperCategory = $helperCategory;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperPricelist = $helperPricelist;
        $this->helperTierprice = $helperTierprice;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperPriceRule = $helperPriceRule;
        $this->priceHelper = $priceHelper;
        $this->divisionHelper = $divisionHelper;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $priceRender = $this->getLayout()->getBlock($this->getPriceRender());
        if ($priceRender instanceof PricingRender) {
            $product = $this->getProduct();
            if ($product instanceof SaleableInterface) {
                $arguments = $this->getData();
                $arguments['render_block'] = $this;

                $item = $product;
                $qtyItem = 1;
                $productId = $item->getEntityId();
                $customerId = $this->customerSession->getCustomer()->getId();
                $customerType = $this->customerSession->getCustomer()->getCustomerType();
                $websiteId = $this->customerSession->getCustomer()->getWebsiteId();

                $customerPricelistCode = $this->customerSession->getCustomer()->getData('pricelist_code');
                if ($customerType == 3) {

                    if ($this->divisionHelper->isParentContact($customerId)) {
                        $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                        $currentCustomerId = $this->customerSession->getCurrentCustomerId();
                        if (isset($currentCustomerId)) {

                            // For division specific discount
                            $divisionCustomerId = $currentCustomerId;
                            $getCustomerDetails = $this->helperPriceRule->getCustomerDetails($divisionCustomerId);
                            $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
                            $customerId = $getCustomerDetails['customerId'];

                        } else {
                            $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
                            $customerPricelistCode = $customerCollection->getData('pricelist_code');
                            $customerId = $customerDetail['customer_id'];
                        }

                    } else {
                        $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                        // For division specific discount
                        $divisionCustomerId = $customerDetail['customer_id'];
                        $getCustomerDetails = $this->helperPriceRule->getCustomerDetails($divisionCustomerId);
                        $customerPricelistCode = $getCustomerDetails['customerPricelistCode'];
                        $customerId = $getCustomerDetails['customerId'];
                    }
                }
                if ($customerType == 2) {
                    return parent::_toHtml();
                }
                $pricelistStatus = null;
                $pricelistCollection = $this->pricelistPriceCollectionFactory->create()
                    ->addFieldToFilter('id', $customerPricelistCode)
                    ->addFieldToFilter('website_id', $websiteId)
                    ->getData();
                if (isset($pricelistCollection[0])) {
                    $pricelistStatus = $pricelistCollection[0]['is_active'];
                }
                $qtyItem = ($qtyItem) ? $qtyItem : 1;
                if ($customerId) {
                    $finalPrice = $this->productFactory->create()
                        ->load($productId)
                        ->getPrice();
                    $pricelistPrice = '';
                    if ($customerPricelistCode && $pricelistStatus) {
                        $pricelistPrice = $this->helperPricelist->getAmount(
                            $productId,
                            $finalPrice,
                            $customerPricelistCode,
                            true
                        );
                    }
                    $categoryIds = $this->productFactory->create()
                        ->load($productId)
                        ->getCategoryIds();
                    $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount(
                        $finalPrice,
                        $customerId,
                        $categoryIds
                    );
                    // for tier price
                    $tierPrice = '';
                    $productSku = $item->getSku();
                    $tierPrice = $this->helperTierprice->getTierprice(
                        $productId,
                        $productSku,
                        $customerId,
                        $websiteId,
                        $qtyItem,
                        $finalPrice
                    );
                    $specialPrice = '';
                    $specialPrice = $this->helperCustomerSpecialPrice->getSpecialPrice(
                        $productId,
                        $productSku,
                        $customerId,
                        $websiteId,
                        $finalPrice
                    );


                    if ($item->getTypeId() != 'bundle' || $item->getTypeId() != 'configurable') {
                        if ($pricelistPrice) {
                            $finalPrice = $pricelistPrice;
                        }
                        $actualPrice = $this->helperPriceRule->getActualPrice(
                            $finalPrice,
                            $tierPrice,
                            $categoryDiscountedPrice,
                            $pricelistPrice,
                            $specialPrice,
                            0,
                            0
                        );
                        $item->setPrice($actualPrice);
                        $item->setFinalPrice($actualPrice);

                        return $priceRender->render($this->getPriceTypeCode(), $item, $arguments);
                    }
                } else {
                    return $priceRender->render($this->getPriceTypeCode(), $product, $arguments);
                }

                //echo $priceRender->render($this->getPriceTypeCode(), $product, $arguments);exit;

            }
        }
        return parent::_toHtml();
    }

    public function getCacheLifetime()
    {
        return null;
    }

}
