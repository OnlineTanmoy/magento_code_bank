<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Helper\Quotation;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'section/group/insync_custom_mail_custom_mail_custom_email_type';

    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * StateInterface
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    public $inlineTranslation;

    /**
     * TransportBuilder
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    public $transportBuilder;

    /**
     * String
     *
     * @var string
     */
    public $tempId;

    /**
     * Email constructor.
     *
     * @param \Magento\Framework\App\Helper\Context              $context           Context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig       ScopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager      StoreManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation InlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder  $transportBuilder  TransportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path    Path
     * @param int    $storeId StoreId
     *
     * @return mixed
     */
    public function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * Return template id according to store
     *
     * @param Mixed $xmlPath XmlPath
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue(
            $xmlPath, $this->getStore()
                ->getStoreId()
        );
    }

    /**
     * [generateTemplate description] with template file and tempaltes variables values
     *
     * @param Mixed $emailTemplateVariables EmailTemplateVariables
     * @param Mixed $senderInfo             SenderInfo
     * @param Mixed $receiverInfo           ReceiverInfo
     *
     * @return void
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template = $this->transportBuilder->setTemplateIdentifier($this->tempId)
            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }

    /**
     * [sendInvoicedOrderEmail description]
     *
     * @param Mixed $emailTemplateVariables EmailTemplateVariables
     * @param Mixed $senderInfo             SenderInfo
     * @param Mixed $receiverInfo           ReceiverInfo
     *
     * @return void
     */
    public function yourCustomMailSendMethod($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $templateID = $this->scopeConfig->getValue('insync_quotes/email/type', 'store');
        
        $this->tempId = $templateID;
        
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
