<?php
/**
 * Namespace
 *
 * @category Cron
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Cron\Quotation;

/**
 * Class UpdateQuote
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpdateQuote
{
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Quotation\Data
     */
    protected $quotationHelper;

    /**
     * UpdateQuote constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper QuotationHelper
     */
    public function __construct(\Appseconnect\B2BMage\Helper\Quotation\Data $quotationHelper
    ) {
        $this->quotationHelper = $quotationHelper;
    }

    /**
     * Execute
     *
     * @return $this
     */
    public function execute()
    {
        $this->quotationHelper->updateQuote();
        return $this;

    }
}
