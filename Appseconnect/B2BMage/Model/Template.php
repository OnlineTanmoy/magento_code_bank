<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model;

/**
 * Class Template
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Template extends \Magento\Email\Model\Template
{

    /**
     * Process Template
     *
     * @throws \Magento\Framework\Exception\MailException
     * @return mixed
     */
    public function processTemplate()
    {
        // Support theme fallback for email templates
        $isDesignApplied = $this->applyDesignConfig();
        $templateId = $this->getId();
        if (is_numeric($templateId)) {
            $this->load($templateId);
        } else {
            $this->loadDefault($templateId);
        }

        if (!$this->getId()) {
            throw new \Magento\Framework\Exception\MailException(
                __('Invalid transactional email code: %1', $templateId)
            );
        }

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($this->_getVars());

        if ($isDesignApplied) {
            $this->cancelDesignConfig();
        }
        return $text;
    }
    
    /**
     * Process template custom
     * 
     * @throws \Magento\Framework\Exception\MailException
     * @return mixed
     */
    public function processTemplateCustom()
    {
        // Support theme fallback for email templates
        $isDesignApplied = $this->applyDesignConfig();
        $templateId = 1;
        if (is_numeric($templateId)) {
            $this->load($templateId);
        } else {
            $this->loadDefault($templateId);
        }
    
        if (!$this->getId()) {
            throw new \Magento\Framework\Exception\MailException(
                __('Invalid transactional email code: %1', $templateId)
            );
        }
    
        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($this->_getVars());
    
        if ($isDesignApplied) {
            $this->cancelDesignConfig();
        }
        return $text;
    }
}
