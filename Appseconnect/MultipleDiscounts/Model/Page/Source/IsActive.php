<?php

namespace Appseconnect\MultipleDiscounts\Model\Page\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
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
        $options =  [];

        $options [] =  [
            'label' => 'No',
            'value' => 0
        ];
        $options [] =  [
            'label' => 'Yes',
            'value' => 1
        ];

        return $options;
    }
}