<?php
namespace Appseconnect\ServiceRequest\Block\Warranty;

use Magento\Customer\Model\Session;
use Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory;

/**
 * Class View
 * @package Appseconnect\ServiceRequest\Block\Request
 */
class Check extends \Magento\Framework\View\Element\Template
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

        array $data = []
    ) {

        $this->customerSession = $customerSession;
        $this->requestPostFactory = $requestPostFactory;
        $this->request = $request;
        $this->productRepository = $productRepository;

        parent::__construct($context, $data);
    }

    /**
     * Return is in warranty from customer session
     *
     * @return mixed
     */
    public function getIsInWarranty() {
        return $this->customerSession->getIsInWarranty();
    }

    /**
     * Return fixed price cost of the product
     * @return mixed
     */
    public function getRepairPrice() {
        return $this->customerSession->getRepairPrice();
    }

}
