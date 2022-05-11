<?php
/**
 * Namespace
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Block\Adminhtml\Salesrep\Edit;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * AccountManagementInterface
     *
     * @var AccountManagementInterface
     */
    public $customerAccountManagement;

    /**
     * Http
     *
     * @var Http
     */
    public $httpRequest;

    /**
     * DeleteButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context                   Context
     * @param Http                                  $httpRequest               HttpRequest
     * @param \Magento\Framework\Registry           $registry                  Registry
     * @param AccountManagementInterface            $customerAccountManagement CustomerAccountManagement
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        Http $httpRequest,
        \Magento\Framework\Registry $registry,
        AccountManagementInterface $customerAccountManagement
    ) {
        parent::__construct($context, $registry);
        $this->httpRequest = $httpRequest;
        $this->customerAccountManagement = $customerAccountManagement;
    }

    /**
     * GetButtonData
     *
     * @return array
     */
    public function getButtonData()
    {
        $salesrepId = $this->httpRequest->getParam('salesrep_id');
        $data = [];
        if ($salesrepId) {
            $data = [
                'label' => __('Delete Sales Representative'),
                'class' => '',
                'on_click' => 'setLocation("' . $this->getDeleteUrl() . '")'
            ];
        }
        return $data;
    }

    /**
     * GetDeleteUrl
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        $salesrepId = $this->httpRequest->getParam('salesrep_id');
        return $this->getUrl(
            'b2bmage/salesrep/delete',
            [
                'id' => $salesrepId
            ]
        );
    }
}
