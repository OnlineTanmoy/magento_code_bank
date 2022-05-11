<?php
/**
 * Namespace
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Quotation\Quote;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct\CollectionFactory as ItemCollectionFactory;

/**
 * Interface DefaultRenderer
 *
 * @category BLOCK
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Items extends \Appseconnect\B2BMage\Block\Quotation\Items\AbstractItems
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * Quote items per page.
     *
     * @var int
     */
    public $itemsPerPage;

    /**
     * Item collection
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct\CollectionFactory
     */
    public $itemCollectionFactory;

    /**
     * Catalog session
     *
     * @var $catalogSession
     */
    public $catalogSession;

    /**
     * Item session
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\QuoteProduct\Collection|null
     */
    private $itemCollection;
    
    /**
     * Session
     *
     * @var Session
     */
    public $session;

    /**
     * Items constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context               context
     * @param Session                                          $customerSession       customer session
     * @param \Magento\Framework\Registry                      $registry              registry
     * @param ItemCollectionFactory                            $itemCollectionFactory item collection
     * @param array                                            $data                  data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        \Magento\Framework\Registry $registry,
        ItemCollectionFactory $itemCollectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->session = $customerSession;
        $this->itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($context, $data);
    }
    /**
     * Init pager block and item collection with page size and current page number
     *
     * @return $this
     * @since  100.1.7
     */
    public function _prepareLayout()
    {
        $this->itemsPerPage = 3;
        
        $this->itemCollection = $this->itemCollectionFactory->create();
        $this->itemCollection->setQuoteFilter($this->getQuote());
        $this->itemCollection->filterByParent(null);

        $pagerBlock = $this->getChildBlock('quote_item_pager');
        if ($pagerBlock) {
            $pagerBlock->setLimit($this->itemsPerPage);
            $pagerBlock->setCollection($this->itemCollection);
            $pagerBlock->setAvailableLimit(
                [
                $this->itemsPerPage
                ]
            );
            $pagerBlock->setShowAmounts($this->isPagerDisplayed());
        }
        
        return parent::_prepareLayout();
    }
    
    /**
     * Get catalog session
     *
     * @return \Magento\Catalog\Model\Session
     */
    public function getCatalogSession()
    {
        $this->catalogSession = ObjectManager::getInstance()->get(
            \Magento\Catalog\Model\Session::class
        );
        return $this->catalogSession;
    }

    /**
     * Determine if the pager should be displayed for quote items list
     * To be called from templates(after _prepareLayout())
     *
     * @return bool
     * @since  100.1.7
     */
    public function isPagerDisplayed()
    {
        $pagerBlock = $this->getChildBlock('quote_item_pager');
        return $pagerBlock && ($this->itemCollection->getSize() > $this->itemsPerPage);
    }

    /**
     * Get visible items for current page.
     * To be called from templates(after _prepareLayout())
     *
     * @return \Magento\Framework\DataObject[]
     * @since  100.1.7
     */
    public function getItems()
    {
        return $this->itemCollection->getItems();
    }

    /**
     * Get pager HTML according to our requirements
     * To be called from templates(after _prepareLayout())
     *
     * @return string HTML output
     * @since  100.1.7
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('quote_item_pager');
        return $pagerBlock ? $pagerBlock->toHtml() : '';
    }
    
    /**
     * Get sales rep id
     *
     * @return NULL|in
     */
    public function getSalesrepId()
    {
        return $this->getCatalogSession()->getSalesrepId() ? $this->getCatalogSession()->getSalesrepId() : null;
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('insync_current_customer_quote');
    }
}
