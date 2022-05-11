<?php
declare(strict_types=1);

namespace Appseconnect\PWACustomerapi\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;

/**
 * Retrieves the Billing information object
 */
class Billing implements ResolverInterface
{
    /**
     * @inheritdoc
     */

    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['billing_address'])) {
            return null;
        }
        $billingData = $value['billing_address'];
        $billingAddress = [];
        $billingAddress['billing']['name'] = $billingData['firstname'].' '.$billingData['lastname'];
        //$this->logger->info(json_encode($billingData['street']));
//        if(count($billingData['street']) > 1) {
//            $bill_address = implode(" , ",$billingData['street']);
//            $billingAddress['billing']['street'] = $bill_address;
//        } else {
//            $billingAddress['billing']['street'] = $billingData['street'];
//        }
        $billingAddress['billing']['street'] = $billingData['street'];
        $billingAddress['billing']['city'] = $billingData['city'];
        $billingAddress['billing']['region'] = $billingData['region'];
        $billingAddress['billing']['country_id'] = $billingData['country_id'];
        $billingAddress['billing']['postcode'] = $billingData['postcode'];
        $billingAddress['billing']['telephone'] = $billingData['telephone'];
        $billingAddress['billing']['fax'] = $billingData['fax'];
        $billingAddress['billing']['company'] = $billingData['company'];
        return $billingAddress;
    }
}