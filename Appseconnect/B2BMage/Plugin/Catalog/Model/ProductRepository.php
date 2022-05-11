<?php
/**
 * Namespace
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Plugin\Catalog\Model;

/**
 * Class ProductRepository
 *
 * @category B2BMage\Plugin
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class ProductRepository
{
    /**
     * AfterGet
     *
     * @param \Magento\Catalog\Model\ProductRepository $subject Subject
     * @param $product Product
     *
     * @return mixed
     */
    public function afterGet(\Magento\Catalog\Model\ProductRepository $subject, $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        $extensionAttributes->setCustomPrice(48);
        $extensionAttributes->setTirePrice(array(array('type'=>'1 to 2', 'value' => '52')));
        $product->setExtensionAttributes($extensionAttributes);

        return $product;
    }
}
