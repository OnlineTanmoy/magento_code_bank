<?php
/**
 * Namespace
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Api\Quotation\Data;

/**
 * Interface QuoteStatusInterface
 *
 * @category API
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface QuoteStatusInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     * Status
     */
    const STATUS = "status";

    /**
     * Label
     */
    const LABEL = "label";

    /**
     * Gets the Status .
     *
     * @return string|null Status .
     */
    public function getStatus();

    /**
     * Sets Status .
     *
     * @param string $status status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Gets the Label .
     *
     * @return string|null Label .
     */
    public function getLabel();

    /**
     * Sets Label .
     *
     * @param string $label label
     *
     * @return $this
     */
    public function setLabel($label);
}
