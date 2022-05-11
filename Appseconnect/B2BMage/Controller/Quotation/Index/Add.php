<?php
/**
 * Namespace
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
namespace Appseconnect\B2BMage\Controller\Quotation\Index;

use Magento\Sales\Controller\OrderInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteInterface;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Appseconnect\B2BMage\Api\Quotation\Data\QuoteProductInterface;
use Appseconnect\B2BMage\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Add
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Add extends \Magento\Framework\App\Action\Action
{
    
    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;
    
    /**
     * Filter
     *
     * @var \Zend_Filter_LocalizedToNormalizedFactory
     */
    public $filterFactory;
    
    /**
     * Resolver
     *
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    public $resolver;
    
    /**
     * Custom cart
     *
     * @var \Appseconnect\B2BMage\Model\CustomCart
     */
    public $customCart;
    
    /**
     * Store Manager
     *
     * @var StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * Product Repository
     *
     * @var ProductRepositoryInterface
     */
    public $productRepository;
    
    /**
     * Add constructor
     *
     * @param Context                                     $context           context
     * @param \Psr\Log\LoggerInterface                    $logger            logger
     * @param \Zend_Filter_LocalizedToNormalizedFactory   $filterFactory     filter
     * @param \Magento\Framework\Locale\ResolverInterface $resolver          resolver
     * @param StoreManagerInterface                       $storeManager      store manager
     * @param \Appseconnect\B2BMage\Model\CustomCart      $customCart        custom cart
     * @param ProductRepositoryInterface                  $productRepository product repository
     */
    public function __construct(
        Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Zend_Filter_LocalizedToNormalizedFactory $filterFactory,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        StoreManagerInterface $storeManager,
        \Appseconnect\B2BMage\Model\CustomCart $customCart,
        ProductRepositoryInterface $productRepository
    ) {
        $this->logger = $logger;
        $this->filterFactory = $filterFactory;
        $this->resolver = $resolver;
        $this->customCart = $customCart;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Init product
     *
     * @return ProductRepositoryInterface|boolean
     */
    private function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add exicute
     *
     * @throws InputException
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        
        try {
            if (isset($params['super_attribute']) && $params['super_attribute']) {
                foreach ($params['super_attribute'] as $key => $value) {
                    if ($value == null) {
                        throw new InputException(__('Please select the attributes.'));
                    }
                }
            }
            
            if (isset($params['qty'])) {
                $filter = $this->filterFactory->create(
                    [
                    'locale' => $this->resolver->getLocale()
                    ]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
            
            $product = $this->_initProduct();
            
            $this->customCart->addQuoteProduct($product, $params);
            $this->customCart->save();
            $message = __('You added %1 to your quote.', $product->getName());
            $this->messageManager->addSuccessMessage($message);
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('We can\'t add this item to your quote right now.')
            );
            $this->logger->critical($e);
        }
    }
}
