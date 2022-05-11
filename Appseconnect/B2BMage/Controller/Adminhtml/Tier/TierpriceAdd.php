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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Tier;

/**
 * Class TierpriceAdd
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class TierpriceAdd extends \Magento\Backend\App\Action
{

    /**
     * Tier price
     *
     * @var \Appseconnect\B2BMage\Model\TierpriceFactory
     */
    public $tierPriceFactory;
    
    /**
     * Tier price constructor
     *
     * @param \Magento\Backend\App\Action\Context          $context          context
     * @param \Appseconnect\B2BMage\Model\TierpriceFactory $tierPriceFactory tier price
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Appseconnect\B2BMage\Model\TierpriceFactory $tierPriceFactory
    ) {
    
        $this->tierPriceFactory = $tierPriceFactory;
        parent::__construct($context);
    }

    /**
     * Add tier price
     *
     * @return void
     */
    public function execute()
    {
        $data = [];
        $data = $this->_request->getParams();
        $variable = $data['action'];
        switch ($variable) {
        case 'delete':
            $modelData = $this->tierPriceFactory->create()->load($data['tierPriceId']);
            $modelData->delete();
            break;
            
        case 'process':
            if (isset($data['data']['update_data'])) {
                $this->_saveUpdateData($data);
            }
            if (isset($data['data']['insert_data'])) {
                $this->_savePostData($data);
            }
            break;
        }
    }

    /**
     * Save post data
     *
     * @param array $data data
     *
     * @return void
     */
    private function _savePostData($data)
    {
        $insertData = $data['data']['insert_data'];
        foreach ($insertData as $insertValues) {
            if ($insertValues['tier_price'] != '' 
                && $insertValues['quantity'] != '' 
                && $insertValues['quantity'] != 0 
                && $insertValues['quantity'] <= 100000000
            ) {
                $insertValues['parent_id'] = $data['tierPriceId'];
                $this->_linkProducts($insertValues);
            }
        }
    }
    
    /**
     * Save update data
     *
     * @param array $data data
     *
     * @return void
     */
    private function _saveUpdateData($data)
    {
        $updateData = $data['data']['update_data'];
        foreach ($updateData as $updateValues) {
            if ($updateValues['quantity'] != '' 
                && $updateValues['tier_price'] != '' 
                && $updateValues['quantity'] != 0 
                && $updateValues['quantity'] <= 100000000
            ) {
                $updateValues['parent_id'] = $data['tierPriceId'];
                if ($updateValues['id']) {
                    $this->_linkProducts($updateValues);
                }
            }
        }
    }
    
    /**
     * Link product
     *
     * @param array $data data
     *
     * @return void
     */
    private function _linkProducts($data)
    {
        $model = $this->tierPriceFactory->create();
        $model->setData($data)->save();
    }
}
