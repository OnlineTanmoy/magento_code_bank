<?php
declare(strict_types=1);

namespace Appseconnect\PWACustomerapi\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;

/**
 * Retrieves the Shipping information object
 */
class Shipping implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['shipping_address'])) {
            return null;
        }
        $shippingData = $value['shipping_address'];
        $shippingAddress = [];
        $shippingAddress['shipping']['name'] = $shippingData['firstname'].' '.$shippingData['lastname'];
        $shippingAddress['shipping']['street'] = $shippingData['street'];
//        if(count($billingData['street']) > 1) {
//            $ship_address = implode(" , ",$shippingData['street']);
//            $shippingAddress['shipping']['street'] = $ship_address;
//        } else {
//            $shippingAddress['shipping']['street'] = $shippingData['street'];
//        }
        $shippingAddress['shipping']['city'] = $shippingData['city'];
        $shippingAddress['shipping']['region'] = $shippingData['region'];
        $shippingAddress['shipping']['country_id'] = $shippingData['country_id'];
        $shippingAddress['shipping']['postcode'] = $shippingData['postcode'];
        $shippingAddress['shipping']['telephone'] = $shippingData['telephone'];
        $shippingAddress['shipping']['fax'] = $shippingData['fax'];
        $shippingAddress['shipping']['company'] = $shippingData['company'];
        return $shippingAddress;
    }
}