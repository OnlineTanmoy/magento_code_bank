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
namespace Appseconnect\B2BMage\Setup;

use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class QuotationSetup
 *
 * @category B2BMage\Setup
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuotationSetup extends \Magento\Eav\Setup\EavSetup
{
    /**
     * ScopeConfigInterface
     *
     * @var ScopeConfigInterface
     */
    public $config;

    /**
     * Encryptor
     *
     * @var EncryptorInterface
     */
    public $encryptor;

    /**
     * Initialize class variable
     *
     * @param ModuleDataSetupInterface $setup                      Setup
     * @param Context                  $context                    Context
     * @param CacheInterface           $cache                      Cache
     * @param CollectionFactory        $attrGroupCollectionFactory AttrGroup CollectionFactory
     * @param ScopeConfigInterface     $config                     Config
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
        $this->encryptor = $context->getEncryptor();
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    /**
     * Get DefaultEntities
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = [
            'quotation' => [
                'entity_type_id' => 9,
                'entity_model' => 'Appseconnect\B2BMage\Model\Quotation\ResourceModel\Quote',
                'table' => 'insync_customer_quote',
                'increment_model' => 'Magento\Eav\Model\Entity\Increment\NumericValue',
                'increment_per_store' => true,
                'attributes' => [],
            ]
        ];
        return $entities;
    }

    /**
     * Get config model
     *
     * @return ScopeConfigInterface
     */
    public function getConfigModel()
    {
        return $this->config;
    }

    /**
     * Get Encryptor
     *
     * @return EncryptorInterface
     */
    public function getEncryptor()
    {
        return $this->encryptor;
    }
}
