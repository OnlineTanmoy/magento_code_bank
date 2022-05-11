<?php
/**
 * Namespace
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\B2BMage\Observer\CategoryVisibility;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CategorySaveObserver
 *
 * @category B2BMage\Observer
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CategorySaveObserver implements ObserverInterface
{

    /**
     *  Data
     *
     * @var \Appseconnect\B2BMage\Helper\CategoryVisibility\Data
     */
    public $categoryVisibilityHelper;

    /**
     * CategorySaveObserver constructor.
     *
     * @param \Appseconnect\B2BMage\Helper\CategoryVisibility\Data $categoryVisibilityHelper CategoryVisibilityHelper
     */
    public function __construct(
        \Appseconnect\B2BMage\Helper\CategoryVisibility\Data $categoryVisibilityHelper
    ) {
        $this->categoryVisibilityHelper = $categoryVisibilityHelper;
    }

    /**
     * Execute
     *
     * @param Observer $observer Observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category = $observer->getEvent()->getData('category');
        $postData = $observer->getEvent()->getData('request')->getPostValue();
        $useConfig = $postData['use_config'];
        if (isset($useConfig['customer_group']) && !empty($useConfig['customer_group'])) {
            if ($useConfig['customer_group'] == true && $postData['customer_group'] == '') {
                $customerGroups = $this->categoryVisibilityHelper->getCustomerGroups();
                if (!empty($customerGroups)) {
                    $options = [];
                    foreach ($customerGroups as $group) {
                        $options[] = $group['value'];
                    }
                    $data = implode(',', $options);
                    $category->setData('customer_group', $data);
                }
            } else {
                $data = implode(',', $postData['customer_group']);
                if (count($postData['customer_group']) == 1) {
                    $groupOption = reset($postData['customer_group']);
                    if ($groupOption == '0') {
                        $data = 'x,0';
                        $category->setData('customer_group', $data);
                    }
                }
            }
        }
        return $this;
    }
}
