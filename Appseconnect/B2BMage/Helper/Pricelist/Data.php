<?php
/**
 * Namespace
 *
 * @category Helper
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Helper\Pricelist;

use Appseconnect\B2BMage\Model\ResourceModel\PricelistProduct\CollectionFactory as PricelistProductCollectionFactory;
use Appseconnect\B2BMage\Model\ResourceModel\Price\CollectionFactory as PricelistPriceCollectionFactory;

/**
 * Class Data
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Price
     *
     * @var \Appseconnect\B2BMage\Model\Price
     */
    public $pricelistModel;

    /**
     * PricelistProductCollectionFactory
     *
     * @var PricelistProductCollectionFactory
     */
    public $pricelistProductCollectionFactory;

    /**
     * PricelistPriceCollectionFactory
     *
     * @var PricelistPriceCollectionFactory
     */
    public $pricelistPriceCollectionFactory;

    /**
     * Price
     *
     * @var \Appseconnect\B2BMage\Model\Price
     */
    public $pricelist;

    /**
     * Data constructor.
     *
     * @param PricelistProductCollectionFactory $pricelistProductCollectionFactory PricelistProductCollectionFactory
     * @param PricelistPriceCollectionFactory   $pricelistPriceCollectionFactory   PricelistPriceCollectionFactory
     * @param \Appseconnect\B2BMage\Model\Price $pricelistModel                    PricelistModel
     */
    public function __construct(
        PricelistProductCollectionFactory $pricelistProductCollectionFactory,
        PricelistPriceCollectionFactory $pricelistPriceCollectionFactory,
        \Appseconnect\B2BMage\Model\Price $pricelistModel
    ) {
        $this->pricelistProductCollectionFactory = $pricelistProductCollectionFactory;
        $this->pricelistModel = $pricelistModel;
        $this->pricelistPriceCollectionFactory = $pricelistPriceCollectionFactory;
    }

    /**
     * GetPricelistId
     *
     * @param int $priceListId PriceListId
     *
     * @return array
     */
    public function getPricelistId($priceListId)
    {
        $pricelistModel = $this->pricelistPriceCollectionFactory->create();
        $pricelistId = $priceListId;
        $resultData = [];
        $resultData[0] = "Base Price";
        if ($pricelistId) {
            $pricelistModel->addFieldToFilter(
                'id', [
                'nin' => $pricelistId
                ]
            );
        }
        foreach ($pricelistModel->getData() as $val) {
            $resultData[$val['id']] = $val['pricelist_name'];
        }
        
        return $resultData;
    }

    /**
     * GetAmount
     *
     * @param int     $productId             ProductId
     * @param float   $finalPrice            FinalPrice
     * @param string  $customerPricelistCode CustomerPricelistCode
     * @param boolean $priceRuleObserver     PriceRuleObserver
     *
     * @return float
     */
    public function getAmount(
        $productId = null,
        $finalPrice = null,
        $customerPricelistCode = null,
        $priceRuleObserver = false
    ) {
    
        $pricelistModel = $this->pricelistPriceCollectionFactory->create()
            ->getPricelistProduct($customerPricelistCode, $productId);
        $output = $pricelistModel->getData();
        
        if (is_array($output) && ! empty($output)) {
            foreach ($output as $data) {
                if ($data['is_active'] == '1') {
                    $price = $data['final_price'];
                } elseif ($priceRuleObserver) {
                    $price = '';
                } else {
                    $price = $finalPrice;
                }
            }
        } elseif ($priceRuleObserver) {
            $price = '';
        } else {
            $price = $finalPrice;
        }
        return $price;
    }

    /**
     * GetCalculatedPrice
     *
     * @param array $result     Result
     * @param float $finalPrice FinalPrice
     *
     * @return NULL|float
     */
    public function getCalculatedPrice($result, $finalPrice)
    {
        $price = null;
        if ($result) {
            foreach ($result as $value) {
                $price = $finalPrice * (1 - ($value['discount_factor'] / 100));
            }
            $price = $price * (1 - ($data['discount_factor'] / 100));
        } else {
            $price = $finalPrice * (1 - ($data['discount_factor'] / 100));
        }
        return $price;
    }

    /**
     * GetPricelistProducts
     *
     * @param int $id Id
     *
     * @return PricelistProductCollectionFactory
     */
    public function getPricelistProducts($id)
    {
        $productCollection = $this->pricelistProductCollectionFactory->create();
        $productCollection->addFieldToFilter('pricelist_id', $id);
        return $productCollection;
    }

    /**
     * GetPricelist
     *
     * @return array
     */
    public function getPricelist()
    {
        $priceList = $this->pricelistPriceCollectionFactory->create();
        $output = [];
        $outputResult = [];
        $output['label'] = 'Choose';
        $output['value'] = '';
        $outputResult[] = $output;
        foreach ($priceList->getData() as $val) {
            $output['label'] = $val['pricelist_name'];
            $output['value'] = $val['id'];
            $outputResult[] = $output;
        }
        return $outputResult;
    }
}
