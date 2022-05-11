<?php
namespace Appseconnect\ServiceRequest\Helper\ServiceRequest;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'section/group/insync_custom_mail_custom_mail_custom_email_type';

    /**
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
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    public $inlineTranslation;

    /**
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    public $transportBuilder;

    /**
     *
     * @var string
     */
    public $tempId;

    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
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
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    public function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
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
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()
            ->getStoreId());
    }

    /**
     * [generateTemplate description] with template file and tempaltes variables values
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @return void
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template = $this->transportBuilder->setTemplateIdentifier($this->tempId)
            ->setTemplateOptions([
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
            ])
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }

    /**
     * [sendInvoicedOrderEmail description]
     *
     * @param mixed $emailTemplateVariables
     * @param mixed $senderInfo
     * @param mixed $receiverInfo
     * @param mixed $action
     * @return void
     */
    public function yourCustomMailSendMethod(
        $emailTemplateVariables,
        $receiverInfo,
        $action = null
    ) {

        $senderName = $this->scopeConfig->getValue('trans_email/ident_sales/name', 'store');
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_sales/email', 'store');

        $senderInfo = [
            'name' => $senderName,
            'email' => $senderEmail
        ];
    
        if ($action == 'submited') {
            $templateID = $this->scopeConfig->getValue('insync_service/service_email/submited', 'store');
        } elseif ($action == 'in service') {
            $templateID = $this->scopeConfig->getValue('insync_service/service_email/in_service', 'store');
        } elseif ($action == 'complete') {
            $templateID = $this->scopeConfig->getValue('insync_service/service_email/completed', 'store');
        }
        
        $this->tempId = $templateID;
        
        $this->inlineTranslation->suspend();
        if(isset($senderInfo) && isset($receiverInfo)) {
            $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        }
        $this->inlineTranslation->resume();
    }
}
