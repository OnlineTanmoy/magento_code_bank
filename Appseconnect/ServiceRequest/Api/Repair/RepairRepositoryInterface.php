<?php

namespace Appseconnect\ServiceRequest\Api\Repair;

/**
 * Interface RepairRepositoryInterface
 * @package Appseconnect\ServiceRequest\Api\Repair
 */
interface RepairRepositoryInterface
{

    /**
     * Create update Service Request Data
     *
     * @param \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface[] $repair
     * @return \Appseconnect\ServiceRequest\Api\Repair\Data\RepairInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveData($repair);

    /**
     * Get List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Appseconnect\ServiceRequest\Api\Repair\Data\RepairSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
