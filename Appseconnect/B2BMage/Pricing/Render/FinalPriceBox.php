<?php
namespace Appseconnect\B2BMage\Pricing\Render;

class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    public function getCacheLifetime()
    {
        return null;
    }
}

