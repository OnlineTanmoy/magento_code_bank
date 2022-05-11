<?php
/**
 * Namespace
 *
 * @category B2BMage\Setup
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\CompanyDivision\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 *
 * @category B2BMage\Setup
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * CustomerSetupFactory
     *
     * @var CustomerSetupFactory
     */
    public $customerSetupFactory;

    /**
     * EavSetupFactory
     *
     * @var EavSetupFactory
     */
    public $eavSetupFactory;

    /**
     * Initialize class variable
     *
     * @param CustomerSetupFactory $customerSetupFactory CustomerSetupFactory
     * @param EavSetupFactory $eavSetupFactory EavSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup Setup
     * @param ModuleContextInterface $context Context
     *
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.3') < 0) {
            $this->addParentRuleConfigurationAttribute($setup);
        }
    }

    /**
     * Add ParentRuleConfigurationAttribute
     *
     * @param ModuleDataSetupInterface $setup Setup
     *
     * @return void
     */
    public function addParentRuleConfigurationAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $setup
            ]
        );
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "parent_rule_configuration");

        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY, "parent_rule_configuration", [
                "type" => "int",
                "backend" => "",
                "label" => "Parent Rule Applied",
                "input" => "boolean",
                "source" => "Magento\Eav\Model\Entity\Attribute\Source\Boolean",
                "visible" => true,
                "required" => false,
                "default" => "",
                "frontend" => "",
                "unique" => false,
                "note" => "",
                "position" => 150
            ]
        );

        $usedInForms = [];

        $attributeCode = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'parent_rule_configuration'
        );
        $usedInForms[] = "adminhtml_customer";
        $usedInForms[] = "customer_account_create";
        $usedInForms[] = "customer_account_edit";

        $attributeCode->setData("used_in_forms", $usedInForms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100);

        $attributeCode->save();
        $setup->endSetup();
    }
}