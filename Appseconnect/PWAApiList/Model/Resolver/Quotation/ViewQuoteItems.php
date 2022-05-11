<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Quotation;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\Action\Context;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface;
/**
 * View Quote Items resolver
 */
class ViewQuoteItems implements ResolverInterface
{
    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var \Appseconnect\B2BMage\Model\QuoteFactory
     */
    public $quoteFactory;

    /**
     * @var \Appseconnect\B2BMage\Model\QuoteProductFactory
     */
    public $quoteProductFactory;
    /**
     * @var QuotationRepositoryInterface
     */
    public $quotationRepository;


    /**
     * View Quote Items constructor.
     *
     * @param Context $context
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory
     * @param \Appseconnect\B2BMage\Model\QuoteProductFactory $quoteProductFactory
     * @param QuotationRepositoryInterface $quotationRepository
     */

    public function __construct(
        Context $context,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory,
        \Appseconnect\B2BMage\Model\QuoteProductFactory $quoteProductFactory,
        QuotationRepositoryInterface $quotationRepository
    ) {
        $this->helperContactPerson = $helperContactPerson;
        $this->customerFactory = $customerFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteProductFactory = $quoteProductFactory;
        $this->quotationRepository = $quotationRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $currentCustomerId = $context->getUserId();
        $customer = $this->customerFactory->create()->load($currentCustomerId);


        if ($currentCustomerId) {
            if ($this->helperContactPerson->isContactPerson($customer)) {
                $customerId = $this->helperContactPerson->getContactCustomerId($currentCustomerId);

                if (isset($args['quote_id'])) {
                    $quoteData = $this->quoteFactory->create()->load($args['quote_id'])->getData();
                    if (!$quoteData) {
                        throw new GraphQlInputException(__("Quote ID doesn't exist"));
                    }
                } else {
                    throw new GraphQlInputException(__('Quote ID should be specified'));
                }

                $quoteModel = $this->quoteFactory->create()
                    ->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('id', $args['quote_id'])
                    ->addFieldToFilter('customer_id', $customerId)
                    ->getData();

                $status = null;
                $createdAt = null;
                $createdBy = null;
                $subtotal = null;
                $grandtotal = null;


                if (isset($quoteModel[0])) {
                    $status = $quoteModel[0]['status'];
                    $createdAt = $quoteModel[0]['created_at'];
                    $createdBy = $quoteModel[0]['contact_name'];
                    $subtotal = $quoteModel[0]['base_subtotal'];
                    $grandtotal = $quoteModel[0]['base_grand_total'];

                }

                $quoteProductData = [];

                if ($quoteModel) {
                    $quoteProductModel = $this->quoteProductFactory->create()
                        ->getCollection()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('quote_id', $args['quote_id']);

                    foreach ($quoteProductModel as $quoteItems)
                    {
                        $quoteProductData[] = [
                            'product_id' => $quoteItems->getProductId(),
                            'product_name' => $quoteItems->getName(),
                            'sku' => $quoteItems->getProductSku(),
                            'price' => $quoteItems->getPrice(),
                            'qty' => $quoteItems->getQty(),
                            'subtotal' => $quoteItems->getBaseRowTotal()
                        ];
                    }
                } else {
                    throw new GraphQlInputException(__("Access Denied"));
                }
                $quotecomment =$this->quotationRepository->get($args['quote_id']);
                $history = $quotecomment->getStatusHistoryCollection();

                $admincomment= '';
                $customercomment='';
                if (($history->getData())) {
                    foreach ($history as $historyItem) {

                        if ($historyItem->getName() == 'Admin') {
                            $admincomment = $historyItem->getComment();
                        }

                        else {
                            $customercomment = $historyItem->getComment();
                        }

                    }
                }


                $data[] = [
                    'status' => $status,
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                    'subtotal' => $subtotal,
                    'grand_total' => $grandtotal,
                    'items' => $quoteProductData,
                    'customer_comment' => $customercomment,
                    'admin_comment' => $admincomment
                ];

                return ['quotedata' => $data];
            } else {
                throw new GraphQlInputException(__("Access Denied"));
            }
        } else {
            throw new GraphQlInputException(__("Access Denied"));
        }
    }
}
