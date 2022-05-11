<?php
declare(strict_types=1);

namespace Appseconnect\PWACustomerapi\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\CatalogGraphQl\Model\Resolver\Product\Websites\Collection;

/**
 * Retrieves the Items information object
 */
class Items implements ResolverInterface
{
    /**
     * Get All Product Items of Order.
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['items'])) {
            return null;
        }
        $itemArray = [];
        foreach ($value['items'] as $key => $item) {
            $itemArray[$key]['sku'] = $item['sku'];
            $itemArray[$key]['title'] = $item['name'];
            $itemArray[$key]['price'] = $item['price'];
        }
        return $itemArray;
    }
}