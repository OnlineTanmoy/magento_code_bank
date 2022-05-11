<?php

namespace Appseconnect\ServiceRequest\Block\Registerproduct;

use Magento\Customer\Model\Session;
use Appseconnect\ServiceRequest\Model\ResourceModel\Registerproduct\CollectionFactory;

/**
 * Class Listing
 * @package Appseconnect\ServiceRequest\Block\Request
 */
class Listing extends \Magento\Framework\View\Element\Template
{

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * @var
     */
    protected $registerProductFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var
     */
    protected $_repairRequest;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $contactPersonHelper;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    protected $_searchData = [];

    /**
     * @var \Magento\Framework\Registry
     */
    public $_coreRegistry;

    /**
     * @var \Appseconnect\ServiceRequest\Helper\Search
     */
    public $searchHelper;

    /**
     * Listing constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CollectionFactory $registerproductPostFactory
     * @param Session $customerSession
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context     $context,
        CollectionFactory                                    $registerproductPostFactory,
        Session                                              $customerSession,
        \Magento\Framework\App\Request\Http                  $request,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data      $contactPersonHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface      $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface    $priceCurrency,
        \Appseconnect\ServiceRequest\Helper\Search           $searchHelper,
        \Magento\Framework\Registry                          $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface           $storeManager,
        \Magento\Customer\Model\CustomerFactory              $customerFactory,

        array                                                $data = []
    )
    {
        $this->searchHelper = $searchHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->registerProductFactory = $registerproductPostFactory;
        $this->request = $request;
        $this->contactPersonHelper = $contactPersonHelper;
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        $this->timezone = $timezone;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->customerFactory = $customerFactory;

        parent::__construct( $context, $data );
    }

    /**
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        $limit = $this->getRequest()->getParam( 'limit' );
        $limit = $limit == null ? 10 : $limit;
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'simplenews.news.list.pager'
        );
        $pager->setLimit( $limit )
            ->setShowAmounts( true )
            ->setCollection( $this->getRegisterProductList() );
        $this->setChild( 'pager', $pager );
        $this->getRegisterProductList()->getData();

        return $this;
    }

    /**
     * @return $this
     */
    public function _tohtml()
    {
        //$this->setTemplate("Appseconnect_ServiceRequest::registerproduct/listing.phtml");

        return parent::_toHtml();
    }


    /**
     * @return boolean|\Magento\Customer\Model\CustomerFactory
     */
    public function getRegisterProductList()
    {

        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }


        if (empty( $this->_searchData )) {
            $this->_searchData = $this->_coreRegistry->registry( 'search_registerproduct_data' );
            if (!$this->_searchData || empty( $this->_searchData )) {
                $this->_searchData = $this->searchHelper->getSearchData();
            }
        }

        $data = [];
        $data['sku'] = '';
        $data['product_name'] = '';
        if ($this->_searchData) {
            $data['sku'] = isset( $this->_searchData['sku'] ) ? $this->_searchData['sku'] : '';
            $data['serial_number'] = isset( $this->_searchData['serial_number'] ) ? $this->_searchData['serial_number'] : '';
        } else {
            $this->_searchData = $data;
        }

        if (!$this->_repairRequest) {
            $customer = $this->customerFactory->create()->load( $customerId );

            if ($this->contactPersonHelper->isContactPerson( $customer )) {
                $customerData = $this->contactPersonHelper->getCustomerId( $customerId );
                $this->_repairRequest = $this->registerProductFactory->create()
                    ->addFieldToSelect( '*' )
                    ->addFieldToFilter( 'customer_id', $customerData ['customer_id'] )
                    ->setOrder( 'id', 'DESC' );
            } else {
                $this->_repairRequest = $this->registerProductFactory->create()
                    ->addFieldToSelect( '*' )
                    ->addFieldToFilter( 'customer_id', $customerId )
                    ->setOrder( 'id', 'DESC' );
            }


            if (isset( $data['sku'] ) && $data['sku'] != '') {
                $this->_repairRequest->addFieldToFilter( 'sku', array('like' => "%" . $data['sku'] . "%") );
            }
            if (isset( $data['serial_number'] ) && $data['serial_number'] != '') {
                $this->_repairRequest->addFieldToFilter( 'mfr_serial_no', array('like' => "%" . $data['serial_number'] . "%") );
            }
        }

        return $this->_repairRequest;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml( 'pager' );
    }

    /**
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }

    /**
     * Get current store currency symbol with price
     * $price price value
     * true includeContainer
     * Precision value 4
     */
    public function getCurrencyFormat($price)
    {
        $price = $this->priceCurrency->format( $price, true, 4 );
        return $price;
    }

    /**
     * Format to timezone date
     * @param $date
     * @return mixed
     */
    public function getFormatDate($date)
    {
        return $this->timezone->date( $date )
            ->format( 'd-M-Y' );
    }

    /**
     * @return Magento\Framework\Registry
     */
    public function getSearchData()
    {
        return $this->_searchData;
    }

    /**
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->getUrl( 'servicerequest/registerproduct/listing' );
    }

    /**
     * @return string
     */
    public function getRegisterProductSearchUrl()
    {
        return $this->getUrl( 'servicerequest/registerproduct/search' );
    }

    /**
     * Get Download path
     *
     * @return string
     */
    public function getDownloadDocPath()
    {
        return $this->_storeManager->getStore()->getBaseUrl( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }

}
