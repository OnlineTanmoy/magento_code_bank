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

namespace Appseconnect\B2BMage\Model\Entity\Attribute\Backend;

/**
 * Class CustomerGroupBackend
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class CustomerGroupBackend extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * Before save method
     *
     * @param \Magento\Framework\DataObject $object Object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        $data = [];
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);
        if (empty($data)) {
            $data = [];
        }

        if (is_array($data)) {
            $object->setData($attributeCode, implode(',', $data));
        } else {
            $object->setData($attributeCode, $data);
        }
        return parent::beforeSave($object);
    }

    /**
     * After save method
     *
     * @param \Magento\Framework\DataObject $object Object
     *
     * @return mixed
     */
    public function afterSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);
        if (is_string($data)) {
            $object->setData($attributeCode, explode(',', $data));
        }
        return parent::afterSave($object);
    }

    /**
     * After load method
     *
     * @param \Magento\Framework\DataObject $object Object
     *
     * @return                                        $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codeCoverageIgnore
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);

        // only explode and set the value if the attribute is set on the model
        if (null !== $data && is_string($data)) {
            $data = explode(',', $data);
            $object->setData($attributeCode, $data);
        }
        return parent::afterLoad($object);
    }
}
