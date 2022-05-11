<?php

namespace Appseconnect\ServiceRequest\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    /**
     * @var \Magento\Theme\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(EavSetupFactory $eavSetupFactory,
                                \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionFactory,
                                \Magento\Theme\Model\Config $config,
                                \Magento\Cms\Model\BlockFactory $blockFactory,
                                \Magento\Cms\Model\PageFactory $pageFactory,
                                \Magento\Framework\App\Config\ConfigResource\ConfigInterface  $resourceConfig,
                                \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->_blockFactory = $blockFactory;
        $this->pageFactory = $pageFactory;
        $this->resourceConfig = $resourceConfig;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if(version_compare($context->getVersion(), '0.0.4') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'show_in_service', [
                'type' => 'int',
                'label' => 'Show In Service',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'visible' => true,
                'default' => '0',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Display Settings',
            ]);

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY, 'serial_image', [
                    'type' => 'varchar',
                    'label' => 'Product Serial Image',
                    'input' => 'image',
                    'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'required' => false,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
            );

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY, 'sku_image', [
                    'type' => 'varchar',
                    'label' => 'Product Sku Image',
                    'input' => 'image',
                    'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'required' => false,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
            );
        }
    }
}
