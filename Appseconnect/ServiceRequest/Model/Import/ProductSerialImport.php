<?php

namespace Appseconnect\ServiceRequest\Model\Import;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Appseconnect\ServiceRequest\Model\Import\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

/**
 * CSV import of the custom table from magento admin
 * Class CustomImport
 * @package Appseconnect\InternalPayment\Model\Import
 */
class ProductSerialImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const SKU = 'sku';
    const SERIAL_NO = 'serial_no';
    const WARRANTY_MONTHS = 'warranty_months';
    const IS_ACTIVE = 'is_active';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_ID_IS_EMPTY => 'Empty',
    ];

    protected $_permanentAttributes = [self::SKU];

    /**
     * If we should check column names
     * @var bool
     */
    protected $needColumnCheck = false;

    /**
     * Valid column names
     * @array
     */
    protected $validColumnNames = [
        self::SKU,
        self::SERIAL_NO,
        self::WARRANTY_MONTHS,
        self::IS_ACTIVE,
    ];

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * Need to log in import history
     * @var bool
     */
    protected $logInHistory = true;

    protected $_validators = [];

    protected $_connection;

    protected $_resource;

    protected $_tableEntity;

    private $serializer;

    private $_objectManager;

    protected $_serial = [];

    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Catalog\Model\Product $product,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Appseconnect\ServiceRequest\Model\SerialFactory $serialFactory,
        \Magento\Customer\Model\Customer $customerModel
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->product = $product;
        $this->_tableEntity = $this->_resource->getTableName('insync_product_serial');
        $this->customerModel = $customerModel;
        $this->serialFactory = $serialFactory;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'appseconnect_product_serial_import';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $title = false;
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        if (!isset($rowData[self::SKU]) || empty($rowData[self::SKU])) {
            $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
            return false;
        } else if (!is_numeric($rowData[self::WARRANTY_MONTHS])) {
            $this->addRowError('Invalid numeric field for Warranty months', $rowNum);
            return false;
        } else if (6 > intval($rowData[self::WARRANTY_MONTHS])) {
            $this->addRowError("Warranty months can't be less then 6 months", $rowNum);
            return false;
        } else if (isset($this->_serial[$rowData[self::SERIAL_NO]])) {
            $this->addRowError('Multiple entries for same SKU id : ' . $rowData[self::SKU] . ' in line ' . ($this->_serial[$rowData[self::SERIAL_NO]] + 1), $rowNum);
            return false;
        } else {
            // Serial data validation
            $serialCollection = $this->serialFactory->create()->getCollection();
            $serialCollection->addFieldToFilter('serial_no', $rowData[self::SERIAL_NO]);
            foreach ($serialCollection as $_eachSerialCollection) {
                $this->addRowError('Serial No. already exist : ' . $rowData[self::SERIAL_NO], $rowNum);
                return false;
            }
        }
        $this->_serial[$rowData[self::SERIAL_NO]] = $rowNum;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create advanced Records data from raw data.
     * @return bool Result of operation.
     * @throws \Exception
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }
        return true;
    }

    /**
     * Save Records
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    /**
     * Replace Records
     *
     * @return $this
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    /**
     * Deletes Records data from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
        return $this;
    }

    /**
     * Save and replace Records
     *
     * @return $this
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $ids = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
//            $saveCustomerList = [];
            $this->_serial = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_MESSAGE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $rowId = $rowNum;
                $ids[] = $rowId;

                $entityList[$rowId][] = [
                    self::SKU => $rowData[self::SKU],
                    self::SERIAL_NO => $rowData[self::SERIAL_NO],
                    self::WARRANTY_MONTHS => $rowData[self::WARRANTY_MONTHS],
                    self::IS_ACTIVE => $rowData[self::IS_ACTIVE],
                ];
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($ids) {
                    if ($this->deleteEntityFinish(array_unique($ids), $this->_tableEntity)) {
                        $this->saveEntityFinish($entityList, $this->_tableEntity);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, $this->_tableEntity);
            }
        }
        return $this;
    }

    /**
     * Save Records
     * @param array $priceData
     * @param string $table
     * @return $this
     */
    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    $entityIn[] = $row;
                }
            }
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($table, $entityIn, [
                    'serial_no',
                    self::SERIAL_NO,
                    self::WARRANTY_MONTHS,
                ]);
            }
        }
        return $this;
    }

    /**
     * Delete record for existing SKU in custom table
     * @param array $skus
     * @param $table
     * @return bool
     */
    protected function deleteEntityFinish(array $skus, $table)
    {
        return false;
    }

    public function getCustomerCollection()
    {
        return $this->_customerFactory->create();
    }

    /**
     * Brought from parent class to skip the SKU validation for custom table
     * @return $this
     */
    protected function _saveValidatedBunches()
    {
        $source = $this->_getSource();
        $currentDataSize = 0;
        $bunchRows = [];
        $startNewBunch = false;
        $nextRowBackup = [];
        $maxDataSize = $this->_resourceHelper->getMaxDataSize();
        $bunchSize = $this->_importExportData->getBunchSize();
        $skuSet = [];

        $source->rewind();
        $this->_dataSourceModel->cleanBunches();

        while ($source->valid() || $bunchRows) {
            if ($startNewBunch || !$source->valid()) {
                $this->_dataSourceModel->saveBunch($this->getEntityTypeCode(), $this->getBehavior(), $bunchRows);

                $bunchRows = $nextRowBackup;
                $currentDataSize = strlen($this->getSerializer()->serialize($bunchRows));
                $startNewBunch = false;
                $nextRowBackup = [];
            }
            if ($source->valid()) {
                try {
                    $rowData = $source->current();
                } catch (\InvalidArgumentException $e) {
                    $this->addRowError($e->getMessage(), $this->_processedRowsCount);
                    $this->_processedRowsCount++;
                    $source->next();
                    continue;
                }

                $this->_processedRowsCount++;

                if ($this->validateRow($rowData, $source->key())) {
                    // add row to bunch for save
                    $rowData = $this->_prepareRowForDb($rowData);
                    $rowSize = strlen($this->jsonHelper->jsonEncode($rowData));

                    $isBunchSizeExceeded = $bunchSize > 0 && count($bunchRows) >= $bunchSize;

                    if ($currentDataSize + $rowSize >= $maxDataSize || $isBunchSizeExceeded) {
                        $startNewBunch = true;
                        $nextRowBackup = [$source->key() => $rowData];
                    } else {
                        $bunchRows[$source->key()] = $rowData;
                        $currentDataSize += $rowSize;
                    }
                }
                $source->next();
            }
        }
        $this->_processedEntitiesCount = count($skuSet);

        return $this;
    }

    /**
     * Brought from parent class to skip the SKU validation for custom table
     * @return $this
     */
    private function getSerializer()
    {
        if (null === $this->serializer) {
            $this->serializer = ObjectManager::getInstance()->get(Json::class);
        }
        return $this->serializer;
    }
}
