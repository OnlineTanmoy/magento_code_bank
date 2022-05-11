<?php
namespace Appseconnect\Shoppinglist\Helper\Mylist;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Price\Collection as PricelistPriceCollection;
use Appseconnect\B2BMage\Helper\CustomerSpecialPrice\Data as SpecialPriceHelper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Appseconnect\Shoppinglist\Model\CustomerProductList
     */
    public $customerProductList;

    /**
     * @var \Appseconnect\Shoppinglist\Model\CustomerProductListItem
     */
    public $customerProductListItem;

    /**
     * @var customerSession
     */
    public $customerSession;

    /**
     * @var fileFactory
     */
    public $fileFactory;

    /**
     * @var productloader
     */
    public $productloader;

    /**
     * @var pdf
     */
    public $pdf;

    /**
     * @var priceCurrency
     */
    public $priceCurrency;

    /**
     * @var customerRepositoryInterface
     */
    public $customerRepositoryInterface;


    /**
     * @var storeManager
     */
    public $storeManager;

    /**
     * @var transportBuilder
     */
    public $transportBuilder;

    /**
     * @var inlineTranslation
     */
    public $inlineTranslation;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Bundle\Model\Selection
     */
    protected $bundleSelection;

    /**
     * @var bundleOption
     */
    protected $bundleOption;

    /**
     * @var configurable
     */
    protected $configurable;

    /**
     * @var targetLine
     */
    public $subLine = 0;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var \Appseconnect\B2BMage\Helper\PriceRule\Data
     */
    protected $priceRuleData;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var PricelistPriceCollection
     */
    public $pricelistCollection;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * @var \Appseconnect\B2BMage\Helper\Pricelist\Data
     */
    public $helperPricelist;

    /**
     * @var \Appseconnect\B2BMage\Helper\CategoryDiscount\Data
     */
    public $helperCategory;

    /**
     * @var \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data
     */
    public $helperTierprice;

    /**
     * @var SpecialPriceHelper
     */
    public $helperCustomerSpecialPrice;

    /**
     * Path for shoppinglist config
     */
    const XML_PATH_ENABLE_QUOTATION = 'insync_shoppinglist/general/enable_shoppinglist_active';

    public $httpContext;

    public $divisionHelper;
    /**
     * CustomerRepositoryInterface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;




    /**
     * Data constructor.
     * @param \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList
     * @param \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Magento\Framework\Pricing\Helper\Data $priceCurrency
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Bundle\Model\Selection $bundleSelection
     * @param \Magento\Bundle\Model\Option $bundleOption
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param Session $customerSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Http\Context                $httpContext
     * @param \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Appseconnect\Shoppinglist\Model\CustomerProductListFactory $customerProductList,
        \Appseconnect\Shoppinglist\Model\CustomerProductListItemFactory $customerProductListItem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Framework\Pricing\Helper\Data $priceCurrency,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Bundle\Model\Selection $bundleSelection,
        \Magento\Bundle\Model\Option $bundleOption,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        Session $customerSession,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\App\Helper\Context $context,
        \Appseconnect\B2BMage\Helper\PriceRule\Data $priceRuleData,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        PricelistPriceCollection $pricelistCollection,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CategoryDiscount\Data $helperCategory,
        \Appseconnect\B2BMage\Helper\Pricelist\Data $helperPricelist,
        \Appseconnect\B2BMage\Helper\CustomerTierPrice\Data $helperTierprice,
        SpecialPriceHelper $helperCustomerSpecialPrice,
        ProductRepositoryInterface $productRepository,
        ProductAttributeRepositoryInterface $attributeRepository,
        \Appseconnect\CompanyDivision\Helper\Division\Data $divisionHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Http\Context $httpContext

    ) {
        $this->customerProductList = $customerProductList;
        $this->customerProductListItem = $customerProductListItem;
        $this->customerSession = $customerSession;
        $this->fileFactory = $fileFactory;
        $this->productloader = $productloader;
        $this->pdf = new \Zend_Pdf();
        $this->priceCurrency = $priceCurrency;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->resultPageFactory = $resultPageFactory;
        $this->bundleSelection = $bundleSelection;
        $this->bundleOption = $bundleOption;
        $this->configurable = $configurable;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->fileDriver = $fileDriver;
        $this->priceRuleData = $priceRuleData;
        $this->customerFactory = $customerFactory;
        $this->pricelistCollection = $pricelistCollection;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCategory = $helperCategory;
        $this->helperTierprice = $helperTierprice;
        $this->helperCustomerSpecialPrice = $helperCustomerSpecialPrice;
        $this->helperPricelist = $helperPricelist;
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->divisionHelper = $divisionHelper;
        $this->customerRepository = $customerRepository;
        $this->httpContext = $httpContext;
        parent::__construct($context);


    }

    /**
     * @param $product
     * @param $selectData
     * @param $productType
     */
    public function getFinalProductPrice($product)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customer = $this->customerFactory->create()->load($customerId);
        $websiteId = $this->customerSession->getCustomer()->getWebsiteId();
        $customerType = $customer->getCustomerType();

        $customerPricelistCode = $this->customerSession->getCustomer()->getData('pricelist_code');

        if ($customerType == 3) {
            $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
            $customerCollection = $this->customerFactory->create()->load($customerDetail['customer_id']);
            $customerPricelistCode = $customerCollection->getData('pricelist_code');
            $customerId = $customerDetail['customer_id'];
        } else if ($customerType == 2) {
            $customerPricelistCode = '';
        }

        $pricelistStatus = 0;
        $pricelistCollection = $this->pricelistCollection
            ->addFieldToFilter('id', $customerPricelistCode)
            ->addFieldToFilter('website_id', $websiteId)
            ->getData();

        if (isset($pricelistCollection[0])) {
            $pricelistStatus = $pricelistCollection[0]['is_active'];
        }

        return $this->getSimpleFinalPrice($product, $customerPricelistCode,
            $pricelistStatus, $customerId, $websiteId);

    }

    /**
     * @param $product
     * @param $customerPricelistCode
     * @param $pricelistStatus
     * @param $customerId
     * @param $websiteId
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSimpleFinalPrice($product, $customerPricelistCode,
                                        $pricelistStatus, $customerId, $websiteId)
    {
        $productId = $product->getId();
        $categoryIds = $product->getCategoryIds();
        $finalPrice = $product->getFinalPrice();

        $pricelistPrice = '';
        if ($customerPricelistCode && $pricelistStatus) {
            $pricelistPrice = $this->helperPricelist->getAmount(
                $productId,
                $finalPrice,
                $customerPricelistCode,
                true
            );
        }

        $categoryDiscountedPrice = $this->helperCategory->getCategoryDiscountAmount(
            $finalPrice,
            $customerId,
            $categoryIds
        );

        $productSku = $product->getSku();
        $tierPrice = '';
        $tierPrice = $this->helperTierprice->getTierprice(
            $productId,
            $productSku,
            $customerId,
            $websiteId,
            1,
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

        if ($pricelistPrice) {
            $finalPrice = $pricelistPrice;
        }

        $simpleCalculatedPrice = $this->priceRuleData->getActualPrice(
            $finalPrice,
            $tierPrice,
            $categoryDiscountedPrice,
            $pricelistPrice,
            $specialPrice
        );

        return $simpleCalculatedPrice;
    }

    /**
     * @param int $priceListId
     * @return array
     */
    public function getListCollection()
    {
        $collection = $this->customerProductList->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $this->customerSession->getCustomerId());

        return $collection;
    }

    public function getListPrint($listId){

        $list = $this->customerProductList->create()->load($listId);

        $listItemCollection = $this->customerProductListItem->create()->getCollection()
            ->addFieldToFilter('list_id', array('eq' => $listId));

        $pageNumber = 1;
        $page = $this->createPage($pageNumber);
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
        $style = new \Zend_Pdf_Style();

        //Header Logo And Details
        $this->insertLogo($page);

        $page->drawText(__("List Detials"), $this->x + 5, $this->y+50, 'UTF-8');

        $style->setFont($font,15);
        $page->setStyle($style);
        $page->drawText(__("List Name: ").$list->getListName(), $this->x + 5, $this->y+20, 'UTF-8');
        $page->drawText(__("List Item: ").$list->getItem(), $this->x + 250, $this->y+20, 'UTF-8');
        $page->drawText(__("List Total Price: ").$this->priceCurrency->currency($list->getTotalPrice(), true, false),
            $this->x + 350, $this->y+20, 'UTF-8');

        $style->setFont($font,12);
        $page->setStyle($style);
        $page->drawRectangle(30, $this->y , $page->getWidth()-30, $this->y - 30, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
        $page->drawLine($this->x + 100, $this->y, $this->x + 100, $this->y - 30);
        $page->drawLine(round($page->getWidth()/2) + 70, $this->y,round($page->getWidth()/2) + 70, $this->y - 30);
        $page->drawLine(round($page->getWidth()*3/4), $this->y, round($page->getWidth()*3/4), $this->y - 30);
        $page->drawLine(round($page->getWidth()*3/4) + 60, $this->y, round($page->getWidth()*3/4) + 60, $this->y - 30);

        $page->drawText(__("Code"), $this->x + 30, $this->y - 20, 'UTF-8');
        $page->drawText(__("Description"), $this->x + 190, $this->y - 20, 'UTF-8');
        $page->drawText(__("Unit Price"), $this->x + 350, $this->y - 15, 'UTF-8');
        $page->drawText(__("Incl. GST"), $this->x + 350, $this->y - 25, 'UTF-8');
        $page->drawText(__("UOM"), round($page->getWidth()*3/4) + 5, $this->y - 20, 'UTF-8');
        $page->drawText(__("Quantity"), round($page->getWidth()*3/4) + 60 + 5, $this->y - 20, 'UTF-8');
        $counter = 1;
        $style->setFont($font,8);
        $page->setStyle($style);
        $yx = $this->y - 18;

        foreach($listItemCollection as $listItem) {
            $yx -= 30;
            $_optionText = "";
            $product = $this->productloader->create()->load($listItem->getProductId());
            $_attributeId = $product->getResource()->getAttribute('product_uom');
            if ($_attributeId->usesSource()) {
                $_optionText = $_attributeId->getSource()->getOptionText($product->getProductUom());
            }
            $page->drawLine(30, $this->y-($counter*30) - $this->subLine , $page->getWidth()-30, $this->y-($counter*30) - $this->subLine);
            $desc = $listItem->getProductDescription();
            $code = $listItem->getProductSku();
            $length = strlen($code);

            if($length > 18){
                $output = str_split($code, 18);

                if(count($output) < 3) {
                    $codmyx = $yx + 4;
                }elseif(count($output) > 2) {
                    $codmyx = $yx + 9;
                }else{
                    $codmyx = $yx + 7;
                }
                foreach ($output as $codeLine => $line){
                    $page->drawText($line, $this->x+ 10, $codmyx, 'UTF-8');
                    $codmyx -=7;
                }
            }

            if(strlen($desc) > 45){
                $array = explode( "\n", wordwrap( $desc, 45));
                $yx -= 25;
                if(count($array) > 2){
                    $myx = $yx + 35;
                }else{
                    $myx = $yx + 30;
                }

                if($length < 18 && strlen($desc) < 46) {
                    $page->drawText($listItem->getProductSku(), $this->x + 10, $myx, 'UTF-8');
                    $page->drawText($this->priceCurrency->currency($listItem->getUnitPrice(), true, false),
                        round($page->getWidth()/2) + 90, $myx, 'UTF-8');
                    $page->drawText($_optionText, $page->getWidth() / 2 + 160, $myx, 'UTF-8');
                    $page->drawText($listItem->getQty(), round($page->getWidth()*3/4) + 60 + 15, $myx, 'UTF-8');
                }elseif($length > 19 && strlen($desc) > 46){
                    $page->drawText($this->priceCurrency->currency($listItem->getUnitPrice(), true, false),
                        round($page->getWidth()/2) + 90, $myx - 5, 'UTF-8');
                    $page->drawText($_optionText, $page->getWidth() / 2 + 160, $myx - 5, 'UTF-8');
                    $page->drawText($listItem->getQty(), round($page->getWidth()*3/4) + 60 + 15, $myx - 5, 'UTF-8');
                }else{
                    $page->drawText($listItem->getProductSku(), $this->x + 10, $myx - 5, 'UTF-8');

                    $page->drawText($this->priceCurrency->currency($listItem->getUnitPrice(), true, false),
                        round($page->getWidth()/2) + 90, $myx, 'UTF-8');
                    $page->drawText($_optionText, $page->getWidth() / 2 + 160, $myx, 'UTF-8');
                    $page->drawText($listItem->getQty(), round($page->getWidth()*3/4) + 60 + 15, $myx , 'UTF-8');
                }

                foreach ($array as $line){
                    $page->drawText($line, $this->x + 120, $myx, 'UTF-8');
                    $myx-= 7;
                }
                $yx += 25;

            }else {
                $page->drawText($listItem->getProductSku(), $this->x + 10, $yx, 'UTF-8');
                $page->drawText($desc, $this->x + 120, $yx, 'UTF-8');

                $page->drawText($this->priceCurrency->currency($listItem->getUnitPrice(), true, false),
                    $page->getWidth() / 2 + 90, $yx, 'UTF-8');

                $page->drawText($_optionText, $page->getWidth() / 2 + 160, $yx, 'UTF-8');
                $page->drawText($listItem->getQty(), round($page->getWidth() * 3 / 4) + 60 + 15, $yx, 'UTF-8');

            }

                $this->getProductOption($listItem, $page, $counter);

                    $page->drawLine($this->x + 100, $this->y - ($counter * 30), $this->x + 100, $this->y - (($counter + 1) * 30) - $this->subLine);
                    //code left
                    $page->drawLine(30, $this->y - ($counter * 30), 30, $this->y - (($counter + 1) * 30) - $this->subLine);
                    // qty right
                    $page->drawLine($page->getWidth() - 30, $this->y - ($counter * 30), $page->getWidth() - 30, $this->y - (($counter + 1) * 30) - $this->subLine);
                    // DESC | unit price
                    $page->drawLine(round($page->getWidth() / 2) + 70, $this->y - ($counter * 30), round($page->getWidth() / 2) + 70, $this->y - (($counter + 1) * 30) - $this->subLine);
                    //uom|qty
                    $page->drawLine(round($page->getWidth() * 3 / 4) + 60, $this->y - ($counter * 30), round($page->getWidth() * 3 / 4) + 60, $this->y - (($counter + 1) * 30) - $this->subLine);
                    //unit ptice | uom
                    $page->drawLine(round($page->getWidth() * 3 / 4), $this->y - ($counter * 30), round($page->getWidth() * 3 / 4), $this->y - (($counter + 1) * 30) - $this->subLine);
           $page->drawLine(30, $this->y - (($counter + 1) * 30) - $this->subLine, $page->getWidth() - 30, $this->y - (($counter + 1) * 30) - $this->subLine);

            $counter++;

            if($pageNumber == 1 && $counter == 21) {
                $pageNumber++;
                $page = $this->createPage($pageNumber);
                $this->getTableHeader($page);
                $counter = 0;
                $yx = $this->y;
                $this->y = $this->y - 10;
            } else if($pageNumber > 1 && ($counter%24) == 0) {
                $pageNumber++;
                $page = $this->createPage($pageNumber);
                $this->getTableHeader($page);
                $counter = 0;
                $yx = $this->y;
                $this->y = $this->y - 10;
            }
        }

        $fileName = 'list.pdf';

        $this->fileFactory->create(
            $fileName,
            $this->pdf->render(),
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR, // this pdf will be saved in var directory with the name example.pdf
            'application/pdf'
        );
    }

    public function createPage($pageNumber) {
        $this->pdf->pages[] = $this->pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
        $page = $this->pdf->pages[($pageNumber - 1)];
        $style = new \Zend_Pdf_Style();
        $style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
        $style->setFont($font,8);
        $page->setStyle($style);
        $this->x = 30;

        $this->y = (850 - 100); //print table row from page top – 100px
        //Draw table header row’s
        $style->setFont($font,8);
        $page->setStyle($style);

        return $page;
    }

    /**
     * @param $page
     */
    public function getTableHeader($page){
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
        $style = new \Zend_Pdf_Style();
        $style->setFont($font,8);
        $page->setStyle($style);
        $page->drawRectangle(30, $this->y + 50 , $page->getWidth()-30, $this->y +20, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
        $page->drawLine($this->x + 100, $this->y + 50,$this->x + 100, $this->y + 20);
        $page->drawLine(round($page->getWidth()/2) + 70, $this->y + 50,round($page->getWidth()/2) + 70, $this->y + 20);
        $page->drawLine(round($page->getWidth()*3/4), $this->y + 50, round($page->getWidth()*3/4), $this->y + 20);
        $page->drawLine(round($page->getWidth()*3/4) + 60, $this->y + 50, round($page->getWidth()*.85), $this->y + 20);

        $page->drawText(__("Code"), $this->x + 25, $this->y + 30, 'UTF-8');
        $page->drawText(__("Description"), $this->x + 180, $this->y + 30, 'UTF-8');
        $page->drawText(__("Unit Price"), $this->x + 350, $this->y + 35, 'UTF-8');
        $page->drawText(__("Incl. GST"), $this->x + 350, $this->y + 25, 'UTF-8');
        $page->drawText(__("UOM"), round($page->getWidth()*3/4)  + 5, $this->y + 30, 'UTF-8');
        $page->drawText(__("Qunatity"), round($page->getWidth()*3/4) +60 + 5, $this->y + 30, 'UTF-8');

        $this->y = $this->y + 30;
    }

    /**
     * @param $customerIds
     * @param $listId
     */
    public function addShareCustomer($customerIds, $listId) {

        $loadProduct = $this->resultPageFactory->create()->getLayout()->createBlock('Appseconnect\Shoppinglist\Block\Customer\Account\Mylist\ItemList', 'customer.mylist.search',
            [
                'data' => [ 'listId' => $listId ]
            ]);
        $loadProduct->setTemplate('Appseconnect_Shoppinglist::customer/account/mylist/emailitemlist.phtml');

        foreach($customerIds as $customerId) {

            $customer = $this->customerRepositoryInterface->getById($customerId);

            $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
            $templateVars = array(
                'store' => $this->storeManager->getStore(),
                'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                'current_customer_name' => $this->customerSession->getCustomer()->getFirstname() . ' ' . $this->customerSession->getCustomer()->getLastname(),
                'message'   => $loadProduct->toHtml()
            );
            $from = array('email' => $this->customerSession->getCustomer()->getEmail(),
                            'name' => $this->customerSession->getCustomer()->getFirstname() . ' ' . $this->customerSession->getCustomer()->getLastname());
            $this->inlineTranslation->suspend();
            $to = array($customer->getEmail());
            $transport = $this->transportBuilder->setTemplateIdentifier('insync_share_list_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

        }
    }

    /**
     * @param $listMap
     * @param $page
     * @param $counter
     */
    public function getProductOption($listMap, $page, $counter)
    {
        $productOption = $listMap->getProductOption();
        parse_str($productOption, $optionData);

        if ($listMap->getProductType() == 'bundle') {
            asort($optionData['bundle_option']);
            $optionArray = [];
            foreach ($optionData['bundle_option'] as $optionId => $selection) {
                if (!in_array($optionId, $optionArray)) {
                    $bundleOption = $this->bundleOption->load($optionId);
                    $optionArray[] = $optionId;
                    $this->subLine = $this->subLine + 10;
                    $page->drawText($bundleOption->getTitle(), $this->x + 20, $this->y - (($counter+1)*25) - $this->subLine, 'UTF-8');
                }
                $bundleSlection = $this->bundleSelection->load($selection);
                $product = $this->productloader->create()->load($bundleSlection->getProductId());
                $this->subLine = $this->subLine + 10;
                $page->drawText($product->getSku(), $this->x + 20, $this->y - (($counter+1)*25) - $this->subLine, 'UTF-8');
            }
        } else if($listMap->getProductType() == 'configurable') {
            $productParent = $this->productloader->create()->load($listMap->getProductId());
            $productAttributeOptions = $this->configurable->getConfigurableAttributesAsArray($productParent);
            foreach ($productAttributeOptions as $key => $value) {

                $tmp_option = $value['values'];
                if(count($tmp_option) > 0)
                {
                    foreach ($tmp_option as $tmp)
                    {
                        if(in_array($tmp['value_index'], $optionData['super_attribute'])) {
                            $this->subLine = $this->subLine + 10;
                            $page->drawText($value['label'] . ': ' . $tmp['label'], $this->x + 20, $this->y - (($counter+1)*25) - $this->subLine, 'UTF-8');
                        }
                    }
                }
            }
        }

    }

    /**
     * @param $page
     */
    public function insertLogo($page)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
        $mediaPath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
        $img = $mediaPath."logo/stores/1/pdf-header.png";
        if ($this->fileDriver->isExists($img)) {
            $image = \Zend_Pdf_Image::imageWithPath($img);
            $top = 830;
            //top border of the page
            $widthLimit = 600;
            //half of the page width
            $heightLimit = 200;
            //assuming the image is not a "skyscraper"
            $width = $image->getPixelWidth();
            $height = $image->getPixelHeight();
            //preserving aspect ratio (proportions)
            $ratio = $width / $height;
            if ($ratio > 1 && $width > $widthLimit) {
                $width = $widthLimit;
                $height = $width / $ratio;
            } elseif ($ratio < 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $height * $ratio;
        } elseif ($ratio == 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $widthLimit;
            }
            $y1 = $top - $height;
            $y2 = $top;
            $x1 = 0;
            $x2 = $x1 + $width;
            //coordinates after transformation are rounded by Zend
            $page->drawImage($image, $x1, $y1, $x2, $y2);
            $this->y = 650;
        }
    }
    /**
     * Get Super attribute details by the child and
     * parent sku value
     *
     * @param string $parentSku
     * @param string $childSku
     *
     * @return array
     */
    public function getChildSuperAttribute(string $parentSku, string $childSku )
    {
        $parentProduct = $this->getProduct($parentSku);
        $childProduct = $this->getProduct($childSku);
        $_attributes = $parentProduct->getTypeInstance(true)->getConfigurableAttributes($parentProduct);

        $attributesPair = [];
        foreach ($_attributes as $_attribute) {
            $attributeId = (int)$_attribute->getAttributeId();
            $attributeCode = $this->getAttributeCode($attributeId);
            $attributesPair[$attributeId] = (int)$childProduct->getData($attributeCode);
        }
        return $attributesPair;
    }

    /**
     * Get attribute code by attribute id
     * @param int $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributeCode(int $id)
    {
        return $this->attributeRepository->get($id)->getAttributeCode();
    }

    /**
     * Get Product Object by SKU
     *
     * @param string $sku
     * @return ProductInterface|null
     */
    public function getProduct(string $sku)
    {
        $product = null;
        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $exception) {
            $this->logger->error(__($exception->getMessage()));
        }

        return $product;
    }

    public function getShoppingListConfiguration()
    {
        $shoppingListVisibility = $this->_scopeConfig
            ->getValue('insync_shoppinglist/general/enable_shoppinglist_active', 'store');

        return $shoppingListVisibility;
    }

    public function getCustomerQuoteConfigurationValue()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerType = $this->customerSession->getCustomer()->getCustomerType();

        if ($customerId && $customerType == 3) {
            if ($this->divisionHelper->isParentContact($customerId)) {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);

                $currentCustomerId = $this->customerSession->getCurrentCustomerId();
                if (isset($currentCustomerId)) {
                    // For division
                    $customerId = $currentCustomerId;

                } else {
                    $customerId = $customerDetail['customer_id'];
                }

            } else {
                $customerDetail = $this->helperContactPerson->getCustomerId($customerId);
                // For division
                $customerId = $customerDetail['customer_id'];
            }

            $customer = $this->customerRepository->getById($customerId);

            if ($customer->getCustomAttribute('enable_quote') == null){
                return 0;
            }

            return $customer->getCustomAttribute('enable_quote')->getValue();
        }

        return 0;
    }
/**
 * getEnableshoppinglistValue
 *
 * @return int
 */
    public function  getEnableshoppinglistValue()
    {

        $customerId = $this->httpContext->getValue( 'customer_id' );
        $shoppingenable = $this->scopeConfig->getValue(
            'insync_shoppinglist/general/enable_shoppinglist_active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($customerId) {
            $customerType = $this->httpContext->getValue( 'customer_type' );
            if ($customerType == 3 && $shoppingenable == 1 ) {
                return true;

            }
        }
            return false;
    }
}

