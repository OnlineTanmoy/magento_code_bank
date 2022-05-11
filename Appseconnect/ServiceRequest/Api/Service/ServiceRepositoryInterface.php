<?php

namespace Appseconnect\ServiceRequest\Api\Service;

/**
 * Interface ServiceRepositoryInterface
 * @package Appseconnect\ServiceRequest\Api\Service
 */
interface ServiceRepositoryInterface
{

    /**
     * Create update Service Request Data
     *
     * @param \Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface $service
     * @return \Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveData(\Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface $service);

    /**
     * Get Service Request Data by entity_id
     *
     * @param int $entityId
     * @return \Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByEntityId($entityId);

    /**
     * Get List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Appseconnect\ServiceRequest\Api\Service\Data\ServiceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
