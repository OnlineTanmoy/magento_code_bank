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

namespace Appseconnect\B2BMage\Block\Adminhtml\Salesrep\Renderer;

use Appseconnect\B2BMage\Model\ResourceModel\Salesrep\CollectionFactory;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Status
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Status extends AbstractRenderer
{

    /**
     * Data
     *
     * @var \Appseconnect\B2BMage\Helper\Salesrep\Data
     */
    public $salesrepHelper;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    public $salesrepCollectionFactory;

    /**
     * Status constructor.
     *
     * @param \Magento\Backend\Block\Context             $context                   Context
     * @param \Appseconnect\B2BMage\Helper\Salesrep\Data $salesrepHelper            SalesrepHelper
     * @param CollectionFactory                          $salesrepCollectionFactory SalesrepCollectionFactory
     * @param array                                      $data                      Data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Appseconnect\B2BMage\Helper\Salesrep\Data $salesrepHelper,
        CollectionFactory $salesrepCollectionFactory,
        array $data = []
    ) {
        $this->salesrepHelper = $salesrepHelper;
        $this->salesrepCollectionFactory = $salesrepCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Render
     *
     * @param DataObject $row Row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $selsrepData = $this->salesrepHelper->isSalesrep(
            $this->getRequest()->getParam('id'),
            true
        );
        $salesrepId = $selsrepData[0]['id'];
        $customer = $this->salesrepCollectionFactory->create();
        $customer->addFieldToFilter('customer_id', $row->getId());
        $customer->addFieldToFilter('salesrep_id', $salesrepId);
        $output = $customer->getData();
        $result = [];
        if (!empty($output)) {
            return 'Assigned';
        } else {
            return "Unassigned";
        }
    }
}
