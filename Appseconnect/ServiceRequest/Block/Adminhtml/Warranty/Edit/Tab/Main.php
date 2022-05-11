<?php

namespace Appseconnect\ServiceRequest\Block\Adminhtml\Warranty\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;

class Main extends Generic implements TabInterface
{


    /**
     * @var WebsiteCollectionFactory
     */
    public $websiteCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    public $customerCollectionFactory;

    /**
     * @var \Appseconnect\ServiceRequest\Model\Source\Status
     */
    public $serviceStatus;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    public $yesNo;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param PricelistCollectionFactory $pricelistModelFactory
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        WebsiteCollectionFactory $websiteCollectionFactory,
        array $data = [],
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Appseconnect\ServiceRequest\Model\Source\ServiceStatus $serviceStatus,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Config\Model\Config\Source\Yesno $yesNo
    ) {
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->serviceStatus = $serviceStatus;
        $this->yesNo = $yesNo;
        $this->timezone = $timezone;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('insync_warrantyrequest');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Additional Information')
        ]);

        if ($model) {
            $fieldset->addField('id', 'hidden', [
                'name' => 'id'
            ]);
        }

        $fieldset->addField('is_active', 'select', [
                'name' => 'is_active',
                'label' => __('Activate warranty ?'),
                'title' => __('Activate warranty ?'),
                'values' => $this->yesNo->toOptionArray(),
            ]
        );


        if ($model) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }


    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Additional Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Additional Information');
    }

    /**
     *
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Override GetTemplate
     *
     * @return string
     */
    public function getTemplate() {
        return 'Appseconnect_ServiceRequest::widget/warranty/form.phtml';
    }

    public function getWarrantyData() {
        $data = $this->_coreRegistry->registry('insync_warrantyrequest')->getData();
        $customer = $this->customerRepository->getById($data['customer_id']);
        $data['customer_name'] = $customer->getFirstname() . ' ' . $customer->getLastname();
        $data['customer_email'] = $customer->getEmail();
        $data['customer_group'] = $this->groupRepository->getById($customer->getGroupId())->getCode();
        $data['document_path'] = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );


        //timezone
        if($data['date_of_purchase']) {
            $data['date_of_purchase'] = $this->timezone->date($data['date_of_purchase'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if($data['submit_date']) {
            $data['submit_date'] = $this->timezone->date($data['submit_date'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if($data['warranty_start_date']) {
            $data['warranty_start_date'] = $this->timezone->date($data['warranty_start_date'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if($data['warranty_end_date']) {
            $data['warranty_end_date'] = $this->timezone->date($data['warranty_end_date'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        return $data;
    }
}
