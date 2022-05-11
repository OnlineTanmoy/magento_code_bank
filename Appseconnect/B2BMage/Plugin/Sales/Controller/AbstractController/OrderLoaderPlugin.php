<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Plugin\Sales\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
/**
 * Class OrderLoaderPlugin
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderLoaderPlugin
{
    
    /**
     * OrderFactory
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    public $orderFactory;
    
    /**
     * OrderViewAuthorizationInterface
     *
     * @var OrderViewAuthorizationInterface
     */
    public $orderAuthorization;
    
    /**
     * Registry
     *
     * @var Registry
     */
    public $registry;
    
    /**
     * UrlInterface
     *
     * @var \Magento\Framework\UrlInterface
     */
    public $url;
    
    /**
     * ForwardFactory
     *
     * @var ForwardFactory
     */
    public $resultForwardFactory;
    
    /**
     * RedirectFactory
     *
     * @var RedirectFactory
     */
    public $redirectFactory;
    
    /**
     * Session
     *
     * @var Session
     */
    public $customerSession;
    
    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Sales\Data
     */
    public $helperSales;

    /**
     * Class Variable initialize
     *
     * @param \Magento\Sales\Model\OrderFactory       $orderFactory         OrderFactory
     * @param OrderViewAuthorizationInterface         $orderAuthorization   OrderAuthorization
     * @param Registry                                $registry             Registry
     * @param \Magento\Framework\UrlInterface         $url                  Url
     * @param ForwardFactory                          $resultForwardFactory ResultForwardFactory
     * @param RedirectFactory                         $redirectFactory      RedirectFactory
     * @param Session                                 $customerSession      CustomerSession
     * @param \Appseconnect\B2BMage\Helper\Sales\Data $helperSales          HelperSales
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        OrderViewAuthorizationInterface $orderAuthorization,
        Registry $registry,
        \Magento\Framework\UrlInterface $url,
        ForwardFactory $resultForwardFactory,
        RedirectFactory $redirectFactory,
        Session $customerSession,
        \Appseconnect\B2BMage\Helper\Sales\Data $helperSales
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderAuthorization = $orderAuthorization;
        $this->registry = $registry;
        $this->url = $url;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->redirectFactory = $redirectFactory;
        $this->customerSession = $customerSession;
        $this->helperSales = $helperSales;
    }

    /**
     * AroundLoad
     *
     * @param \Magento\Sales\Controller\AbstractController\OrderLoader $subject Subject
     * @param \Closure                                                 $proceed Proceed
     * @param RequestInterface                                         $request Request
     *
     * @return mixed|boolean
     */
    public function aroundLoad(
        \Magento\Sales\Controller\AbstractController\OrderLoader $subject,
        \Closure $proceed,
        RequestInterface $request
    ) {
    
        $flag = false;
        $orderId = (int) $request->getParam('order_id');
        $parentObject = $subject;
        
        if (! $orderId) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        
        $order = $this->orderFactory->create()->load($orderId);
        $customerId = $this->customerSession->getCustomer()->getId();
        $isApprove = false;
        if ($order->getStatus() == 'holded' && $order->getCustomerId() != $customerId) {
            $flag = true;
            $isApprove = $this->helperSales->isOrderApprover($order->getIncrementId(), $customerId);
        }
        
        if (!$flag) {
            $result = $proceed($request);
            return $result;
        }
        
        if ($this->orderAuthorization->canView($order) || $isApprove) {
            $this->registry->register('current_order', $order);
            return true;
        }

        $resultRedirect = $this->redirectFactory->create();
        return $resultRedirect->setUrl($this->url->getUrl('*/*/history'));
    }
}
