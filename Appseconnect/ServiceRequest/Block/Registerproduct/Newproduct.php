<?php
namespace Appseconnect\ServiceRequest\Block\Registerproduct;

use Magento\Customer\Model\Session;
use Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory;

/**
 * Class View
 * @package Appseconnect\ServiceRequest\Block\Request
 */
class Newproduct extends \Magento\Framework\View\Element\Template
{

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * @var RequestPostFactory
     */
    protected $requestPostFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * View constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CollectionFactory $requestPostFactory
     * @param Session $customerSession
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $requestPostFactory,
        Session $customerSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Appseconnect\ServiceRequest\Helper\ServiceRequest\Data $helperData,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->requestPostFactory = $requestPostFactory;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;

        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(sprintf("New product Registration"));
    }

    /**
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }

    public function getProductName($sku){
        return $this->productRepository->get($sku)->getName();
    }
}
