<?php

namespace Appseconnect\ServiceRequest\Model\Source;

use Magento\Framework\DB\Ddl\Table;
use Magento\Ui\Component\AbstractComponent;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class CloseWithoutStatus extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * Customername constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $components = [],
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerRepository = $customerRepository;
    }

    /**
     * Update datasource based on status
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as $key => $item) {
                if (isset($item["status"])) {
                    if($item["status"] == 10) {
                        $dataSource["data"]["items"][$key]["close_without_repair_status"] = "Yes";
                    } elseif ($item["status"] == 9) {
                        $dataSource["data"]["items"][$key]["close_without_repair_status"] = "No";
                    }
                }
            }
        }

        return $dataSource;
    }
}
