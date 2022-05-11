<?php
/**
 * Namespace
 *
 * @category Ui\Quotation
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Ui\Quotation\Component\Listing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Appseconnect\B2BMage\Model\ResourceModel\QuoteStatus\CollectionFactory;

/**
 * Class Options
 *
 * @category Ui\CustomerSpecialPrice
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Options implements OptionSourceInterface
{

    /**
     * Option Array
     *
     * @var array
     */
    public $options;

    /**
     * Collection Factory
     *
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory Collection Factory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->collectionFactory->create()->toOptionArray();
        }
        return $this->options;
    }
}
