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
 * Class Website
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Website implements OptionSourceInterface
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
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    public $websiteCollection;

    /**
     * Website constructor.
     *
     * @param \Magento\Cms\Model\Page                                      $cmsPage           CmsPage
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollection WebsiteCollection
     */
    public function __construct(
        \Magento\Cms\Model\Page $cmsPage,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollection
    ) {
    
        $this->cmsPage = $cmsPage;
        $this->websiteCollection = $websiteCollection;
    }
    
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $output = $this->websiteCollection->create()->getData();
        $result =  [];
        foreach ($output as $val) {
            $result [] =  [
                    'label' => $val ['name'],
                    'value' => $val ["website_id"]
            ];
        }
        return $result;
    }
}
