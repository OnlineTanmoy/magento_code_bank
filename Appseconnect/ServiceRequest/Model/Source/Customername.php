<?php

namespace Appseconnect\ServiceRequest\Model\Source;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\AbstractComponent;

class Customername extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * Customername constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $components = [],
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerFactory = $customerFactory;
        $this->timezone = $timezone;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as $key => $item) {
                $customer = null;
                if (isset($item["contact_person_id"]) || isset($item["model_number"])) {
                    $customer = $this->customerFactory->create()->load($item["contact_person_id"]);
                    $dataSource["data"]["items"][$key]["contact_person_name"] = $customer->getFirstname() . ' ' . $customer->getLastname();
                    $dataSource["data"]["items"][$key]["contact_person_email"] = $customer->getEmail();

                    $customer = $this->customerFactory->create()->load($item["customer_id"]);
                } elseif (isset($item["assign_customer"])) {
                    $customer = $this->customerFactory->create()->load($item["assign_customer"]);
                    if ($customer->getId()) {
                        $dataSource["data"]["items"][$key]["assign_customer_name"] = $customer->getFirstname() . ' ' . $customer->getLastname();
                        $dataSource["data"]["items"][$key]["in_used"] = "Yes";
                    } else {
                        $dataSource["data"]["items"][$key]["assign_customer_name"] = '';
                        $dataSource["data"]["items"][$key]["in_used"] = "No";
                    }
                } elseif (isset($item["customer_id"]) || isset($item["sku"]) || !isset($item["customer_name"])) {
                    $customer = $this->customerFactory->create()->load($item["customer_id"]);
                    if ($customer->getId()) {
                        $dataSource["data"]["items"][$key]["customer_name"] = $customer->getFirstname() . ' ' . $customer->getLastname();
                    }
                }
                unset($customer);
                if (isset($item["post"])) {
                    $dataSource["data"]["items"][$key]["post_date"] =
                        $this->timezone->date($item["post"])
                        ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                }
            }
        }

        return $dataSource;
    }
}
