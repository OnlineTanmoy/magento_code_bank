<?php
namespace Appseconnect\DisableCategoryProduct\Block\Html;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    public function getCacheLifetime()
    {
        return null;
    }
}
