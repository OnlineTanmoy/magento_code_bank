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

use Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class QuoteProductPersister
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteProductPersister
{

    /**
     * Factory
     *
     * @var \Magento\Framework\DataObject\Factory
     */
    public $objectFactory;

    /**
     * CartItemOptionsProcessor
     *
     * @var CartItemOptionsProcessor
     */
    public $cartItemOptionProcessor;

    /**
     * ProductRepositoryInterface
     *
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * QuoteProductPersister constructor.
     *
     * @param ProductRepositoryInterface            $productRepository       ProductRepository
     * @param \Magento\Framework\DataObject\Factory $objectFactory           ObjectFactory
     * @param CartItemOptionsProcessor              $cartItemOptionProcessor CartItemOptionProcessor
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\DataObject\Factory $objectFactory,
        CartItemOptionsProcessor $cartItemOptionProcessor
    ) {
        $this->objectFactory = $objectFactory;
        $this->productRepository = $productRepository;
        $this->cartItemOptionProcessor = $cartItemOptionProcessor;
    }

    /**
     * Save
     *
     * @param QuoteInterface        $quote Quote
     * @param QuoteProductInterface $item  Item
     * @param bool                  $flag  Flag
     *
     * @return \Appseconnect\B2BMage\Model\QuoteProduct
     */
    public function save(QuoteInterface $quote, QuoteProductInterface $item, $flag = false)
    {
        $params = $this->objectFactory->create();
        $qty = $item->getQty();

        if (! is_numeric($qty) || $qty <= 0) {
            throw InputException::invalidFieldValue('qty', $qty);
        }
        $quoteId = $item->getQuoteId();
        $itemId = $item->getId();
        $params->setData('qty', $qty);
        try {

            /**
             * Update existing item
             */
            if (isset($itemId)) {
                $currentItem = $quote->getItemById($itemId);
                if (! $currentItem) {
                    throw new NoSuchEntityException(__('Quote %1 does not contain item %2', $quoteId, $itemId));
                }
                $this->updateParams($currentItem, $item, $flag);
            } else {
                /**
                 * Add new item to shopping cart
                 */
                $product = $this->productRepository->get($item->getProductSku());
                $item = $this->processItemToCart($product, $params, $quote);
                if (is_string($item)) {
                    throw new LocalizedException(__($item));
                }
            }
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save quote'));
        }
        $itemId = $item->getId();
        foreach ($quote->getAllItems() as $quoteItem) {
            if ($itemId == $quoteItem->getId()) {
                return $quoteItem;
            }
        }
    }

    /**
     * UpdateParams
     *
     * @param \Appseconnect\B2BMage\Model\QuoteProduct $currentItem CurrentItem
     * @param \Appseconnect\B2BMage\Model\QuoteProduct $item        Item
     * @param boolean                                  $flag        Flag
     *
     * @return void
     */
    public function updateParams($currentItem, $item, $flag)
    {
        if ($flag) {
            $currentItem->setQty($item->getQty());
        }
    }

    /**
     * ProcessItemToCart
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product Product
     * @param mixed                                      $params  Params
     * @param \Appseconnect\B2BMage\Model\Quote          $quote   Quote
     *
     * @return mixed
     */
    public function processItemToCart($product, $params, $quote)
    {
        $productType = $product->getTypeId();
        $result = $quote->addProductQuoteItem($product, $params);
        return $result;
    }
}
