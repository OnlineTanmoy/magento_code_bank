<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Model\Quote\Product;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Repository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Repository implements \Appseconnect\B2BMage\Api\Quotation\QuotationItemRepositoryInterface
{

    /**
     * Quotation repository.
     *
     * @var \Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface
     */
    public $quotationRepository;

    /**
     * Product repository.
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * QuoteProductInterfaceFactory
     *
     * @var \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterfaceFactory
     */
    public $itemDataFactory;

    /**
     * Repository constructor.
     *
     * @param \Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface      $quotationRepository QuotationRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                       $productRepository   ProductRepository
     * @param \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterfaceFactory $itemDataFactory     ItemDataFactory
     */
    public function __construct(
        \Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface $quotationRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterfaceFactory $itemDataFactory
    ) {
        $this->quotationRepository = $quotationRepository;
        $this->productRepository = $productRepository;
        $this->itemDataFactory = $itemDataFactory;
    }

    /**
     * GetList
     *
     * @param int $quoteId QuoteId
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface[]|array
     */
    public function getList($quoteId)
    {
        $output = [];
        $quote = $this->quotationRepository->getActive($quoteId);
        foreach ($quote->getAllVisibleItems() as $item) {
            $output[] = $item;
        }
        return $output;
    }

    /**
     * Save
     *
     * @param \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface $quoteItem QuoteItem
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface
     */
    public function save(\Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface $quoteItem)
    {
        $quoteId = $quoteItem->getQuoteId();
        $quote = $this->quotationRepository->getActive($quoteId);
        
        $quoteItems = $quote->getItems();
        $quoteItems[] = $quoteItem;
        
        $quote->setItems($quoteItems);
        $this->quotationRepository->save($quote);
        return $quote->getLastAddedItem();
    }

    /**
     * DeleteById
     *
     * @param int $quoteId QuoteId
     * @param int $itemId  ItemId
     *
     * @return bool
     */
    public function deleteById($quoteId, $itemId)
    {
        $quote = $this->quotationRepository->getActive($quoteId);
        $quoteItem = $quote->getItemById($itemId);
        if (! $quoteItem) {
            throw new NoSuchEntityException(__('Quote %1 doesn\'t contain item  %2', $quoteId, $itemId));
        }
        try {
            $quote->removeItem($itemId);
            $this->quotationRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not remove item from quote'));
        }
        
        return true;
    }
}
