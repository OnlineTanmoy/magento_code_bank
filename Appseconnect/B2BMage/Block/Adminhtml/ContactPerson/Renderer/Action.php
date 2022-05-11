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
namespace Appseconnect\B2BMage\Block\Adminhtml\ContactPerson\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Appseconnect\B2BMage\Model\ResourceModel\ContactFactory;
use Magento\Framework\DataObject;

/**
 * Abstract Class Action
 *
 * @category Block
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Action extends AbstractRenderer
{
    
    /**
     * Contact resource
     *
     * @var ContactFactory
     */
    public $contactResourceFactory;

    /**
     * Action constructor.
     *
     * @param \Magento\Backend\Block\Context $context                context
     * @param ContactFactory                 $contactResourceFactory contact resource
     * @param array                          $data                   data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        ContactFactory $contactResourceFactory,
        array $data = []
    ) {
    
        $this->contactResourceFactory = $contactResourceFactory;
        parent::__construct($context, $data);
    }

    /**
     * Render
     *
     * @param DataObject $row row
     * 
     * @return string
     */
    public function render(DataObject $row)
    {
        $contactResourceModel = $this->contactResourceFactory->create();
        $result = $contactResourceModel->getRowData($row->getId());
        
        $url = $this->getUrl(
            'b2bmage/contact/edit', [
            'id' => $result['contactperson_id'],
            'contactperson_id' => $row->getId(),
            'customer_id' => $this->getRequest()
                ->getParam('id'),
            'is_contactperson' => 1,
            '_current' => false
            ]
        );
        return '<a href="' . $url . '">Edit</a>';
    }
}
