<?php

namespace Appseconnect\ShippingMethod\Controller\Adminhtml\ShippingMethod;

use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Save extends Action
{
    /**
     * @var \Appseconnect\ShippingMethod\Model\ShippingMethodFactory
     */
    public $shippingMethodFactory;

    /**
     * @param Action\Context $context
     * @param \Appseconnect\ShippingMethod\Model\ShippingMethodFactory $shippingMethodFactory
     */
    public function __construct(
        Action\Context $context,
        \Appseconnect\ShippingMethod\Model\ShippingMethodFactory $shippingMethodFactory
    ) {
        $this->shippingMethodFactory = $shippingMethodFactory;
        parent::__construct($context);
    }

    /**
     * Execute function
     *
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $post = $this->getRequest()->getPost();

        $customerId = $post['customer_id'];
        $shippingType = $post['shipping_type'];
        $status = $post['status'];
        $minimumOrderValue = $post['minimum_order_value'];

        $shippingMethodData = [];
        $shippingMethodData['customer_id'] = $customerId;
        $shippingMethodData['shipping_type'] = $shippingType;
        $shippingMethodData['status'] = $status;
        $shippingMethodData['minimum_order_value'] = $minimumOrderValue;

        if ($minimumOrderValue < 0) {
            $this->messageManager->addError(__('Minimum order amount is invalid'));
        } else {
            $shippingMethodModel = $this->shippingMethodFactory->create();

            if ($post['table_id']) {
                $shippingMethodModel = $shippingMethodModel->load($post['table_id']);
                $shippingMethodModel->setStatus($shippingMethodData['status']);
                $shippingMethodModel->setMinimumOrderValue($shippingMethodData['minimum_order_value']);
            } else {
                $shippingMethodModel->setData($shippingMethodData);
            }

            $shippingMethodModel->save();
        }

        $resultRedirect->setRefererUrl();

        return $resultRedirect;
    }
}