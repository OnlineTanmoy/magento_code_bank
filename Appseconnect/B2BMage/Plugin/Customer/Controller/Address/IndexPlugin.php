<?php

/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Plugin\Customer\Controller\Address;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class IndexPlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class IndexPlugin
{

    /**
     * Customer repository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * PageFactory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;

    /**
     * RedirectFactory
     *
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    public $resultRedirectFactory;

    /**
     * RedirectInterface
     *
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    public $redirect;

    /**
     * Helper Contact Person.
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * CustomerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * Initialize class variable
     *
     * @param \Magento\Framework\View\Result\PageFactory           $resultPageFactory     ResultPageFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface    $customerRepository    CustomerRepository
     * @param Session                                              $customerSession       CustomerSession
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory ResultRedirectFactory
     * @param \Magento\Framework\App\Response\RedirectInterface    $redirect              Redirect
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data      $helperContactPerson   HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory              $customerFactory       CustomerFactory
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->redirect = $redirect;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
    }

    /**
     * AroundExecute
     *
     * @param \Magento\Customer\Controller\Address\Index $subject Subject
     * @param \Closure                                   $proceed Proceed
     *
     * @return \Magento\Framework\View\Result\Page|mixed
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Address\Index $subject,
        \Closure $proceed
    ) {

        $customerId = $this->customerSession->getCustomerId();
        if ($this->helperContactPerson->isContactPerson(
            $this->customerFactory->create()
                ->load($customerId)
        )
        ) {
            $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
            $customerId = $parentCustomerMapData ?
                $parentCustomerMapData['customer_id'] :
                $customerId;
        }
        $addresses = $this->customerRepository->getById($customerId)->getAddresses();
        if ($addresses) {
            $resultPage = $this->resultPageFactory->create();
            $block = $resultPage->getLayout()->getBlock('address_book');
            if ($block) {
                $block->setRefererUrl($this->redirect->getRefererUrl());
            }
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath('*/*/new');
        }
    }
}
