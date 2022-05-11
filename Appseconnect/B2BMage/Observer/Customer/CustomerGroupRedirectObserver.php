<?php

namespace Appseconnect\B2BMage\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ProductCategoryList;

class CustomerGroupRedirectObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    protected $httpContext;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    public $request;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    public $categoryCollectionFactory;

    /**
     * @var ProductCategoryList
     */
    public $productCategory;

    /**
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\Request\Http $request
     * @param Session $session
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param ProductCategoryList $productCategory
     */
    public function __construct(
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Request\Http $request,
        Session $session,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        ProductCategoryList $productCategory
    )
    {
        $this->redirect = $redirect;
        $this->httpContext = $httpContext;
        $this->scopeConfig = $scopeConfig;
        $this->_urlInterface = $urlInterface;
        $this->request = $request;
        $this->customerSession = $session;
        $this->helperContactPerson = $helperContactPerson;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCategory = $productCategory;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $categoryVisibility = $this->scopeConfig
            ->getValue('insync_category_visibility/select_visibility/select_visibility_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $customerId = $this->httpContext->getValue('customer_id');
        $customerType = $this->httpContext->getValue('customer_type');
        $groupId = '';
        if ($customerType == 3) {
            $customerData = $this->helperContactPerson->getCustomerData($customerId);
            $groupId = $customerData["group_id"];
        } elseif (!$this->customerSession->isLoggedIn()) {
            $groupId = 0;
        } else {
            $groupId = $this->customerSession->getCustomer()->getGroupId();
        }

        $categoryDatas = $this->categoryCollectionFactory
            ->create()
            ->addAttributeToFilter([
                ['attribute' => 'customer_group', ['finset' => [$groupId]]]])
            ->getData();

        $categoryId = [];
        foreach ($categoryDatas as $categoryData) {
            $categoryId[] = $categoryData['entity_id'];
        }

        $defaultCatKey = array_search(2, $categoryId);
        unset($categoryId[$defaultCatKey]);

        if ($categoryVisibility == 'group_wise_visibility') {
            if ($this->request->getFullActionName() == 'catalog_category_view') {
                $param = $this->request->getParams('id');
                $currentCategoryId = $param['id'];

                if (!in_array($currentCategoryId, $categoryId)) {
                    $url = $this->_urlInterface->getUrl('');
                    $observer->getControllerAction()
                        ->getResponse()
                        ->setRedirect($url);
                }
            }
            if ($this->request->getFullActionName() == 'catalog_product_view') {
                $param = $this->request->getParams();
                $productId = $param['id'];

                $categoryIds = $this->productCategory->getCategoryIds($productId);

                $result = array_intersect($categoryIds, $categoryId);

                if (!$result) {
                    $url = $this->_urlInterface->getUrl('');
                    $observer->getControllerAction()
                        ->getResponse()
                        ->setRedirect($url);
                }
            }
        }
    }
}
