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
namespace Appseconnect\B2BMage\Model\Entity\Attribute\Source;

/**
 * Class Options
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Options implements \Magento\Framework\Option\ArrayInterface
{
    
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\CategoryVisibility\Data
     */
    public $categoryVisibilityHelper;

    /**
     * Options constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\CategoryVisibility\Data $categoryVisibilityHelper CategoryVisibilityHelper
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\CategoryVisibility\Data $categoryVisibilityHelper
    ) {
            $this->categoryVisibilityHelper = $categoryVisibilityHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groupOptions = $this->categoryVisibilityHelper->getCustomerGroups();
        return $groupOptions;
    }
}
