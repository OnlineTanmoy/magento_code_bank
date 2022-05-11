<?php

namespace Appseconnect\ServiceRequest\Api\Warranty;

/**
 * Interface for managing customer specific tier price.
 * @api
 */
interface WarrantyRepositoryInterface
{

    /**
     * Create customer specific tier price.
     *
     * @param \Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantyInterface $warranty
     * @return \Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantyInterface $warranty
    );

    /**
     * Get List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Appseconnect\ServiceRequest\Api\Warranty\Data\WarrantySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
