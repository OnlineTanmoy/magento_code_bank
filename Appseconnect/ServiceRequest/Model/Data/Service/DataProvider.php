<?php
namespace Appseconnect\ServiceRequest\Model\Data\Service;
use Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory;
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $contactCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $contactCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $contactCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = array();
        foreach ($items as $service) {
            $this->loadedData[$service->getEntityId()]['service'] = $service->getData();
           // $this->loadeddata[$service->getEntityId()]['fault_description'] = $service->getData();
        }


        return $this->loadedData;

    }
}
