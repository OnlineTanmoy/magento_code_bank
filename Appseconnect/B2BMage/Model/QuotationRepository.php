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

use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Session;
use Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\ResourceModel\Order as Resource;
use Appseconnect\B2BMage\Model\QuotationRepository\SaveHandler;
use Appseconnect\B2BMage\Model\QuotationRepository\LoadHandler;
use Appseconnect\B2BMage\Model\ResourceModel\Metadata;
use Appseconnect\B2BMage\Model\Quote;
use Magento\Sales\Model\Order\ShippingAssignmentBuilder;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SortOrder;

/**
 * Class QuotationRepository
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuotationRepository implements QuotationRepositoryInterface
{

    /**
     * Quotes by id
     *
     * @var Quote[]
     */
    public $quotesById = [];

    /**
     * Quotes by contact id
     *
     * @var Quote[]
     */
    public $quotesByContactId = [];

    /**
     * Quote
     *
     * @var QuoteFactory
     */
    public $quoteFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * Meta data
     *
     * @var Metadata
     */
    public $metadata;

    /**
     * Search result factory
     *
     * @var SearchResultFactory
     */
    public $searchResultFactory = null;

    /**
     * QuoteInterface[]
     *
     * @var array
     */
    public $registry = [];

    /**
     * Save handeler
     *
     * @var SaveHandler
     */
    public $saveHandler;

    /**
     * Load handler
     *
     * @var LoadHandler
     */
    public $loadHandler;

    /**
     * QuotationRepository constructor.
     *
     * @param Metadata                                          $metadata            metadata
     * @param Session                                           $customerSession     customer session
     * @param \Magento\Framework\Event\ManagerInterface         $eventManager        event manager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository  customer repository
     * @param QuoteFactory                                      $quoteFactory        quote factory
     * @param StoreManagerInterface                             $storeManager        strore manager
     * @param SearchResultFactory                               $searchResultFactory search result factory
     */
    public function __construct(
        Metadata $metadata,
        Session $customerSession,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        SearchResultFactory $searchResultFactory
    ) {
    
        $this->metadata = $metadata;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->quoteFactory = $quoteFactory;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Load entity
     *
     * @param int $id id
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (! $id) {
            throw new InputException(__('Id required'));
        }
        if (! isset($this->registry[$id])) {

            $entity = $this->metadata->getNewInstance()->load($id);
            $this->_getLoadHandler()->load($entity);
            if (! $entity->getId()) {
                throw new NoSuchEntityException(
                    __('Requested entity doesn\'t exist')
                );
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /**
     * Get for contact
     *
     * @param int   $contactPersonId contact person id
     * @param array $sharedStoreIds  shared store id
     *
     * @return QuoteInterface|\Appseconnect\B2BMage\Model\Quote
     */
    public function getForContact($contactPersonId, array $sharedStoreIds = [])
    {
        if (! isset($this->quotesByContactId[$contactPersonId])) {
            $quote = $this->loadQuote(
                'loadByContact',
                'contactPersonId',
                $contactPersonId,
                $sharedStoreIds
            );
            $this->_getLoadHandler()->load($quote);
            $this->quotesById[$quote->getId()] = $quote;
            $this->quotesByContactId[$contactPersonId] = $quote;
        }
        return $this->quotesByContactId[$contactPersonId];
    }

    /**
     * Load quote with different methods
     *
     * @param string $loadMethod     load method
     * @param string $loadField      load Field
     * @param int    $identifier     identifier
     * @param int[]  $sharedStoreIds Shared strore id
     *
     * @throws NoSuchEntityException
     * @return Quote
     */
    public function loadQuote(
        $loadMethod,
        $loadField,
        $identifier,
        array $sharedStoreIds = []
    ) {
        $quote = $this->quoteFactory->create();
        if ($sharedStoreIds) {
            $quote->setSharedStoreIds($sharedStoreIds);
        }
        $quote->setStoreId(
            $this->storeManager->getStore()
                ->getId()
        )
            ->$loadMethod($identifier);
        if (! $quote->getId()) {
            throw NoSuchEntityException::singleField($loadField, $identifier);
        }
        return $quote;
    }

    /**
     * Get active
     *
     * @param int   $quoteId        quote id
     * @param array $sharedStoreIds chared store id
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface
     */
    public function getActive($quoteId, array $sharedStoreIds = [])
    {
        $quote = $this->get($quoteId, $sharedStoreIds);
        if ($quote->getStatusLabel() != 'Open') {
            throw NoSuchEntityException::singleField('quoteId', $quoteId);
        }
        return $quote;
    }

    /**
     * Save
     *
     * @param QuoteInterface $quote quote
     *
     * @return void
     */
    public function save(\Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface $quote)
    {
        if ($quote->getId()) {
            $currentQuote = $this->get($quote->getId());
            
            foreach ($currentQuote->getData() as $key => $value) {
                $quote->setData($key, $value);
            }
        }
        
        $flag = $this->customerSession->isLoggedIn() ? false : true;
        $this->_getSaveHandler()->save($quote, $flag);
        unset($this->registry[$quote->getId()]);
    }

    /**
     * Get list
     *
     * @param SearchCriteriaInterface $searchCriteria search criteria
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->quoteFactory->create()->getCollection();
        
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {

            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC)
                    ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $exclusiveProducts = [];

        $quotes = array();
        foreach ($collection as $quotationModel) {
            $quotes[] = $quotationModel->getDataModel();
        }
        $searchResults->setItems($quotes);
        return $searchResults;
    }

    /**
     * Get active for contact
     *
     * @param int   $contactPersonId contact person id
     * @param array $sharedStoreIds  shared store id
     *
     * @return \Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface
     */
    public function getActiveForContact($contactPersonId, array $sharedStoreIds = [])
    {
        $quote = $this->getForContact($contactPersonId, $sharedStoreIds);
        if ($quote->getStatusLabel() != 'Open') {
            throw NoSuchEntityException::singleField('contactPersonId', $contactPersonId);
        }
        return $quote;
    }

    /**
     * Create empty quote for contact
     *
     * @param int $contactPersonId contact person id
     *
     * @return int|null
     */
    public function createEmptyQuoteForContact($contactPersonId)
    {
        $storeId = $this->storeManager->getStore()->getStoreId();
        $quote = $this->createContactQuote($contactPersonId);
        
        try {
            $this->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Cannot create quote'));
        }
        return $quote->getId();
    }

    /**
     * Create contact quote
     *
     * @param int $contactPersonId contact person id
     *
     * @return \Appseconnect\B2BMage\Model\Quote
     */
    public function createContactQuote($contactPersonId)
    {
        $contactPerson = $this->customerRepository->getById($contactPersonId);
        
        try {
            $quote = $this->getActiveForContact($contactPersonId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {

            $quote = $this->quoteFactory->create();
            $quote->setCreatedAt(date("Y-m-d H:i:s"));
            $quote->setStatus('open');
            $quote->setStore($this->storeManager->getStore());
            $quote->setCustomer($contactPerson);
            $quote->setCustomerIsGuest(0);
        }
        return $quote;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup                  $filterGroup filter group
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Quote\Collection $collection  collection
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Appseconnect\B2BMage\Model\ResourceModel\Quote\Collection $collection
    ) {
    
        $fields = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $collection->addFieldToFilter($filter->getField(), $filter->getValue());
        }
    }

    /**
     * Get new SaveHandler dependency for application code.
     *
     * @return SaveHandler
     */
    private function _getSaveHandler()
    {
        if (! $this->saveHandler) {
            $this->saveHandler = ObjectManager::getInstance()->get(SaveHandler::class);
        }
        return $this->saveHandler;
    }

    /**
     * Get load handler
     *
     * @return LoadHandler
     */
    private function _getLoadHandler()
    {
        if (! $this->loadHandler) {
            $this->loadHandler = ObjectManager::getInstance()->get(LoadHandler::class);
        }
        return $this->loadHandler;
    }
}
