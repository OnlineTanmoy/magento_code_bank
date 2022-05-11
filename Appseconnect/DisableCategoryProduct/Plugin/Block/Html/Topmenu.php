<?php
namespace Appseconnect\DisableCategoryProduct\Plugin\Block\Html;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Customer\Model\Session;

/**
 * Class Topmenu
 *
 */
class Topmenu
{
    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var Session
     */
    public $customerSession;

    protected $httpContext;

    /**
     * Topmenu constructor.
     * @param Resolver $layerResolver
     * @param Session $session
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Resolver $layerResolver,
        Session $session,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->layerResolver = $layerResolver;
        $this->customerSession = $session;
        $this->httpContext = $httpContext;
    }

    /**
     * Get current Category from catalog layer
     *
     * @return \Magento\Catalog\Model\Category
     */
    private function getCurrentCategory()
    {
        $catalogLayer = $this->layerResolver->get();

        if (!$catalogLayer) {
            return null;
        }

        return $catalogLayer->getCurrentCategory();
    }

    /**
     * Add category id to cache tag
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param array $result
     * @return array
     */
    public function afterGetCacheKeyInfo(\Magento\Theme\Block\Html\Topmenu $subject, $result)
    {
        $category = $this->getCurrentCategory();
        $customerId = $this->httpContext->getValue('customer_id');

        if (!$customerId) {
            return $result;
        }

        $result[] = Category::CACHE_TAG . '_' . $category->getId();

        return $result;
    }
}