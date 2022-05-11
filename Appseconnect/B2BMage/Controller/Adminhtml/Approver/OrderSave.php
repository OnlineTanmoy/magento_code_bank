<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\Adminhtml\Approver;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class OrderSave
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class OrderSave extends Action
{
    
    /**
     * Approver factory variable
     *
     * @var \Appseconnect\B2BMage\Model\ApproverFactory
     */
    public $approverFactory;
    
    /**
     * Contractor
     *
     * @param Action\Context                              $context         context
     * @param \Appseconnect\B2BMage\Model\ApproverFactory $approverFactory approver factory
     */
    public function __construct(
        Action\Context $context,
        \Appseconnect\B2BMage\Model\ApproverFactory $approverFactory
    ) {
        $this->approverFactory = $approverFactory;
        parent::__construct($context);
    }
    
    /**
     * Exiqute function
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = [];
        $data = $this->_request->getParams();
        
        $variable = $data['data']['action'];
        
        switch ($variable) {
        case 'delete':
            $output = [];
            $approverModel = $this->approverFactory->create();
            $approverModel->load($data['data']['id']);
            $approverModel->delete();
            break;
            
        case 'process':
            $output = [];
            $newData = $data['data']['new'];
            if ($newData) {
                foreach ($newData as $val) {
                    $this->_saveTransactionalData($val);
                }
            }
            break;
        }
    }

    /**
     * Save transaction data
     *
     * @param array $data ordar data
     *
     * @return void
     */
    private function _saveTransactionalData($data)
    {
        $approverModel = $this->approverFactory->create();
        if (isset($data['insync_approver_id'])) {
            $approverModel->addData($data);
        } else {
            $approverModel->setData($data);
        }
        $approverModel->save();
    }
}
