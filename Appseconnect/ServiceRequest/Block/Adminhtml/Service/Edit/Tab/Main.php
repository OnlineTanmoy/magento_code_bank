<?php

namespace Appseconnect\ServiceRequest\Block\Adminhtml\Service\Edit\Tab;

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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Appseconnect\ServiceRequest\Model\Source\ServiceStatus $serviceStatus,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Config\Model\Config\Source\Yesno $yesNo
    ) {
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->serviceStatus = $serviceStatus;
        $this->yesNo = $yesNo;
        $this->timezone = $timezone;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('insync_servicerequest');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Service Information')
        ]);

        if ($model) {
            $fieldset->addField('entity_id', 'hidden', [
                'name' => 'entity_id'
            ]);
        }

        $fieldset->addField('model_number', 'text', [
            'name' => 'model_number',
            'label' => __('Model Number'),
            'title' => __('Model Number'),
            'readonly' => true,
            'required' => true
        ]);

        $fieldset->addField('serial_number', 'text', [
            'name' => 'serial_number',
            'label' => __('Serial Number'),
            'title' => __('Serial Number'),
            'readonly' => true,
            'required' => true
        ]);

        $fieldset->addField('short_description', 'textarea', [
            'name' => 'short_description',
            'label' => __('Short Description'),
            'title' => __('Short Description'),
            'readonly' => true,
            'required' => false
        ]);

        $fieldset->addField('detailed_description', 'textarea', [
            'name' => 'detailed_description',
            'label' => __('Detailed Description'),
            'title' => __('Detailed Description'),
            'readonly' => true,
            'required' => false
        ]);

        $fieldset->addField('customer_name', 'text', [
            'name' => 'customer_name',
            'label' => __('Customer Name'),
            'title' => __('Customer Name'),
            'readonly' => true,
            'required' => true
        ]);

        $serviceStatus = $this->serviceStatus->getOptionArray();
        $serviceStatus[10] = 'Closed without Repair';
        $fieldset->addField('status', 'select', [
            'name' => 'service_status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $serviceStatus,
            'required' => true
        ]);
        if($model->getData('service_quote_required') == 1 && $model->getData('fpr_price') == 0 ) {
            $fieldset->addField('fpr_price', 'text', [
                'name' => 'fpr_price',
                'label' => __('Repair cost'),
                'title' => __('Repair cost'),
                'required' => true
            ]);
        }

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
        return 'Appseconnect_ServiceRequest::widget/form.phtml';
    }

    public function getServiceData() {
        $data = $this->_coreRegistry->registry('insync_servicerequest')->getData();
        $customer = $this->customerRepository->getById($data['customer_id']);
        $data['customer_name'] = $customer->getFirstname() . ' ' . $customer->getLastname();
        $data['customer_email'] = $customer->getEmail();
        $data['customer_group'] = $this->groupRepository->getById($customer->getGroupId())->getCode();

        $customer = $this->customerRepository->getById($data['contact_person_id']);
        $data['requested_by_name'] = $customer->getFirstname() . ' ' . $customer->getLastname();
        $data['requested_by_email'] = $customer->getEmail();

        //timezone
        if($data['post']) {
            $data['post'] = $this->timezone->date($data['post'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if($data['draft_date']) {
            $data['draft_date'] = $this->timezone->date($data['draft_date'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if($data['submit_date']) {
            $data['submit_date'] = $this->timezone->date($data['submit_date'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if($data['service_date']) {
            $data['service_date'] = $this->timezone->date($data['service_date'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if($data['transit_date']) {
            $data['transit_date'] = $this->timezone->date($data['transit_date'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if($data['complete_date']) {
            $data['complete_date'] = $this->timezone->date($data['complete_date'])
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        return $data;
    }
}
