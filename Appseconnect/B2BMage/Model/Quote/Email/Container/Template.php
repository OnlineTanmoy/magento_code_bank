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
namespace Appseconnect\B2BMage\Model\Quote\Email\Container;

/**
 * Class Template
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Template
{
    /**
     * Array
     *
     * @var array
     */
    public $vars;

    /**
     * Array
     *
     * @var array
     */
    public $options;

    /**
     * String
     *
     * @var string
     */
    public $templateId;

    /**
     * Int
     *
     * @var int
     */
    public $id;

    /**
     * Set email template variables
     *
     * @param array $vars Vars
     *
     * @return void
     */
    public function setTemplateVars(array $vars)
    {
        $this->vars = $vars;
    }

    /**
     * Set email template options
     *
     * @param array $options Options
     *
     * @return void
     */
    public function setTemplateOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get email template variables
     *
     * @return array
     */
    public function getTemplateVars()
    {
        return $this->vars;
    }

    /**
     * Get email template options
     *
     * @return array
     */
    public function getTemplateOptions()
    {
        return $this->options;
    }

    /**
     * Set email template id
     *
     * @param int $id Id
     *
     * @return void
     */
    public function setTemplateId($id)
    {
        $this->id = $id;
    }

    /**
     * Get email template id
     *
     * @return int
     */
    public function getTemplateId()
    {
        return $this->id;
    }
}
