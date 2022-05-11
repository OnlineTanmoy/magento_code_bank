<?php

namespace Appseconnect\ServiceRequest\Block\Repair;

use Magento\Customer\Model\Session;
use Appseconnect\ServiceRequest\Model\ResourceModel\Repair\CollectionFactory;

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
    protected $repairPostFactory;

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
     * @param CollectionFactory $repairPostFactory
     * @param Session $customerSession
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $repairPostFactory,
        Session $customerSession,
        \Magento\Framework\App\Request\Http $request,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Appseconnect\ServiceRequest\Helper\Search $searchHelper,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    )
    {
        $this->searchHelper = $searchHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->repairPostFactory = $repairPostFactory;
        $this->request = $request;
        $this->contactPersonHelper = $contactPersonHelper;
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        $limit = $this->getRequest()->getParam('limit');
        $limit = $limit == null ? 10 : $limit;
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'simplenews.news.list.pager'
        );
        $pager->setLimit($limit)
            ->setShowAmounts(true)
            ->setCollection($this->getRepairRequestList());
        $this->setChild('pager', $pager);
        $this->getRepairRequestList()->getData();

        return $this;
    }

    /**
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_ServiceRequest::repair/listing.phtml");

        return parent::_toHtml();
    }


    /**
     * @return boolean|\Magento\Customer\Model\CustomerFactory
     */
    public function getRepairRequestList()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (empty($this->_searchData)) {
            $this->_searchData = $this->_coreRegistry->registry('search_repair_data');
            if (!$this->_searchData || empty($this->_searchData)) {
                $this->_searchData = $this->searchHelper->getSearchData();
            }
        }

        $data = [];
        $data['model_number'] = '';
        $data['product_name'] = '';
        if ($this->_searchData) {
            $data['model_number'] = isset($this->_searchData['model_number']) ? $this->_searchData['model_number'] : '';
            $data['product_name'] = isset($this->_searchData['product_name']) ? $this->_searchData['product_name'] : '';
        } else {
            $this->_searchData = $data;
        }

        if (!$this->_repairRequest) {
            $this->_repairRequest = $this->repairPostFactory->create()
                ->addFieldToSelect('*')
                ->setOrder('id', 'DESC');


            if (isset($data['model_number']) && $data['model_number'] != '') {
                $this->_repairRequest->addFieldToFilter('sku', array('like' => "%".$data['model_number']."%"));
            }
            if (isset($data['product_name']) && $data['product_name'] != '') {
                $this->_repairRequest->addFieldToFilter('product_description', array('like' => "%".$data['product_name']."%"));
            }
        }

        return $this->_repairRequest;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
        $price = $this->priceCurrency->format($price,true,4);
        return $price;
    }

    /**
     * @return Magento\Framework\Registry
     */
    public function getSearchData()
    {
        return $this->_searchData;
    }

    /**
     * @param object $order
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->getUrl('servicerequest/repair/listing');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getOrderSearchUrl()
    {
        return $this->getUrl('servicerequest/repair/search');
    }

}
