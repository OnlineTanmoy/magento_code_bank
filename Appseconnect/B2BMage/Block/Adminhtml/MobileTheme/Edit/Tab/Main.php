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

namespace Appseconnect\B2BMage\Block\Adminhtml\MobileTheme\Edit\Tab;

use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory as PricelistCollectionFactory;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;

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
     * MobilethemeFactory
     *
     * @var \Appseconnect\B2BMage\Model\MobilethemeFactory
     */
    public $mobilethemeFactory;

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Mobiletheme\Data
     */
    public $mobileThemeData;


    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context        $context                  Context
     * @param \Magento\Framework\Registry                    $registry                 Registry
     * @param \Magento\Framework\Data\FormFactory            $formFactory              FormFactory
     * @param PricelistCollectionFactory                     $pricelistModelFactory    PricelistModelFactory
     * @param WebsiteCollectionFactory                       $websiteCollectionFactory WebsiteCollectionFactory
     * @param \Appseconnect\B2BMage\Helper\Mobiletheme\Data  $mobileThemeData          MobileThemeData
     * @param \Appseconnect\B2BMage\Model\MobilethemeFactory $mobilethemeFactory       MobilethemeFactory
     * @param array                                          $data                     Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        PricelistCollectionFactory $pricelistModelFactory,
        WebsiteCollectionFactory $websiteCollectionFactory,
        \Appseconnect\B2BMage\Helper\Mobiletheme\Data $mobileThemeData,
        \Appseconnect\B2BMage\Model\MobilethemeFactory $mobilethemeFactory,
        array $data = []
    ) {
        $this->pricelistModelFactory = $pricelistModelFactory;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->mobileThemeData = $mobileThemeData;
        $this->mobilethemeFactory = $mobilethemeFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('insync_mobile_theme');

        $isElementDisabled = false;

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Configuration Details')
            ]
        );

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $apiUrl = $this->_scopeConfig->getValue('insync_mobile/theme/api_url', $storeScope);

        $mobileThemeModel = $this->mobilethemeFactory->create();
        $mobileThemeModel->load(1);

        $organisationName = '';
        $contactNumber = '';
        $contactName = '';
        $emailId = '';
        $phoneNumber2 = '';
        $contactName2 = '';
        $emailId2 = '';

        $appImageUrl = '';
        $backColour = '';
        $textColour = '';
        $registerKey = '';

        $buttonColor = '';
        $buttonTextColor = '';
        $iconColor = '';
        $default = '';
        $selectionColor = '';
        $mutedSelection = '';

        if ($mobileThemeModel->getMobileAccountId() && $apiUrl != '') {
            $make_call = $this->mobileThemeData->callAPI(
                'GET',
                $this->_scopeConfig->getValue('insync_mobile/theme/api_url', $storeScope) . 'Account?orgAppId=' . $mobileThemeModel->getMobileAccountId(),
                false
            );
            $response = json_decode($make_call, true);

            $fieldset->addField(
                'id',
                'hidden',
                [
                    'name' => 'id'
                ]
            );

            $model->setData('id', 1);

            $organisationName = $mobileThemeModel->getOrganisationName();
            $contactNumber = $mobileThemeModel->getPhoneNumber();
            $contactName = $response['PrimaryContactDetail']['ContactName'];
            $emailId = $response['PrimaryContactDetail']['EmailId'];
            $registerKey = $response['OrganizationDetail']['RegistrationKey'];
            if ($response['AlternateContactDetail'] != null) {
                $phoneNumber2 = $response['AlternateContactDetail']['PhoneNumber'];
                $contactName2 = $response['AlternateContactDetail']['ContactName'];
                $emailId2 = $response['AlternateContactDetail']['EmailId'];
            } else {
                $phoneNumber2 = '';
                $contactName2 = '';
                $emailId2 = '';
            }
            foreach ($response['OrganizationSettingDetails'] as $key => $appSetting) {
                if ($appSetting['SettingKey'] == 'AppImageUrl ') {
                    $appImageUrl = $appSetting['SettingValue'];
                } elseif ($appSetting['SettingKey'] == 'BackColour') {
                    $backColour = isset($appSetting['SettingValue']) ? $appSetting['SettingValue'] : '';
                } elseif ($appSetting['SettingKey'] == 'TextColour') {
                    $textColour = isset($appSetting['SettingValue']) ? $appSetting['SettingValue'] : '';
                } elseif ($appSetting['SettingKey'] == 'ButtonColor') {
                    $buttonColor = isset($appSetting['SettingValue']) ? $appSetting['SettingValue'] : '';
                } elseif ($appSetting['SettingKey'] == 'ButtonTextColor') {
                    $buttonTextColor = isset($appSetting['SettingValue']) ? $appSetting['SettingValue'] : '';
                } elseif ($appSetting['SettingKey'] == 'IconColor') {
                    $iconColor = isset($appSetting['SettingValue']) ? $appSetting['SettingValue'] : '';
                } elseif ($appSetting['SettingKey'] == 'Default') {
                    $default = isset($appSetting['SettingValue']) ? $appSetting['SettingValue'] : '';
                } elseif ($appSetting['SettingKey'] == 'SelectionColor') {
                    $selectionColor = isset($appSetting['SettingValue']) ? $appSetting['SettingValue'] : '';
                } elseif ($appSetting['SettingKey'] == 'MutedSelection') {
                    $mutedSelection = isset($appSetting['SettingValue']) ? $appSetting['SettingValue'] : '';
                }
            }
        }

        $fieldset->addField(
            'register_key',
            'text',
            ['name' => 'register_key',
                'label' => __('Registration Key'),
                'title' => __('Registration Key'),
                'disabled' => 1]
        );

        if ($registerKey) {
            $model->setData('register_key', $registerKey);
        }

        $fieldset->addField(
            'organisation_name',
            'text',
            ['name' => 'organisation_name',
                'label' => __('Organisation Name'),
                'title' => __('Organisation Name'),
                'required' => true,
                'disabled' => $isElementDisabled]
        );
        if ($organisationName) {
            $model->setData('organisation_name', $organisationName);
        }

        $fieldset->addField(
            'contact_name',
            'text',
            ['name' => 'contact_name',
                'label' => __('Contact Name'),
                'title' => __('Contact Name'),
                'required' => true,
                'disabled' => $isElementDisabled]
        );

        if ($contactName) {
            $model->setData('contact_name', $contactName);
        }

        $fieldset->addField(
            'email_id',
            'text',
            ['name' => 'email_id',
                'label' => __('Email Id'),
                'title' => __('Email Id'),
                'required' => true,
                'disabled' => $isElementDisabled]
        );

        if ($emailId) {
            $model->setData('email_id', $emailId);
        }

        $fieldset->addField(
            'phone_number',
            'text',
            ['name' => 'phone_number',
                'label' => __('Phone Number'),
                'title' => __('Phone Number'),
                'required' => true,
                'disabled' => $isElementDisabled]
        );

        if ($contactNumber) {
            $model->setData('phone_number', $contactNumber);
        }

        $fieldset->addField(
            'contact_name_2',
            'text',
            ['name' => 'contact_name_2',
                'label' => __('Second Contact Name'),
                'title' => __('Second Contact Name'),
                'disabled' => $isElementDisabled]
        );

        if ($contactName2) {
            $model->setData('contact_name_2', $contactName2);
        }

        $fieldset->addField(
            'email_id_2',
            'text',
            ['name' => 'email_id_2',
                'label' => __('Second Email Id'),
                'title' => __('Second Email Id'),
                'disabled' => $isElementDisabled]
        );

        if ($emailId2) {
            $model->setData('email_id_2', $emailId2);
        }

        $fieldset->addField(
            'phone_number_2',
            'text',
            ['name' => 'phone_number_2',
                'label' => __('Second Phone Number'),
                'title' => __('Second Phone Number'),
                'disabled' => $isElementDisabled]
        );

        if ($phoneNumber2) {
            $model->setData('phone_number_2', $phoneNumber2);
        }


        $form->setValues($model->getData());
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
        return __('Configuration Details');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Configuration Details');
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
     * @param string $resourceId
     *
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

}
