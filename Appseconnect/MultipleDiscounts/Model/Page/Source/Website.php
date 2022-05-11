<?php

namespace Appseconnect\MultipleDiscounts\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Website implements OptionSourceInterface
{
    /**
     *
     * @var \Magento\Cms\Model\Page
     */
    public $cmsPage;

    /**
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    public $websiteCollection;

    /**
     * Constructor
     *
     * @param \Magento\Cms\Model\Page $cmsPage
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