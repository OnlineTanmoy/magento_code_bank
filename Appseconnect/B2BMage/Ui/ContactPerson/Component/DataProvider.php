<?php
/**
 * Namespace
 *
 * @category Ui
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Ui\ContactPerson\Component;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

/**
 * Class DataProvider
 *
 * @category Ui
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */

class DataProvider extends \Magento\Customer\Ui\Component\DataProvider
{
    
    /**
     * API filter
     *
     * @var Filter
     */
    public $filter;

    /**
     * Initialize class variable
     *
     * @param mixed                 $name                  Name
     * @param mixed                 $primaryFieldName      PrimaryFieldName
     * @param mixed                 $requestFieldName      RequestFieldName
     * @param Filter                $filter                Filter
     * @param Reporting             $reporting             Reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder SearchCriteriaBuilder
     * @param RequestInterface      $request               Request
     * @param FilterBuilder         $filterBuilder         FilterBuilder
     * @param AttributeRepository   $attributeRepository   AttributeRepository
     * @param array                 $meta                  Meta array
     * @param array                 $data                  Data array
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Filter $filter,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        AttributeRepository $attributeRepository,
        array $meta = [],
        array $data = []
    ) {
    
        $this->filter = $filter;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $attributeRepository,
            $meta,
            $data
        );
    }

    /**
     * Get customer detail in array
     *
     * @return mixed
     */
    public function getData()
    {
        $this->filter->setField('customer_type');
        $this->filter->setValue('1,4');
        $this->filter->setConditionType('in');
        parent::addFilter($this->filter);
        $data = parent::getData();
        return $data;
    }
}
