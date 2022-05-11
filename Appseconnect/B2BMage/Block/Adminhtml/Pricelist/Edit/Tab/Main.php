<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\Pricelist\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory as PricelistCollectionFactory;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Main
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Main extends Generic implements TabInterface
{

    /**
     * PricelistCollectionFactory
     *
     * @var PricelistCollectionFactory
     */
    public $pricelistModelFactory;

    /**
     * WebsiteCollectionFactory
     *
     * @var WebsiteCollectionFactory
     */
    public $websiteCollectionFactory;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context                  Context
     * @param \Magento\Framework\Registry             $registry                 Registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory              FormFactory
     * @param PricelistCollectionFactory              $pricelistModelFactory    PricelistModelFactory
     * @param WebsiteCollectionFactory                $websiteCollectionFactory WebsiteCollectionFactory
     * @param array                                   $data                     Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        PricelistCollectionFactory $pricelistModelFactory,
        WebsiteCollectionFactory $websiteCollectionFactory,
        array $data = []
    ) {
        $this->pricelistModelFactory = $pricelistModelFactory;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('insync_pricelist');
        
        $isElementDisabled = false;
        
        $form = $this->_formFactory->create();
        
        $form->setHtmlIdPrefix('page_');
        
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
            'legend' => __('Pricelist Information')
            ]
        );
        
        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                [
                'name' => 'id'
                ]
            );
        }
        
        $fieldset->addField(
            'website_id',
            'select',
            [
            'name' => 'website_id',
            'label' => __('Associated to Website'),
            'title' => __('Associated to Website'),
            'required' => true,
            'options' => $this->getWebsiteId(),
            'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'pricelist_name',
            'text',
            [
            'name' => 'pricelist_name',
            'label' => __('Pricelist Name'),
            'title' => __('Pricelist Name'),
            'required' => true,
            'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'discount_factor',
            'text',
            [
            'name' => 'discount_factor',
            'label' => __('Factor'),
            'title' => __('Factor'),
            'required' => true,
            'disabled' => $isElementDisabled,
            'class' => 'validate-number'
            ]
        );
        $fieldset->addField(
            'calculate_discount_factor',
            'hidden',
            [
                'index' => 'discount_factor'
            ]
        );
        
        $fieldset->addField(
            'is_active',
            'select',
            [
            'name' => 'is_active',
            'label' => __('Is Active'),
            'title' => __('Is Active'),
            'required' => false,
            'options' => [
                '0' => 'No',
                '1' => 'Yes'
            ],
            'disabled' => $isElementDisabled
            ]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    /**
     * GetPricelistId
     *
     * @return array
     */
    public function getPricelistId()
    {
        $pricelistModel = $this->pricelistModelFactory->create();
        $pricelistId = $this->getRequest()->getParam('id');
        $resultData = [];
        $resultData[0] = "Base Price";
        if ($pricelistId) {
            $pricelistModel->addFieldToFilter(
                'id',
                [
                'nin' => $pricelistId
                ]
            );
        }
        foreach ($pricelistModel->getData() as $val) {
            $resultData[$val['id']] = $val['pricelist_name'];
        }
        
        return $resultData;
    }

    /**
     * GetWebsiteId
     *
     * @return array
     */
    public function getWebsiteId()
    {
        $websiteCollection = $this->websiteCollectionFactory->create();
        $output = $websiteCollection->getData();
        foreach ($output as $val) {
            $result[$val["website_id"]] = $val['name'];
        }
        
        return $result;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Pricelist Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Pricelist Information');
    }

    /**
     * CanShowTab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * IsHidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId resource id
     *
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
