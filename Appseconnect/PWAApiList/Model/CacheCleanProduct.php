<?php
namespace Appseconnect\PWAApiList\Model;

class CacheCleanProduct extends \Magento\CatalogGraphQl\Model\ProductInterfaceTypeResolverComposite
{
    /**
     * TypeResolverInterface[]
     */
    private $productTypeNameResolvers = [];

    /**
     * @param TypeResolverInterface[] $productTypeNameResolvers
     */
    public function __construct(\Magento\PageCache\Model\Cache\Type $fullPageCache, array $productTypeNameResolvers = [])
    {
        $this->fullPageCache = $fullPageCache;
        parent::__construct($productTypeNameResolvers);
    }

    public function resolveType(array $data) : string
    {
        $tags = [\Magento\Catalog\Model\Product::CACHE_TAG.'_'.$data['entity_id']];
        $this->fullPageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
        return parent::resolveType($data);
    }
}
