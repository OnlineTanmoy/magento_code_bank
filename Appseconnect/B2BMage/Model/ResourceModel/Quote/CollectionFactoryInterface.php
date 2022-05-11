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
namespace Appseconnect\B2BMage\Model\ResourceModel\Quote;

/**
 * Interface CollectionFactoryInterface
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
interface CollectionFactoryInterface
{

    /**
     * Create class instance with specified parameters
     *
     * @param int $customerId CustomerId
     *
     * @return \Appseconnect\B2BMage\Model\ResourceModel\Quote\Collection
     */
    public function create($customerId = null);
}
