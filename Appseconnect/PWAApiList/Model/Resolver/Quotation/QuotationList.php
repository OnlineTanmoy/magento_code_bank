<?php


declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Quotation;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Appseconnect\B2BMage\Model\QuoteFactory;

/**
 * Orders data reslover
 */
class QuotationList implements ResolverInterface
{
    /**
     * @var CollectionFactoryInterface
     */
    private $collectionFactory;

    /**
     * @var \Appseconnect\B2BMage\Model\QuoteFactory
     */

    public $quoteFactory;

    /**
     * @param CollectionFactoryInterface $collectionFactory
     */
    public function __construct(
        CollectionFactoryInterface                      $collectionFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactHelper,
        \Magento\Customer\Model\CustomerFactory         $customerFactory,
        \Appseconnect\B2BMage\Model\QuoteFactory        $quoteFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->contactHelper = $contactHelper;
        $this->customerFactory = $customerFactory;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    )
    {
        $quoteData = [];
        $customer = $this->customerFactory->create()
            ->load($context->getUserId());
        if ($this->contactHelper->isContactPerson($customer)) {
            $currentcustomerId = $context->getUserId();
            $ParentcustomerId = $this->contactHelper->getContactCustomerId($context->getUserId());

            $quoteCollection = $this->quoteFactory->create()
                ->getCollection()
                ->addFieldToSelect( '*' )
                ->addFieldToFilter( 'customer_id', $ParentcustomerId )
                ->addFieldToFilter( 'contact_id', $currentcustomerId );

            foreach ($quoteCollection as $quote) {
                $quoteData[] = [
                    'quote_id' => $quote->getId(),
                    'created_at' => $quote->getCreatedAt(),
                    'created_by' => $quote->getContactName(),
                    'status' => $quote->getStatus(),
                    'quote_total' => $quote->getSubtotal()
                ];
            }
        }
        return ['quotedata' => $quoteData];
    }
}
