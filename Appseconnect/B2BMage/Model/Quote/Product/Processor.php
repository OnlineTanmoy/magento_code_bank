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

use Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface;
use Appseconnect\B2BMage\Model\QuoteProduct;
use Appseconnect\B2BMage\Model\QuoteProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Processor
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Processor
{

    /**
     * QuoteProductFactory
     *
     * @var \Appseconnect\B2BMage\Model\QuoteProductFactory
     */
    public $quoteProductFactory;

    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * State
     *
     * @var \Magento\Framework\App\State
     */
    public $appState;

    /**
     * Processor constructor.
     *
     * @param QuoteProductFactory   $quoteProductFactory QuoteProductFactory
     * @param StoreManagerInterface $storeManager        StoreManager
     * @param State                 $appState            AppState
     */
    public function __construct(
        QuoteProductFactory $quoteProductFactory,
        StoreManagerInterface $storeManager,
        State $appState
    ) {

        $this->quoteProductFactory = $quoteProductFactory;
        $this->storeManager = $storeManager;
        $this->appState = $appState;
    }

    /**
     * Initialize quote item object
     *
     * @param Product                       $product Product
     * @param \Magento\Framework\DataObject $request Request
     *
     * @return \Appseconnect\B2BMage\Model\QuoteProduct
     */
    public function init(Product $product, $request)
    {
        $item = $this->quoteProductFactory->create();
        if ($request->getSuperAttribute()) {
            $jsonValue = json_encode($request->getSuperAttribute());
            $item->setSuperAttribute($jsonValue);
        }

        /**
         * We can't modify existing child items
         */
        if ($item->getId() && $product->getParentProductId()) {
            return $item;
        }

        $item->setOptions($product->getCustomOptions());
        $item->setProduct($product);

        if ($request->getResetCount()
            && !$product->getStickWithinParent()
            && $item->getId() === $request->getId()
        ) {
            $item->setData(QuoteProductInterface::QTY, 0);
        }

        return $item;
    }

    /**
     * Set qty and custom price for quote item
     *
     * @param QuoteProduct                  $item      Item
     * @param \Magento\Framework\DataObject $request   Request
     * @param Product                       $candidate Candidate
     *
     * @return void
     */
    public function prepare(QuoteProduct $item, DataObject $request, Product $candidate)
    {
        /**
         * We specify qty after we know about parent (for stock)
         */
        if ($request->getResetCount()
            && !$candidate->getStickWithinParent()
            && $item->getId() == $request->getId()
        ) {
            $item->setData(QuoteProductInterface::QTY, 0);
        }
        $item->addQty($candidate->getCartQty());
    }

    /**
     * Set store_id value to quote item
     *
     * @param Product $item Item
     *
     * @return void
     */
    public function setProductStoreId(Product $item)
    {
        if ($this->appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            $storeId = $this->storeManager->getStore(
                $this->storeManager->getStore()
                    ->getId()
            )
                ->getId();
            $item->setStoreId($storeId);
        } else {
            $item->setStoreId(
                $this->storeManager->getStore()
                    ->getId()
            );
        }
    }
}
