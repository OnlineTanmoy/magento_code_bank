<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Block\Adminhtml\Quotation\Quote\View;

use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Appseconnect\B2BMage\Model\Quote;

/**
 * Class Messages
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Messages extends \Magento\Framework\View\Element\Messages
{

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * QuoteProduct
     *
     * @var \Appseconnect\B2BMage\Model\QuoteProduct
     */
    public $quoteItems;

    /**
     * Messages constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context                Context
     * @param \Appseconnect\B2BMage\Model\QuoteProduct         $quoteItems             QuoteItems
     * @param \Magento\Framework\Message\Factory               $messageFactory         MessageFactory
     * @param \Magento\Framework\Message\CollectionFactory     $collectionFactory      CollectionFactory
     * @param \Magento\Framework\Message\ManagerInterface      $messageManager         MessageManager
     * @param InterpretationStrategyInterface                  $interpretationStrategy InterpretationStrategy
     * @param \Magento\Framework\Registry                      $registry               Registry
     * @param array                                            $data                   Data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Appseconnect\B2BMage\Model\QuoteProduct $quoteItems,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
        $this->quoteItems = $quoteItems;
        $this->coreRegistry = $registry;
    }

    /**
     * Retrieve quote model instance
     *
     * @return Quote
     */
    public function _getQuote()
    {
        return $this->coreRegistry->registry('insync_customer_quote');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        /**
         * Check Item products existing
         */
        $productIds = [];
        $quoteItems = $this->quoteItems->getCollection()
            ->addFieldToFilter(
                'quote_id',
                $this->_getQuote()
                    ->getId()
            )
            ->getData();
        foreach ($quoteItems as $item) {
            $productIds[] = $item['product_id'];
        }
        
        return parent::_prepareLayout();
    }
}
