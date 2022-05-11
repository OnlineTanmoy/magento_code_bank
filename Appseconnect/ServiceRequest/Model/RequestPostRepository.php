<?php
namespace Appseconnect\ServiceRequest\Model;


use Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterfaceFactory;
use Appseconnect\ServiceRequest\Api\Service\Data\ServiceSearchResultsInterfaceFactory;
use Appseconnect\ServiceRequest\Api\Service\ServiceRepositoryInterface;
use Appseconnect\ServiceRequest\Model\RequestPostFactory;
use Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory as RequestPostCollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class RequestPostRepository
 * @package Appseconnect\ServiceRequest\Model
 */
class RequestPostRepository extends \Magento\Framework\Model\AbstractModel implements ServiceRepositoryInterface
{
    /**
     * @var \Appseconnect\ServiceRequest\Model\RequestPostFactory
     */
    protected $requestPostFactory;
    /**
     * @var RequestPostCollectionFactory
     */
    protected $requestPostCollectionFactory;
    /**
     * @var ServiceInterfaceFactory
     */
    protected $serviceInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var ServiceSearchResultsInterfaceFactory
     */
    protected $serviceSearchResultsInterfaceFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var ExtensibleDataObjectConverter
     */
    public $extensibleDataObjectConverter;

    /**
     * RequestPostRepository constructor.
     * @param \Appseconnect\ServiceRequest\Model\RequestPostFactory $requestPostFactory
     * @param RequestPostCollectionFactory $requestPostCollectionFactory
     * @param ServiceInterfaceFactory $serviceInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ServiceSearchResultsInterfaceFactory $serviceSearchResultsInterfaceFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Appseconnect\ServiceRequest\Helper\ServiceRequest\Email $helperServiceEmail
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        RequestPostFactory $requestPostFactory,
        RequestPostCollectionFactory $requestPostCollectionFactory,
        ServiceInterfaceFactory $serviceInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        ServiceSearchResultsInterfaceFactory $serviceSearchResultsInterfaceFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Appseconnect\ServiceRequest\Helper\ServiceRequest\Email $helperServiceEmail,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTime $date

    ) {
        $this->requestPostFactory = $requestPostFactory;
        $this->requestPostCollectionFactory = $requestPostCollectionFactory;
        $this->serviceInterfaceFactory = $serviceInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->serviceSearchResultsInterfaceFactory = $serviceSearchResultsInterfaceFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->customerFactory = $customerFactory;
        $this->helperServiceEmail = $helperServiceEmail;
        $this->_storeManager = $storeManager;
        $this->date = $date;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Create Update Service Request Data
     *
     * @param \Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface $service
     * @return \Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveData(\Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface $service){
        $customerId = $service->getCustomerId();
        $serviceDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray($service, [], 'Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface');

        if (!($this->customerRepositoryInterface
            ->getById($customerId)
            ->getId())) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Customer ID doesn't exist", $customerId)
            );
        }
        if($service->getStatus() == '') {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Service status not provided !")
            );
        } elseif (!in_array(intval($service->getStatus()), range(1, 11))) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Service status invalid")
            );
        }
        $requestModel = $this->requestPostFactory->create();

        $primaryCustomerId = null;
        $raId = null;
        if($service->getEntityId()){
            $requestModel->load($service->getEntityId());
            $contactPersonId = $requestModel->getContactPersonId();
            $primaryCustomerId = $requestModel->getData('customer_id');
            $raId = $requestModel->getData('ra_id');
        } else {
            $contactPersonId = $serviceDataArray['contact_person_id'];
            $primaryCustomerId = $serviceDataArray['customer_id'];
            $raId = $serviceDataArray['ra_id'];
        }
        try{
            // status date update
            $statusDate[1]['date'] = "draft_date";
            $statusDate[2]['date'] = "submit_date";
            $statusDate[3]['date'] = "transit_date";
            $statusDate[4]['date'] = "service_date";
            $statusDate[9]['date'] = "complete_date";
            $statusDate[10]['date'] = "complete_date";

            // identify status jump and update every previous status from the present status
            if ($service->getEntityId() && $requestModel->getId()) {
                $currentStatus = $requestModel->getStatus();
                if (intval($currentStatus) < intval($serviceDataArray['status'])) {
                    for ($i = $currentStatus; $i < $serviceDataArray['status'];) {
                        $i++;
                        if (isset($statusDate[$i]['date'])) {
                            if ($i == 4 && empty($requestModel->getData("service_date"))) {
                                $requestModel->setData($statusDate[$i]['date'], $this->date->gmtDate())->save();
                                $statusDate[4]['firstTime'] = true;
                            } elseif ($i == 9 && empty($requestModel->getData("complete_date"))) {
                                $requestModel->setData($statusDate[$i]['date'], $this->date->gmtDate())->save();
                                $statusDate[9]['firstTime'] = true;
                            } elseif ($i == 10) {
                                $requestModel->setData("complete_date", $this->date->gmtDate())->save();
                                $statusDate[10]['firstTime'] = true;
                            } else {
                                $requestModel->setData($statusDate[$i]['date'], $this->date->gmtDate())->save();
                            }
                            $requestModel->setData('status', $i);
                        }
                        $requestModel->save();
                    }
                }
            }


            $requestModel->setData($serviceDataArray);
            $requestModel->save();

            if ($contactPersonId) {
                // load primary customer
                $b2bCustomer = $this->customerFactory->create()->load($primaryCustomerId);
                $b2bCustomerName = $b2bCustomer->getFirstname() . ' ' . $b2bCustomer->getLastname();
                $b2bCustomerEmail = $b2bCustomer->getEmail();

                $contactPerson = $this->customerFactory->create()->load($contactPersonId);
                $emailTempVariables = [
                    'customer_name' => $b2bCustomerName,
                    'service_number' => $raId
                ];

                // CP Details
                $receiverInfoCP = [
                    'name' => $contactPerson->getFirstname() . ' ' . $contactPerson->getLastname(),
                    'email' => $contactPerson->getEmail()
                ];

                // Custom Email 2
                $custom2Name = $this->scopeConfig->getValue('trans_email/ident_custom2/name', 'store');
                $custom2Email = $this->scopeConfig->getValue('trans_email/ident_custom2/email', 'store');
                $receiverInfoCustom = [
                    'name' => $custom2Name,
                    'email' => $custom2Email
                ];

                // BP
                $receiverInfoBP = [
                    'name' => $b2bCustomerName,
                    'email' => $b2bCustomerEmail
                ];

                // send mail for the first time change
                if (in_array($requestModel->getStatus(), [9, 10]) && ($statusDate[9]['firstTime'] == true || $statusDate[10]['firstTime'] == true)) { // complete or close without repair
                    // for both for BP and CP
                    $this->helperServiceEmail->yourCustomMailSendMethod(
                        $emailTempVariables,
                        $receiverInfoCP,
                        'complete'
                    );
                    $this->helperServiceEmail->yourCustomMailSendMethod(
                        $emailTempVariables,
                        $receiverInfoCustom,
                        'complete'
                    );
                    $this->helperServiceEmail->yourCustomMailSendMethod(
                        $emailTempVariables,
                        $receiverInfoBP,
                        'complete'
                    );

                } else if (isset($statusDate[4]['firstTime']) && $statusDate[4]['firstTime'] == true) {
                    // for both for BP and CP
                    $this->helperServiceEmail->yourCustomMailSendMethod(
                        $emailTempVariables,
                        $receiverInfoCP,
                        'in service'
                    );
                    $this->helperServiceEmail->yourCustomMailSendMethod(
                        $emailTempVariables,
                        $receiverInfoCustom,
                        'in service'
                    );
                    $this->helperServiceEmail->yourCustomMailSendMethod(
                        $emailTempVariables,
                        $receiverInfoBP,
                        'in service'
                    );
                }
            }

        }catch (\Exception $e){
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __("Error occurred, please try again. ")
            );
        }
        return $requestModel;

    }

    /**
     * Get Service Request Data by entity_id
     *
     * @param int $entityId
     * @return \Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByEntityId($entityId){
        if($entityId) {
            $postData = $this->requestPostFactory->create()->load($entityId);
            if($postData->getEntityId()){
                // update store media path
                $postData->setData('download_path', $this->_storeManager->getStore()->getUrl('pub/media/'));
                return $postData;
            }
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __($entityId." not found.")
            );        }
        throw new \Magento\Framework\Exception\CouldNotSaveException(
            __($entityId." not found.")
        );
    }

    /**
     * Get List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Appseconnect\ServiceRequest\Api\Service\Data\ServiceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->serviceSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->requestPostCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrdersData = $searchCriteria->getSortOrders();
        if ($sortOrdersData) {
            foreach ($sortOrdersData as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $preorderItem = array();
        foreach ($collection as $testModel) {
            $testData = $this->serviceInterfaceFactory->create();
            $testData->setData('download_path', $this->_storeManager->getStore()->getUrl('pub/media/'));
            $testModelData = $testModel->getData();
            $this->dataObjectHelper->populateWithArray(
                $testData,
                $testModelData,
                'Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface'
            );
            $preorderItem[] = $this->dataObjectProcessor->buildOutputDataArray(
                $testData,
                'Appseconnect\ServiceRequest\Api\Service\Data\ServiceInterface'
            );
        }

        $searchResults->setItems($preorderItem);
        return $searchResults;
    }
}
