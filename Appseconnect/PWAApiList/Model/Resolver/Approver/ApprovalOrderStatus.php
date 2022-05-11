<?php

declare(strict_types=1);

namespace Appseconnect\PWAApiList\Model\Resolver\Approver;

use Appseconnect\B2BMage\Model\ResourceModel\OrderApprover\Collection;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Appseconnect\B2BMage\Model\ResourceModel\OrderApproverFactory;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Orders data reslover
 */
class ApprovalOrderStatus implements ResolverInterface
{
    /**
     * Order approver
     *
     * @var \Appseconnect\B2BMage\Model\OrderApproverFactory
     */
    public $orderApproverFactory;
    /**
     * Order
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    public $orderFactory;
    /**
     * Contact person helper
     *
     * @var \Appseconnect\B2BMage\Helper\ContactPerson\Data
     */
    public $helperContactPerson;

    /**
     * Credit limit helper
     *
     * @var \Appseconnect\B2BMage\Helper\CreditLimit\Data
     */
    public $helperCreditLimit;
    /**
     * Customer
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     * @param \Appseconnect\B2BMage\Model\OrderApproverFactory $orderApproverFactory order approver
     * @param \Magento\Sales\Model\OrderFactory $orderFactory order
     * @param \Appseconnect\B2BMage\Helper\ContactPerson\Data $helperContactPerson contact person helper
     * @param \Appseconnect\B2BMage\Helper\CreditLimit\Data $helperCreditLimit credit limit helper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory customer
     */
    public function __construct(

        \Appseconnect\B2BMage\Model\OrderApproverFactory $orderApproverFactory,
        \Magento\Sales\Model\OrderFactory                $orderFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data  $helperContactPerson,
        \Appseconnect\B2BMage\Helper\CreditLimit\Data    $helperCreditLimit,
        \Appseconnect\B2BMage\Helper\Sales\Data          $approverHelper,
        \Magento\Customer\Model\CustomerFactory          $customerFactory,
        \Appseconnect\B2BMage\Helper\ContactPerson\Data  $contactHelper


    )
    {

        $this->orderApproverFactory = $orderApproverFactory;
        $this->orderFactory = $orderFactory;
        $this->helperContactPerson = $helperContactPerson;
        $this->helperCreditLimit = $helperCreditLimit;
        $this->approverHelper = $approverHelper;
        $this->customerFactory = $customerFactory;
        $this->contactHelper = $contactHelper;
    }

    /**
     * @inheritdoc
     */
    public
    function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    )
    {

        $customerId = $context->getUserId();
        $customer = $this->customerFactory->create()->load( $customerId );

        if ($this->contactHelper->isContactPerson( $customer )) {
            $b2bCustomerId = $this->contactHelper->getCustomerId( $customerId )['customer_id'];
            $b2bCustomerStatus = $this->customerFactory->create()
                ->load( $b2bCustomerId )
                ->getCustomerStatus();
            $approver = $this->approverHelper->isApprover( $customerId );
            $approverStatus = $customer["is_active"];

            if ($approver && $approverStatus == 1 && $b2bCustomerStatus == 1) {

                $Order_Id = $args['input']['orderId'];
                $status = $args['input']['status'];
                if (isset( $status )) {

                    $orderApproverModel = $this->orderApproverFactory->create()->getCollection()
                        ->addFieldToFilter( 'increment_id', $Order_Id )->getFirstItem();
                    $order = $this->orderFactory->create()->loadByIncrementId( $Order_Id );
                    if (!empty( $order->getData() )) {
                        if ($order["status"] == "holded") {
                            if ($status == 'approve') {
                                $orderApproverModel->setStatus( 'Approved' );
                                $order->unhold();
                                $order->save();
                                $orderApproverModel->save();
                                return [
                                    "orderId" => $Order_Id,
                                    "status" => $status
                                ];
                            } elseif ($status == 'cancel') {
                                $orderApproverModel->setStatus( 'Canceled' );
                                $order->setStatus( 'canceled' );
                                $order->setState( 'canceled' );
                                $userId = $order->getData( 'contact_person_id' );

                                $paymentMethod = $order->getPayment()
                                    ->getMethodInstance()
                                    ->getCode();

                                $contactPersonData = $this->helperContactPerson->getCustomerId( $userId );

                                $check = $this->helperCreditLimit->isValidPayment( $paymentMethod );

                                if (!empty( $contactPersonData ) && $check) {
                                    $customerId = $contactPersonData['customer_id'];
                                    $customerCreditDetail = $this->helperCreditLimit->creditLimitUpdate(
                                        $customerId,
                                        $order,
                                        $order->getData( 'grand_total' )
                                    );
                                }
                                $order->save();
                                $orderApproverModel->save();
                                return [
                                    "orderId" => $Order_Id,
                                    "status" => $status
                                ];
                            }
                        } else {
                            throw new GraphQlInputException( __( "Order is Already: " . $order['status'] ) );
                        }
                    } else {
                        throw new GraphQlInputException( __( "OrderID Doesn't Exist" ) );
                    }
                }
            }
        }
    }
}
