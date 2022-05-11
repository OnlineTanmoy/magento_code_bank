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
namespace Appseconnect\B2BMage\Model\Quote;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

/**
 * Class Relation
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Relation implements RelationInterface
{

    /**
     * Process object relations
     *
     * @param \Magento\Framework\Model\AbstractModel $object Object
     *
     * @return void
     */
    public function processRelation(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->itemsCollectionWasSet()) {
            $object->getItemsCollection()->save();
        }
    }
}
