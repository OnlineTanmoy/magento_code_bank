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

namespace Appseconnect\B2BMage\Controller\Adminhtml\Mobiletheme;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Store\Model\Store;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Save
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Save extends \Magento\Backend\App\Action
{


    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Mobile theme
     *
     * @var \Appseconnect\B2BMage\Model\MobilethemeFactory
     */
    public $mobilethemeFactory;

    /**
     * Mobile theme helper
     *
     * @var \Appseconnect\B2BMage\Helper\Mobiletheme\Data
     */
    public $mobileThemeData;

    /**
     * System config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Save constructor.
     *
     * @param Action\Context                                     $context             context
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager        store manager
     * @param \Appseconnect\B2BMage\Model\MobilethemeFactory     $mobilethemeFactory  mobile theme model
     * @param \Appseconnect\B2BMage\Helper\Mobiletheme\Data      $mobileThemeData     mobile theme helper
     * @param \Magento\MediaStorage\Model\File\UploaderFactory   $fileUploaderFactory file uploader
     * @param \Magento\Framework\Filesystem                      $filesystem          file system
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig         system config
     */
    public function __construct(
        Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\B2BMage\Model\MobilethemeFactory $mobilethemeFactory,
        \Appseconnect\B2BMage\Helper\Mobiletheme\Data $mobileThemeData,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->mobilethemeFactory = $mobilethemeFactory;
        $this->mobileThemeData = $mobileThemeData;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Save mobile theme
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $imagePath = '';

            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'logo']);
            $fileName  = $uploader->validateFile();
            if ($fileName['name'] != '') {
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath('images/');
                $result = $uploader->save($path);

                $imagePath = $this->getMediaBaseUrl() . 'images/' . $result['file'];
            }

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $apiUrl = $this->scopeConfig->getValue('insync_mobile/theme/api_url', $storeScope);

            if ($apiUrl == '' || !$apiUrl) {
                $this->messageManager->addError(__('Please set the mobile theme api in sytem cofiguration.'));
            }

            if (isset($data['id'])) {
                $mobileThemeModel = $this->mobilethemeFactory->create();
                $mobileThemeModel->load($data['id']);

                $make_call = $this->mobileThemeData->callAPI('GET', $this->scopeConfig->getValue('insync_mobile/theme/api_url', $storeScope) . 'Account?orgAppId=' . $mobileThemeModel->getMobileAccountId(), false);
                $response = json_decode($make_call, true);
                foreach ($response['OrganizationSettingDetails'] as $key => $appSetting) {
                    if ($appSetting['SettingKey'] == 'AppImageUrl' && $imagePath != '') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $imagePath;
                    } else if ($appSetting['SettingKey'] == 'AppBaseUrl') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $this->storeManager->getStore()->getBaseUrl();
                    } else if ($appSetting['SettingKey'] == 'BackColour') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $data['background_color'];
                    } else if ($appSetting['SettingKey'] == 'TextColour') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $data['text_color'];
                    } else if ($appSetting['SettingKey'] == 'ButtonColor') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $data['button_color'];
                    } else if ($appSetting['SettingKey'] == 'ButtonTextColor') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $data['button_text_color'];
                    } else if ($appSetting['SettingKey'] == 'IconColor') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $data['icon_color'];
                    } else if ($appSetting['SettingKey'] == 'Default') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $data['default'];
                    } else if ($appSetting['SettingKey'] == 'SelectionColor') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $data['selection_color'];
                    } else if ($appSetting['SettingKey'] == 'MutedSelection') {
                        $response['OrganizationSettingDetails'][$key]['SettingValue'] = $data['muted_selection'];
                    }
                }

                if ($data['email_id_2'] != '') {
                    $response['AlternateContactDetail'] = array(
                        "contactName" => $data['contact_name_2'],
                        "emailId" => $data['email_id_2'],
                        "phoneNumber" => $data['phone_number_2'],
                        "isActive" => null
                    );
                }
            } else {
                $make_call = $this->mobileThemeData->callAPI('GET', $this->scopeConfig->getValue('insync_mobile/theme/api_url', $storeScope) . 'App?appId=' . $this->scopeConfig->getValue('insync_mobile/theme/appid', $storeScope), false);
                $response = json_decode($make_call, true);
                foreach ($response['AppSettings'] as $key => $appSetting) {
                    if ($appSetting['SettingKey'] == 'AppImageUrl') {
                        $response['AppSettings'][$key]['SettingValue'] = $imagePath;
                    } else if ($appSetting['SettingKey'] == 'AppBaseUrl') {
                        $response['AppSettings'][$key]['SettingValue'] = $this->storeManager->getStore()->getBaseUrl();
                    } else if ($appSetting['SettingKey'] == 'BackColour') {
                        $response['AppSettings'][$key]['SettingValue'] = $data['background_color'];
                    } else if ($appSetting['SettingKey'] == 'TextColour') {
                        $response['AppSettings'][$key]['SettingValue'] = $data['text_color'];
                    } else if ($appSetting['SettingKey'] == 'ButtonColor') {
                        $response['AppSettings'][$key]['SettingValue'] = $data['button_color'];
                    } else if ($appSetting['SettingKey'] == 'ButtonTextColor') {
                        $response['AppSettings'][$key]['SettingValue'] = $data['button_text_color'];
                    } else if ($appSetting['SettingKey'] == 'IconColor') {
                        $response['AppSettings'][$key]['SettingValue'] = $data['icon_color'];
                    } else if ($appSetting['SettingKey'] == 'Default') {
                        $response['AppSettings'][$key]['SettingValue'] = $data['default'];
                    } else if ($appSetting['SettingKey'] == 'SelectionColor') {
                        $response['AppSettings'][$key]['SettingValue'] = $data['selection_color'];
                    } else if ($appSetting['SettingKey'] == 'MutedSelection') {
                        $response['AppSettings'][$key]['SettingValue'] = $data['muted_selection'];
                    }
                }

                if ($data['email_id_2'] != '') {
                    $response_altContactDetails = array(
                        "contactName" => $data['contact_name_2'],
                        "emailId" => $data['email_id_2'],
                        "phoneNumber" => $data['phone_number_2'],
                        "isActive" => null
                    );
                } else {
                    $response_altContactDetails = null;
                }

                $data_array = array(
                    "organizationDetail" => array(
                        "AppId" => "EFAEA9D5-B3A8-4B82-9670-AB8B1DB9837D",
                        "orgName" => $data['organisation_name'],
                        "phoneNumber" => $data['phone_number']
                    ),
                    "primaryContactDetail" => array(
                        "contactName" => $data['contact_name'],
                        "emailId" => $data['email_id'],
                        "phoneNumber" => $data['phone_number'],
                        "isActive" => null
                    ),
                    "alternateContactDetail" => $response_altContactDetails,
                    "organizationSettingDetails" => $response['AppSettings']
                );
            }


            try {
                $data1 = array();
                if (isset($data['id'])) {
                    $data1['id'] = $data['id'];
                    $make_call = $this->mobileThemeData->callAPI('PUT', $this->scopeConfig->getValue('insync_mobile/theme/api_url', $storeScope) . 'Account', json_encode($response));
                    $response = json_decode($make_call, true);
                } else {
                    $make_call = $this->mobileThemeData->callAPI('POST', $this->scopeConfig->getValue('insync_mobile/theme/api_url', $storeScope) . 'Account', json_encode($data_array));
                    $response = json_decode($make_call, true);
                }

                $data1['mobile_account_id'] = $response['OrganizationDetail']['OrgAppId'];
                $data1['organisation_name'] = $data['organisation_name'];
                $data1['phone_number'] = $data['phone_number'];
                $mobilethemeModel = $this->mobilethemeFactory->create();
                $mobilethemeModel->setData($data1)->save();
                $this->messageManager->addSuccess(__('The mobile theme has been saved.'));


                return $resultRedirect->setPath('*/*/edit');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the mobile theme.')
                );
            }

            return $resultRedirect->setPath('*/*/edit');
        }
        return $resultRedirect->setPath('*/*/edit');
    }

    /**
     * GetMediaBaseUrl
     *
     * @return mixed
     */
    function getMediaBaseUrl()
    {
        /**
         * $om
         *
         * @var \Magento\Framework\ObjectManagerInterface $om
         */
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        /**
         * $storeManager
         *
         * @var \Magento\Store\Model\StoreManagerInterface $storeManager
         */
        $storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');

        /**
         * $currentStore
         *
         * @var \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store $currentStore
         */
        $currentStore = $storeManager->getStore();
        return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
