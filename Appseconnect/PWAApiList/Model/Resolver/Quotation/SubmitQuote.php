<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Quotation;

use Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;


/**
 * Orders data reslover
 */
class SubmitQuote implements ResolverInterface
{
    /**
     * Quote
     *
     * @var \Appseconnect\B2BMage\Model\QuoteFactory
     */
    public $quoteFactory;
    /**
     * Quatation service
     *
     * @var \Appseconnect\B2BMage\Model\Service\QuotationService
     */
    public $quotationService;
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $contactHelper;
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var QuotationRepositoryInterface
     */
    public $quotationRepository;

    /**
     * @param \Appseconnect\B2BMage\Model\Service\QuotationService $quotationService quotation service
     * @param \Appseconnect\B2BMage\Model\QuoteFactory $quoteFactory quote
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contact person helper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory customer
     * @param QuotationRepositoryInterface $quotationRepository  QuotationRepository
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory              $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data      $contactHelper,
        \Appseconnect\B2BMage\Model\Service\QuotationService $quotationService,
        \Appseconnect\B2BMage\Model\QuoteFactory             $quoteFactory,
        QuotationRepositoryInterface $quotationRepository
    )
    {
        $this->quotationService = $quotationService;
        $this->quoteFactory = $quoteFactory;
        $this->customerFactory = $customerFactory;
        $this->contactHelper = $contactHelper;
        $this->quotationRepository = $quotationRepository;
    }

    /**
     * @inheritdoc
     */
    public
    function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    )
    {

        $customerId = $context->getUserId();
        $customer = $this->customerFactory->create()->load($customerId);

        if ($this->contactHelper->isContactPerson($customer)) {
            $quoteId = $args['input']['quote_id'];
            $quote = $this->quotationRepository->get($quoteId);
            $quote->addStatusHistoryComment($args['input']['quote_comment'], $quote->getStatus())->save();
            try {
                $this->quotationService->submitQuoteById($quoteId);
            } catch (\Exception $e) {
                throw new GraphQlInputException(__("Something went wrong while submitting the quote."));
            }

            return [
                "quote_id" => $quoteId,
            ];
        }
    }
}
