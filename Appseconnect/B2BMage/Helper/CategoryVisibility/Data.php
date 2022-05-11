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
namespace Appseconnect\B2BMage\Helper\CategoryVisibility;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * CollectionFactory
     *
     * @var Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    public $groupCollectionFactory;
    
    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                         $context                Context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface            $scopeConfig            ScopeConfig
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory GroupCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * GetCategoryVisbility
     *
     * @return string
     */
    public function getCategoryVisbility()
    {
        $categoryVisibilityType = $this->scopeConfig
            ->getValue(
                'insync_category_visibility/select_visibility/select_visibility_type',
                'default'
            );
        return $categoryVisibilityType;
    }

    /**
     * GetCustomerGroups
     *
     * @return \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    public function getCustomerGroups()
    {
        $groupOptions = $this->groupCollectionFactory->create()->toOptionArray();
        return $groupOptions;
    }
}
