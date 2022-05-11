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

namespace Appseconnect\B2BMage\Model\ResourceModel\QuoteHistory;

/**
 * Class Collection
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Quote field for setQuoteFilter
     *
     * @var string
     */
    public $quoteField = 'parent_id';

    /**
     * Collection quote instance
     *
     * @var \Appseconnect\B2BMage\Model\Quote
     */
    public $quote;

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Appseconnect\B2BMage\Model\QuoteHistory',
            'Appseconnect\B2BMage\Model\ResourceModel\QuoteHistory'
        );
        $this->_map['fields']['id'] = 'main_table.entity_id';
    }

    /**
     * Retrieve store Id (From Quote)
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->quote->getStoreId();
    }

    /**
     * Set Quote object to Collection
     *
     * @param \Appseconnect\B2BMage\Model\Quote $quote Quote
     *
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        $quoteId = $quote->getId();
        if ($quoteId) {
            $this->addFieldToFilter('parent_id', $quote->getId());
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * After load processing
     *
     * @return $this
     */
    public function _afterLoad()
    {
        parent::_afterLoad();

        /**
         * Assign parent items
         */
        foreach ($this as $item) {
            if ($this->quote) {
                $item->setQuote($this->quote);
            }
        }

        return $this;
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Retrieve quotation as parent collection object
     *
     * @return \Appseconnect\B2BMage\Model\Quote|null
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Add quote filter
     *
     * @param int|\Appseconnect\B2BMage\Model\Quote|array $quote Quote
     *
     * @return $this
     */
    public function setQuoteFilter($quote)
    {
        if ($quote instanceof \Appseconnect\B2BMage\Model\Quote) {
            $this->setQuote($quote);
            $quoteId = $quote->getId();
            if ($quoteId) {
                $this->addFieldToFilter($this->quoteField, $quoteId);
            } else {
                $this->_totalRecords = 0;
                $this->_setIsLoaded(true);
            }
        } else {
            $this->addFieldToFilter($this->quoteField, $quoteId);
        }
        return $this;
    }

    /**
     * Filter collection by parent_item_id
     *
     * @param int $parentId ParentId
     *
     * @return $this
     */
    public function filterByParent($parentId = null)
    {
        if (empty($parentId)) {
            $this->addFieldToFilter('parent_item_id', ['null' => true]);
        } else {
            $this->addFieldToFilter('parent_item_id', $parentId);
        }
        return $this;
    }
}
