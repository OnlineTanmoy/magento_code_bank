<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\Customer\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Session;
use Magento\Directory\Helper\Data as HelperData;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class FormPost
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class FormPost extends \Magento\Customer\Controller\Address
{
    /**
     * Region
     *
     * @var RegionFactory
     */
    public $regionFactory;
    
    /**
     * Helper data
     *
     * @var HelperData
     */
    public $helperData;
    
    /**
     * Mapper
     *
     * @var Mapper
     */
    private $_customerAddressMapper;
    
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;
    
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Form post constructor
     *
     * @param Context                                         $actionContext        context
     * @param Session                                         $customerSession      customer session
     * @param FormKeyValidator                                $formKeyValidator     form key validator
     * @param FormFactory                                     $formFactory          form
     * @param AddressRepositoryInterface                      $addressRepository    address repository
     * @param AddressInterfaceFactory                         $addressDataFactory   address data
     * @param RegionInterfaceFactory                          $regionDataFactory    region data
     * @param DataObjectProcessor                             $dataProcessor        data processor
     * @param DataObjectHelper                                $dataObjectHelper     data object helper
     * @param ForwardFactory                                  $resultForwardFactory result forward
     * @param PageFactory                                     $resultPageFactory    result page
     * @param RegionFactory                                   $regionFactory        region
     * @param HelperData                                      $helperData           helper
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson  contact person helper
     * @param \Magento\Customer\Model\CustomerFactory         $customerFactory      customer
     */
    public function __construct(
        Context $actionContext,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        FormFactory $formFactory,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        DataObjectProcessor $dataProcessor,
        DataObjectHelper $dataObjectHelper,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        RegionFactory $regionFactory,
        HelperData $helperData,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->regionFactory = $regionFactory;
        $this->helperData = $helperData;
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        parent::__construct(
            $actionContext,
            $customerSession,
            $formKeyValidator,
            $formFactory,
            $addressRepository,
            $addressDataFactory,
            $regionDataFactory,
            $dataProcessor,
            $dataObjectHelper,
            $resultForwardFactory,
            $resultPageFactory
        );
    }
    
    /**
     * Extract address from request
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    private function _extractCustomerAddress()
    {
        $existingAddressData = $this->_getExistingCustomerAddressData();
        
        $addressForm = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            $existingAddressData
        );
        $addressData = $addressForm->extractData($this->getRequest());
        $attributeValues = $addressForm->compactData($addressData);
        
        $this->_updateRegionElements($attributeValues);
        
        $addressDataObject = $this->addressDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $addressDataObject,
            array_merge($existingAddressData, $attributeValues),
            \Magento\Customer\Api\Data\AddressInterface::class
        );
        
        $customerId = $this->_getSession()->getCustomerId();
        if ($this->helperContactPerson->isContactPerson(
            $this->customerFactory->create()->load($customerId)
        )
        ) {
                $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
        }
            $addressDataObject->setCustomerId($customerId)
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        
        return $addressDataObject;
    }
    
    /**
     * Retrieve existing address data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _getExistingCustomerAddressData()
    {
        $existingAddressData = [];
        if ($addressId = $this->getRequest()->getParam('id')) {
            $existingAddress = $this->_addressRepository->getById($addressId);
            $customerId = $this->_getSession()->getCustomerId();
            if ($this->helperContactPerson->isContactPerson(
                $this->customerFactory->create()->load($customerId)
            )
            ) {
                    $parentCustomerMapData = $this->helperContactPerson->getCustomerId($customerId);
                    $customerId = $parentCustomerMapData ? $parentCustomerMapData['customer_id'] : $customerId;
            }
            if ($existingAddress->getCustomerId() !== $customerId) {
                throw new LocalizedException(
                    __('Customer Id do not match.')
                );
            }
            $existingAddressData = $this->_getCustomerAddressMapper()->toFlatArray($existingAddress);
        }
        return $existingAddressData;
    }
    
    /**
     * Update region data
     *
     * @param array $attributeValues atrribute value
     *
     * @return                                  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function _updateRegionElements(&$attributeValues)
    {
        if (!empty($attributeValues['region_id'])) {
            $newRegion = $this->regionFactory->create()->load($attributeValues['region_id']);
            $attributeValues['region_code'] = $newRegion->getCode();
            $attributeValues['region'] = $newRegion->getDefaultName();
        }
        
        $regionElements = [
            RegionInterface::REGION_ID => !empty($attributeValues['region_id']) ? $attributeValues['region_id'] : null,
            RegionInterface::REGION => !empty($attributeValues['region']) ? $attributeValues['region'] : null,
            RegionInterface::REGION_CODE => !empty($attributeValues['region_code'])
            ? $attributeValues['region_code']
            : null,
        ];
        
        $region = $this->regionDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $region,
            $regionElements,
            \Magento\Customer\Api\Data\RegionInterface::class
        );
        $attributeValues['region'] = $region;
    }
    
    /**
     * Process address form save
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $redirectUrl = null;
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        
        if (empty($this->getRequest()->isPost())) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPostValue());
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->_buildUrl('*/*/edit'))
            );
        }
        
        try {
            $customerAddress = $this->_extractCustomerAddress();
            $this->_addressRepository->save($customerAddress);
            $this->messageManager->addSuccess(__('You saved the address.'));
            $returnUrl = $this->_buildUrl('*/*/index', ['_secure' => true]);
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($returnUrl));
        } catch (InputException $e) {
            $this->messageManager->addError($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($error->getMessage());
            }
        } catch (\Exception $e) {
            $redirectUrl = $this->_buildUrl('*/*/index');
            $this->messageManager->addException($e, __('We can\'t save the address.'));
        }
        
        $returnUrl = $redirectUrl;
        if (!$redirectUrl) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPostValue());
            $returnUrl = $this->_buildUrl('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->error($returnUrl));
    }
    
    /**
     * Get Customer Address Mapper instance
     *
     * @return Mapper
     *
     * @deprecated 100.1.3
     */
    private function _getCustomerAddressMapper()
    {
        if ($this->_customerAddressMapper === null) {
            $this->_customerAddressMapper = ObjectManager::getInstance()->get(
                \Magento\Customer\Model\Address\Mapper::class
            );
        }
        return $this->_customerAddressMapper;
    }
}
