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

/**
 * Class Total
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Total extends \Magento\Framework\DataObject
{

    /**
     * Array
     *
     * @var array
     */
    public $totalAmounts;

    /**
     * Array
     *
     * @var array
     */
    public $baseTotalAmounts;

    /**
     * Total constructor.
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer JsonSerializer
     * @param array                                        $data           Data
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        array $data = []
    ) {
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($data);
    }

    /**
     * Set total amount value
     *
     * @param string $quoteCode   QuoteCode
     * @param float  $quoteAmount QuoteAmount
     *
     * @return $this
     */
    public function setTotalAmount($quoteCode, $quoteAmount)
    {
        $this->totalAmounts[$quoteCode] = $quoteAmount;
        if ($quoteCode != 'subtotal') {
            $quoteCode = $quoteCode . '_amount';
        }
        $this->setData($quoteCode, $quoteAmount);
        
        return $this;
    }

    /**
     * Set total amount value in base store currency
     *
     * @param string $quoteCode   QuoteCode
     * @param float  $quoteAmount QuoteAmount
     *
     * @return $this
     */
    public function setBaseTotalAmount($quoteCode, $quoteAmount)
    {
        $this->baseTotalAmounts[$quoteCode] = $quoteAmount;
        if ($quoteCode != 'subtotal') {
            $quoteCode = $quoteCode . '_amount';
        }
        $this->setData('base_' . $quoteCode, $quoteAmount);
        
        return $this;
    }

    /**
     * Add amount total amount value
     *
     * @param string $quoteCode   QuoteCode
     * @param float  $quoteAmount QuoteAmount
     *
     * @return $this
     */
    public function addTotalAmount($quoteCode, $quoteAmount)
    {
        $quoteAmount = $this->getTotalAmount($quoteCode) + $quoteAmount;
        $this->setTotalAmount($quoteCode, $quoteAmount);
        
        return $this;
    }

    /**
     * Add amount total amount value in base store currency
     *
     * @param string $quoteCode   QuoteCode
     * @param float  $quoteAmount QuoteAmount
     *
     * @return $this
     */
    public function addBaseTotalAmount($quoteCode, $quoteAmount)
    {
        $quoteAmount = $this->getBaseTotalAmount($quoteCode) + $quoteAmount;
        $this->setBaseTotalAmount($quoteCode, $quoteAmount);
        
        return $this;
    }

    /**
     * Get total amount value by code
     *
     * @param string $quoteCode QuoteCode
     *
     * @return float|int
     */
    public function getTotalAmount($quoteCode)
    {
        if (isset($this->totalAmounts[$quoteCode])) {
            return $this->totalAmounts[$quoteCode];
        }
        return 0;
    }

    /**
     * Get total amount value by code in base store currency
     *
     * @param string $quoteCode QuoteCode
     *
     * @return float|int
     */
    public function getBaseTotalAmount($quoteCode)
    {
        if (isset($this->baseTotalAmounts[$quoteCode])) {
            return $this->baseTotalAmounts[$quoteCode];
        }
        
        return 0;
    }

    // @codeCoverageIgnoreStart
    
    /**
     * Get all total amount values
     *
     * @return array
     */
    public function getAllTotalAmounts()
    {
        $totalAmounts = $this->totalAmounts;
        return $totalAmounts;
    }

    /**
     * Get all total amount values in base currency
     *
     * @return array
     */
    public function getAllBaseTotalAmounts()
    {
        $baseTotalAmounts = $this->baseTotalAmounts;
        return $baseTotalAmounts;
    }

    /**
     * Set the full info, which is used to capture tax related information.
     * If a string is used, it is assumed to be serialized.
     *
     * @param array|string $fullInfo FullInfo
     *
     * @return $this
     */
    public function setFullInfo($fullInfo)
    {
        $this->setData('full_info', $fullInfo);
        return $this;
    }

    /**
     * Returns the full info, which is used to capture tax related information.
     *
     * @return array
     */
    public function getFullInfo()
    {
        $fullInfoFoTax = $this->getData('full_info');
        if (is_string($fullInfoFoTax)) {
            $fullInfoFoTax = $this->jsonSerializer->unserialize($fullInfoFoTax);
        }
        return $fullInfoFoTax;
    }
}
