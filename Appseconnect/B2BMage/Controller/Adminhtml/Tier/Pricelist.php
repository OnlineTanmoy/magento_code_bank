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
 * Class Pricelist
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Pricelist extends \Magento\Framework\App\Action\Action
{
    
    /**
     * Pricelist
     *
     * @var \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory
     */
    public $pricelistFactory;
    
    /**
     * Json helper
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;
    
    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * Pricelist constructor
     *
     * @param \Magento\Framework\App\Action\Context                             $context          context
     * @param \Magento\Framework\Json\Helper\Data                               $jsonHelper       jsonhelper
     * @param \Magento\Customer\Model\CustomerFactory                           $customerFactory  customer
     * @param \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistFactory pricelist
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory $pricelistFactory
    ) {
    
        $this->pricelistFactory = $pricelistFactory;
        $this->jsonHelper = $jsonHelper;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $priceListCollection = $this->pricelistFactory->create();
        $priceListData = $priceListCollection->getData();
        
        $customerId = $this->getRequest()->getParam('customer_id');
        
        $customerCollection = $this->customerFactory->create()->load($customerId);
        $pricelistCode = $customerCollection->getPricelistCode();
        
        $priceListOption = "<option value='0'>Base Price</option>";
        if (! empty($priceListData)) {
            foreach ($priceListData as $option) {
                if ($option['id']) {
                    $selected = ($option['id'] == $pricelistCode) ?
                                        'selected="selected"' : '';
                    $priceListOption .= "<option " . $selected .
                        " value='" . $option['id'] . "'>"
                        . $option['pricelist_name'] . "</option>";
                }
            }
        }
        $result['htmlconent'] = $priceListOption;
        $this->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
    }
}
