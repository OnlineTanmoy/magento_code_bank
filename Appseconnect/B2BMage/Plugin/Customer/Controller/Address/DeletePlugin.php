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
 * Class DeletePlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DeletePlugin
{

    /**
     * Customer repository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

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
     * Validator
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    public $formKeyValidator;

    /**
     * AddressRepositoryInterface
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    public $addressRepository;

    /**
     * ManagerInterface
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    public $messageManager;

    /**
     * Initialize class constructor.
     *
     * @param \Magento\Framework\Data\Form\FormKey\Validator       $formKeyValidator      FormKeyValidator
     * @param \Magento\Customer\Api\CustomerRepositoryInterface    $customerRepository    CustomerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface     $addressRepository     AddressRepository
     * @param Session                                              $customerSession       CustomerSession
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory ResultRedirectFactory
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data      $helperContactPerson   HelperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory              $customerFactory       CustomerFactory
     * @param \Magento\Framework\Message\ManagerInterface          $messageManager        MessageManager
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->customerSession = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * AroundExecute
     *
     * @param \Magento\Customer\Controller\Address\Delete $subject Subject
     * @param \Closure                                    $proceed Proceed
     *
     * @return mixed
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Address\Delete $subject,
        \Closure $proceed
    ) {
        $addressId = $subject->getRequest()->getParam('id', false);
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
        } else {
            $proceed();
        }

        if ($addressId && $this->formKeyValidator->validate($subject->getRequest())) {
            try {
                $address = $this->addressRepository->getById($addressId);
                if ($address->getCustomerId() === $customerId) {
                    $this->addressRepository->deleteById($addressId);
                    $this->messageManager->addSuccess(__('You deleted the address.'));
                } else {
                    $this->messageManager->addError(__('We can\'t delete the address right now.'));
                }
            } catch (\Exception $exception) {
                $this->messageManager->addException($exception, __('We can\'t delete the address right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
