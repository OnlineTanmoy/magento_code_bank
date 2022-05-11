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
namespace Appseconnect\B2BMage\Model;

use Appseconnect\B2BMage\Api\Quotation\Data\QuoteStatusInterface;

/**
 * Class QuoteStatus
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuoteStatus extends \Magento\Framework\Model\AbstractModel implements QuoteStatusInterface
{

    /**
     * Status table
     *
     * @var string
     */
    public $stateTable;

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Appseconnect\B2BMage\Model\ResourceModel\QuoteStatus');
        $this->statusTable = $this->getTable('insync_quotation_status');
    }

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(QuoteStatusInterface::STATUS);
    }

    /**
     * Set Status
     *
     * @param string $status status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(QuoteStatusInterface::STATUS, $status);
    }

    /**
     * Get Label
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->getData(QuoteStatusInterface::LABEL);
    }

    /**
     * Set Label
     *
     * @param string $label label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->setData(QuoteStatusInterface::LABEL, $label);
    }

    /**
     * Get status label
     *
     * @return string|null
     */
    public function getStatusLabel()
    {
        return $this->getLabel();
    }
}
