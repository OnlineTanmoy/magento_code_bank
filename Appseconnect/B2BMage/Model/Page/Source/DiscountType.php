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
namespace Appseconnect\B2BMage\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DiscountType
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DiscountType implements OptionSourceInterface
{
    /**
     * Page
     *
     * @var \Magento\Cms\Model\Page
     */
    public $cmsPage;

    /**
     * DiscountType constructor.
     *
     * @param \Magento\Cms\Model\Page $cmsPage CmsPage
     */
    public function __construct(\Magento\Cms\Model\Page $cmsPage)
    {
        $this->cmsPage = $cmsPage;
    }
    
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result =  [
                0 =>  [
                    'label' => 'Fixed',
                    'value' => 0
                ], 1 =>  [
                    'label' => 'Percentage',
                    'value' => 1
                ]];
        return $result;
    }
}
