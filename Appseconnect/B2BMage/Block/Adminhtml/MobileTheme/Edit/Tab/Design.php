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
 * Class Design
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Design extends Generic implements TabInterface
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
     * Design constructor.
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
                'legend' => __('Mobile Theme Information')
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
            'logo',
            'image',
            ['name' => 'logo',
                'label' => __('Logo Upload'),
                'title' => __('Logo Upload'),
                'disabled' => $isElementDisabled
            ]
        );


        $jsScript = '<script type="text/javascript">
    require(["jquery", "jquery/colorpicker/js/colorpicker"], function ($) {
        $(function() {
            var $el = $("#%s");
            $el.css("backgroundColor", "#%s");

            // Attach the color picker
            $el.ColorPicker({
                color: "%s",
                onChange: function (hsb, hex, rgb) {
                    $el.css("backgroundColor", "#" + hex).val(hex);
                }
            });
        });
    });
    </script>';

        //Field for text color
        $field = $fieldset->addField(
            'text_color',
            'text',
            ['name' => 'text_color',
                'label' => __('Text Color'),
                'title' => __('Text Color'),
                'required' => true,
                'disabled' => $isElementDisabled]
        );
        if ($textColour) {
            $value = $textColour;
        } else {
            $value = $field->getData('value');
        }
        $html = sprintf($jsScript, $field->getHtmlId(), $value, $value);
        $field->setAfterElementHtml($html);

        if ($textColour) {
            $model->setData('text_color', $textColour);
        }

        //Field for background color
        $field2 = $fieldset->addField(
            'background_color',
            'text',
            ['name' => 'background_color',
                'label' => __('Background Color'),
                'title' => __('Background Color'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'default' => $backColour]
        );
        if ($backColour) {
            $value = $backColour;
        } else {
            $value = $field2->getData('value');
        }
        $html = sprintf($jsScript, $field2->getHtmlId(), $value, $value);
        $field2->setAfterElementHtml($html);

        if ($backColour) {
            $model->setData('background_color', $backColour);
        }

        //Field for button color
        $fieldButtonColor = $fieldset->addField(
            'button_color',
            'text',
            ['name' => 'button_color',
                'label' => __('Button Color'),
                'title' => __('Button Color'),
                'disabled' => $isElementDisabled]
        );
        if ($buttonColor) {
            $value = $buttonColor;
        } else {
            $value = $fieldButtonColor->getData('value');
        }
        $html = sprintf($jsScript, $fieldButtonColor->getHtmlId(), $value, $value);
        $fieldButtonColor->setAfterElementHtml($html);

        if ($buttonColor) {
            $model->setData('button_color', $buttonColor);
        }

        //Field for Button text color
        $fieldButtonTextColor = $fieldset->addField(
            'button_text_color',
            'text',
            ['name' => 'button_text_color',
                'label' => __('Button Text Color'),
                'title' => __('Button Text Color'),
                'disabled' => $isElementDisabled]
        );
        if ($buttonTextColor) {
            $value = $buttonTextColor;
        } else {
            $value = $fieldButtonTextColor->getData('value');
        }
        $html = sprintf($jsScript, $fieldButtonTextColor->getHtmlId(), $value, $value);
        $fieldButtonTextColor->setAfterElementHtml($html);

        if ($buttonTextColor) {
            $model->setData('button_text_color', $buttonTextColor);
        }

        //Field for Icon color
        $FieldIconColor = $fieldset->addField(
            'icon_color', 'text', ['name' => 'icon_color',
                'label' => __('Icon Color'),
                'title' => __('Icon Color'),
                'disabled' => $isElementDisabled]
        );
        if ($iconColor) {
            $value = $iconColor;
        } else {
            $value = $FieldIconColor->getData('value');
        }
        $html = sprintf($jsScript, $FieldIconColor->getHtmlId(), $value, $value);
        $FieldIconColor->setAfterElementHtml($html);

        if ($iconColor) {
            $model->setData('icon_color', $iconColor);
        }

        //Field for Default
        $FieldDefault = $fieldset->addField(
            'default', 'text', ['name' => 'default',
                'label' => __('Default'),
                'title' => __('Default'),
                'disabled' => $isElementDisabled]
        );
        if ($default) {
            $value = $default;
        } else {
            $value = $FieldDefault->getData('value');
        }
        $html = sprintf($jsScript, $FieldDefault->getHtmlId(), $value, $value);
        $FieldDefault->setAfterElementHtml($html);

        if ($default) {
            $model->setData('default', $default);
        }

        //Field for Selection Color
        $FieldSelectionColor = $fieldset->addField(
            'selection_color', 'text', ['name' => 'selection_color',
                'label' => __('Selection Color'),
                'title' => __('Selection Color'),
                'disabled' => $isElementDisabled]
        );
        if ($selectionColor) {
            $value = $selectionColor;
        } else {
            $value = $FieldSelectionColor->getData('value');
        }
        $html = sprintf($jsScript, $FieldSelectionColor->getHtmlId(), $value, $value);
        $FieldSelectionColor->setAfterElementHtml($html);

        if ($selectionColor) {
            $model->setData('selection_color', $selectionColor);
        }

        //Field for Muted Selection
        $fieldMutedSelection = $fieldset->addField(
            'muted_selection', 'text', ['name' => 'muted_selection',
                'label' => __('Muted Selection'),
                'title' => __('Muted Selection'),
                'disabled' => $isElementDisabled]
        );
        if ($mutedSelection) {
            $value = $mutedSelection;
        } else {
            $value = $fieldMutedSelection->getData('value');
        }
        $html = sprintf($jsScript, $fieldMutedSelection->getHtmlId(), $value, $value);
        $fieldMutedSelection->setAfterElementHtml($html);

        if ($mutedSelection) {
            $model->setData('muted_selection', $mutedSelection);
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
        return __('Mobile Theme Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Mobile Theme Information');
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
