<?php

namespace Appseconnect\ServiceRequest\Block\Request;

use Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory;
use Magento\Customer\Model\Session;

/**
 * Class Listing
 * @package Appseconnect\ServiceRequest\Block\Request
 */
class Listing extends \Magento\Framework\View\Element\Template
{

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * @var
     */
    protected $requestPostFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var
     */
    protected $_serviceRequest;

    /**
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $contactPersonHelper;

    /**
     * Listing constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CollectionFactory $requestPostFactory
     * @param Session $customerSession
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $requestPostFactory,
        Session $customerSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data $contactPersonHelper,
        \Appseconnect\ServiceRequest\Helper\Search $searchHelper,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->requestPostFactory = $requestPostFactory;
        $this->request = $request;
        $this->contactPersonHelper = $contactPersonHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->searchHelper = $searchHelper;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        $limit = $this->getRequest()->getParam('limit');
        $limit = $limit == null ? 10 : $limit;
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'simplenews.news.list.pager'
        );
        $pager->setLimit($limit)
            ->setShowAmounts(true)
            ->setCollection($this->getServiceRequestList());
        $this->setChild('pager', $pager);
        $this->getServiceRequestList()->getData();

        return $this;
    }

    /**
     * @return $this
     */
    public function _tohtml()
    {
        $this->setTemplate("Appseconnect_ServiceRequest::request/listing.phtml");

        return parent::_toHtml();
    }


    /**
     * @return boolean|\Magento\Customer\Model\CustomerFactory
     */
    public function getServiceRequestList()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (empty($this->_searchData)) {
            $this->_searchData = $this->_coreRegistry->registry('search_data');
            if (!$this->_searchData || empty($this->_searchData)) {
                $this->_searchData = $this->searchHelper->getSearchData();
            }
        }
        $data = [];
        $data['from_date'] = date("01/m/Y");
        $data['to_date'] = date("d/m/Y");
        $data['status'] = '1'; // draft
        if ($this->_searchData) {
            $data['status'] = isset($this->_searchData['status']) ? $this->_searchData['status'] : '';
            $data['from_date'] = isset($this->_searchData['from_date']) ? $this->_searchData['from_date'] : '';
            $data['to_date'] = isset($this->_searchData['to_date']) ? $this->_searchData['to_date'] : '';
        } else {
            $this->_searchData = $data;
        }

        if (!$this->_serviceRequest) {
//            $companyId = $this->contactPersonHelper->getContactCustomerId($customerId);
            $this->_serviceRequest = $this->requestPostFactory->create()
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('entity_id', 'DESC');

            if (isset($data['from_date']) && $data['from_date']) {
                $date = str_replace('/', '-', $data['from_date']);
                $data['from_date'] = date("Y-m-d", strtotime($date));

                $this->_serviceRequest->addFieldToFilter('post',
                    ['gteq' => $data['from_date'] . ' 00:00:00']
                );
            }

            if (isset($data['to_date']) && $data['to_date'] != '') {
                $date = str_replace('/', '-', $data['to_date']);
                $data['to_date'] = date("Y-m-d", strtotime($date));

                $this->_serviceRequest->addFieldToFilter('post',
                    ['lteq' => $data['to_date'] . ' 23:59:59']
                );
            }

            if (isset($data['status']) && $data['status'] != '') {
                if (in_array($data['status'], [4,5,6,7,8])) {
                    $this->_serviceRequest->addFieldToFilter('status',
                        ['in' => array(4,5,6,7,8)]
                    );
                } else {
                    $this->_serviceRequest->addFieldToFilter('status',
                        ['eq' => $data['status']]
                    );
                }
            }

            $this->_serviceRequest
                ->getSelect()->joinLeft(array("s_status" => "insync_service_status"),
                    'main_table.status = s_status.id',
                    ['s_status.label as status_label']
                );

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sekhar.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("listing page block");
            $logger->info($this->_serviceRequest->getSelectSql(true));
        }

        return $this->_serviceRequest;
    }

    /**
     * Return orderincrement_id, invoiceincrement_id by order id
     *
     * @param $orderId
     *
     * @return array
     */
    public function getServiceInvoiceFile($orderId)
    {
        if (!isset($orderId) || is_null($orderId) || $orderId == 0) {
            return [];
        }
        $order = $this->orderRepository->get($orderId);
        if ($order) {
            $orderIncrementId = $order->getIncrementId();
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invoiceFile = $invoice->getsapInvoiceFilename();
                if(isset($invoiceFile)) {
                    $invoice_id = $invoice->getIncrementId();
                    return ['order_increment_id' => $orderIncrementId, 'invoice_increment_id' => $invoice_id];
                }
            }
        }
        return [];
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }

    /**
     * Service search URL
     *
     * @return string
     */
    public function getServiceSearchUrl()
    {
        return $this->getUrl('servicerequest/request/search');
    }

    /**
     * @return Magento\Framework\Registry
     */
    public function getSearchData()
    {
        return $this->_searchData;
    }
}
