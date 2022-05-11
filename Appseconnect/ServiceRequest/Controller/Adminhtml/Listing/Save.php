<?php

namespace Appseconnect\ServiceRequest\Controller\Adminhtml\Listing;

use Appseconnect\B2BMage\Api\Pricelist\Data\ProductAssignInterfaceFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ProductRepository;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Indexer\Model\Processor
     */
    public $processor;

    /**
     * @var \Appseconnect\B2BMage\Model\PriceFactory
     */
    public $pricelistPriceFactory;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var ProductRepository
     */
    public $productRepository;

    /**
     * @var \Appseconnect\B2BMage\Model\PricelistRepository
     */
    public $pricelistRepository;

    /**
     * @var ProductAssignInterfaceFactory
     */
    public $productAssignInterfaceFactory;

    /**
     * @var Session
     */
    public $session;

    /**
     * @var \Appseconnect\ServiceRequest\Model\RequestPostFactory
     */
    public $serviceRequestPostFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @var Appseconnect\ServiceRequest\Model\ResourceModel\Warranty\CollectionFactory
     */
    public $warrantyCollectionFactory;


    public function __construct(
        ProductRepository $productRepository,
        Session $session,
        \Appseconnect\ServiceRequest\Model\RequestPostFactory $serviceRequestPostFactory,
        Action\Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Indexer\Model\Processor $processor,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\ServiceRequest\Model\ResourceModel\Warranty\CollectionFactory $warrantyCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Appseconnect\ServiceRequest\Helper\ServiceRequest\Email $helperServiceEmail
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->serviceRequestPostFactory = $serviceRequestPostFactory;
        $this->filesystem = $filesystem;
        $this->customerFactory = $customerFactory;
        $this->warrantyCollectionFactory = $warrantyCollectionFactory;
        parent::__construct($context);
        $this->processor = $processor;
        $this->date = $date;
        $this->helperServiceEmail = $helperServiceEmail;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data['status'] = $data['service_status'];

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $serviceRequestModel = $this->serviceRequestPostFactory->create();

            if (isset($data['entity_id']) && $id = $data['entity_id']) {
                $serviceRequestModel->load($id);
            } else {
                $id = '';
            }

            // status date update
            $statusDate[1]['date'] = "draft_date";
            $statusDate[2]['date'] = "submit_date";
            $statusDate[3]['date'] = "transit_date";
            $statusDate[4]['date'] = "service_date";
            $statusDate[9]['date'] = "complete_date";
            $statusDate[10]['date'] = "complete_date";

            // identify status jump and update every previous status from the present status
            if ($id) {
                $currentStatus = $serviceRequestModel->getStatus();
                if (intval($currentStatus) < intval($data['status'])) {
                    for ($i = $currentStatus; $i < $data['status'];) {
                        $i++;
                        if (isset($statusDate[$i]['date'])) {
                            if ($i == 4 && empty($serviceRequestModel->getData("service_date"))) {
                                $serviceRequestModel->setData($statusDate[$i]['date'], $this->date->gmtDate())->save();
                                $statusDate[4]['firstTime'] = true;
                            } elseif ($i == 9 && empty($serviceRequestModel->getData("complete_date"))) {
                                $serviceRequestModel->setData($statusDate[$i]['date'], $this->date->gmtDate())->save();
                                $statusDate[9]['firstTime'] = true;
                            } elseif ($i == 10) {
                                $serviceRequestModel->setData("complete_date", $this->date->gmtDate())->save();
                                $statusDate[10]['firstTime'] = true;
                            } else {
                                $serviceRequestModel->setData($statusDate[$i]['date'], $this->date->gmtDate())->save();
                            }
                            $serviceRequestModel->setData('status', $i);
                        }
                        $serviceRequestModel->save();
                    }
                }
            }
            $data['status'] = $data['service_status'];


            $contactPerson = $this->customerFactory->create()->load($serviceRequestModel->getContactPersonId());
            $raId = $serviceRequestModel->getRaId();
            try {
                $serviceRequestModel->setData($data);
                $serviceRequestModel->save();
                $lastInsertId = $serviceRequestModel->getId();

                $contactPersonId = $serviceRequestModel->getContactPersonId();
                $primaryCustomerId = $serviceRequestModel->getData('customer_id');
                if ($contactPersonId) {
                    // load primary customer
                    $b2bCustomer = $this->customerFactory->create()->load($primaryCustomerId);
                    $b2bCustomerName = $b2bCustomer->getFirstname() . ' ' . $b2bCustomer->getLastname();
                    $b2bCustomerEmail = $b2bCustomer->getEmail();

                    $contactPerson = $this->customerFactory->create()->load($contactPersonId);
                    $emailTempVariables = [
                        'customer_name' => $b2bCustomerName,
                        'service_number' => $serviceRequestModel->getRaId()
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
                    if (in_array($serviceRequestModel->getStatus(), [9, 10]) && ($statusDate[9]['firstTime'] == true || $statusDate[10]['firstTime'] == true)) { // complete or close without repair
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

                if(isset($data['fpr_price'])) {
                    $this->messageManager->addSuccess(__('The Service Request ['.$raId.'] has been saved, with new service cost $' . $data['fpr_price']));
                }else{
                    $this->messageManager->addSuccess(__('The Service Request ['.$raId.'] has been saved.'));
                }
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [
                        'id' => $serviceRequestModel->getId(),
                        '_current' => true
                    ]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the Service Request.')
                );
            }
            return $resultRedirect->setPath('*/*/edit', [
                'id' => $this->getRequest()
                    ->getParam('id')
            ]);
        }

        $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
        if ($returnToEdit) {
            return $resultRedirect->setPath('*/*/edit', [
                'id' => $this->getRequest()
                    ->getParam('id')
            ]);
        } else {
            return $resultRedirect->setPath('*/*/');
        }
    }
}
