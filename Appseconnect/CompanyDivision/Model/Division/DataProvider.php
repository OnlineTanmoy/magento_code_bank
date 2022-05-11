<?php
/**
 * Namespace
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

namespace Appseconnect\CompanyDivision\Model\Division;

use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\EavValidationRules;

/**
 * Class DataProvider
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Collection
     *
     * @var Collection
     */
    public $collection;

    /**
     * Config
     *
     * @var Config
     */
    public $eavConfig;

    /**
     * FilterPool
     *
     * @var FilterPool
     */
    public $filterPool;

    /**
     * Array
     *
     * @var array
     */
    public $loadedData;

    /**
     * EAV attribute properties to fetch from meta storage
     *
     * @var array
     */
    public $metaProperties = [
        'dataType' => 'frontend_input',
        'visible' => 'is_visible',
        'required' => 'is_required',
        'label' => 'frontend_label',
        'sortOrder' => 'sort_order',
        'notice' => 'note',
        'default' => 'default_value',
        'size' => 'multiline_count',
    ];

    /**
     * Form element mapping
     *
     * @var array
     */
    public $formElement = [
        'text' => 'input',
        'hidden' => 'input',
        'boolean' => 'checkbox',
    ];

    /**
     * EavValidationRules
     *
     * @var EavValidationRules
     */
    public $eavValidationRules;

    /**
     * SessionManagerInterface
     *
     * @var SessionManagerInterface
     */
    public $session;

    /**
     * Constructor
     *
     * @param string                    $name                      Name
     * @param string                    $primaryFieldName          PrimaryFieldName
     * @param string                    $requestFieldName          RequestFieldName
     * @param EavValidationRules        $eavValidationRules        EavValidationRules
     * @param CustomerCollectionFactory $customerCollectionFactory CustomerCollectionFactory
     * @param Config                    $eavConfig                 EavConfig
     * @param FilterPool                $filterPool                FilterPool
     * @param array                     $meta                      Meta
     * @param array                     $data                      Data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EavValidationRules $eavValidationRules,
        CustomerCollectionFactory $customerCollectionFactory,
        Config $eavConfig,
        FilterPool $filterPool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->eavValidationRules = $eavValidationRules;
        $this->collection = $customerCollectionFactory->create();
        $this->collection->addAttributeToSelect('*');
        $this->eavConfig = $eavConfig;
        $this->filterPool = $filterPool;
        $this->meta['customer']['children'] = $this->getAttributesMetaData(
            $this->eavConfig->getEntityType('customer')
        );
    }

    /**
     * Get session object
     *
     * @return SessionManagerInterface
     */
    public function getSession()
    {
        if ($this->session === null) {
            $this->session = ObjectManager::getInstance()->get('Magento\Framework\Session\SessionManagerInterface');
        }
        return $this->session;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        foreach ($items as $customer) {
            $result['customer'] = $customer->getData();
            unset($result['address']);

            foreach ($customer->getAddresses() as $address) {
                $addressId = $address->getId();
                $address = $this->loadByAddressId($address, $addressId);
                $result['address'][$addressId] = $address->getData();
                $this->prepareCustomerAddressData($addressId, $result['address'], $result['customer']);
            }
            $this->loadedData[$customer->getId()] = $result;
        }
        $data = $this->getSession()->getCustomerFormData();
        if (!empty($data)) {
            $customerId = isset($data['customer']['entity_id']) ? $data['customer']['entity_id'] : null;
            $this->loadedData[$customerId] = $data;
            $this->getSession()->unsCustomerFormData();
        }
        return $this->loadedData;
    }

    /**
     * Load By AddressId
     *
     * @param $address   Address
     * @param $addressId AddressId
     *
     * @return mixed
     */
    public function loadByAddressId($address, $addressId)
    {
        $addressModel = $address->load($addressId);
        return $addressModel;
    }

    /**
     * Get attributes meta
     *
     * @param Type $entityType EntityType
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributesMetaData(Type $entityType)
    {
        $meta = [];
        $attributes = $entityType->getAttributeCollection();
        foreach ($attributes as $attribute) {
            $this->processFrontendInput($attribute, $meta);
            $code = $attribute->getAttributeCode();
            // use getDataUsingMethod, since some getters are defined and apply additional processing of returning value
            foreach ($this->metaProperties as $metaName => $origName) {
                $value = $attribute->getDataUsingMethod($origName);
                $meta[$code]['arguments']['data']['config'][$metaName] = ($metaName === 'label') ? __($value) : $value;
                if ('frontend_input' === $origName) {
                    $meta[$code]['arguments']['data']['config']['formElement'] = isset($this->formElement[$value])
                        ? $this->formElement[$value]
                        : $value;
                }
            }
            if ($attribute->usesSource()) {
                $meta[$code]['arguments']['data']['config']['options'] = $attribute->getSource()->getAllOptions();
            }
            $rules = $this->eavValidationRules->build($attribute, $meta[$code]['arguments']['data']['config']);
            if (!empty($rules)) {
                $meta[$code]['arguments']['data']['config']['validation'] = $rules;
            }
            $meta[$code]['arguments']['data']['config']['componentType'] = Field::NAME;
        }
        return $meta;
    }

    /**
     * Process attributes by frontend input type
     *
     * @param AttributeInterface $attribute Attribute
     * @param array              $meta      Meta
     *
     * @return array
     */
    public function processFrontendInput(AttributeInterface $attribute, array &$meta)
    {
        $code = $attribute->getAttributeCode();
        if ($attribute->getFrontendInput() === 'boolean') {
            $meta[$code]['arguments']['data']['config']['prefer'] = 'toggle';
            $meta[$code]['arguments']['data']['config']['valueMap'] = [
                'true' => '1',
                'false' => '0',
            ];
        }
    }

    /**
     * Prepare address data
     *
     * @param int   $addressId AddressId
     * @param array $addresses Addresses
     * @param array $customer  Customer
     *
     * @return void
     */
    public function prepareCustomerAddressData($addressId, array &$addresses, array $customer)
    {
        if (isset($customer['default_billing'])
            && $addressId == $customer['default_billing']
        ) {
            $addresses[$addressId]['default_billing'] = $customer['default_billing'];
        }
        if (isset($customer['default_shipping'])
            && $addressId == $customer['default_shipping']
        ) {
            $addresses[$addressId]['default_shipping'] = $customer['default_shipping'];
        }
        if (isset($addresses[$addressId]['street']) && !is_array($addresses[$addressId]['street'])) {
            $addresses[$addressId]['street'] = explode("\n", $addresses[$addressId]['street']);
        }
    }
}
