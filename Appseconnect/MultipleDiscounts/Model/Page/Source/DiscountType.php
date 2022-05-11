<?php

namespace Appseconnect\MultipleDiscounts\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DiscountType implements OptionSourceInterface
{
    /**
     *
     * @var \Magento\Cms\Model\Page
     */
    public $cmsPage;

    /**
     * Constructor
     *
     * @param \Magento\Cms\Model\Page $cmsPage
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
                'label' => 'Buy X item get Y item free',
                'value' => 0
            ], 1 =>  [
                'label' => 'Minimum price item discount',
                'value' => 1
            ]];
        return $result;
    }
}