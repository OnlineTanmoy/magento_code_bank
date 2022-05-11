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
 * Class ParentPricelist
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ParentPricelist implements OptionSourceInterface
{
    /**
     * Page
     *
     * @var \Magento\Cms\Model\Page
     */
    public $cmsPage;
    
    /**
     * CollectionFactory
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory
     */
    public $pricelistPriceCollection;

    /**
     * ParentPricelist constructor.
     *
     * @param \Magento\Cms\Model\Page                                           $cmsPage                  CmsPage
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistPriceCollection PricelistPriceCollection
     */
    public function __construct(
        \Magento\Cms\Model\Page $cmsPage,
        \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistPriceCollection
    ) {
    
        $this->cmsPage = $cmsPage;
        $this->pricelistPriceCollection = $pricelistPriceCollection;
    }
    
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $parentPricelistCollection = $this->pricelistPriceCollection->create();
        $output = $parentPricelistCollection->getData();
        $result =  [];
        $result[0] = [
                    'label' => "Base Price",
                    'value' => 0
                
        ];
        foreach ($output as $val) {
            $result [] =  [
                    'label' => $val ['pricelist_name'],
                    'value' => $val ["parent_id"]
            ];
        }
        return $result;
    }
}
