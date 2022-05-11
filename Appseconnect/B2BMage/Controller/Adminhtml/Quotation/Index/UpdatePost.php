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
namespace Appseconnect\B2BMage\Controller\Adminhtml\Quotation\Index;

/**
 * Class UpdatePost
 *
 * @category Controller
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class UpdatePost extends \Appseconnect\B2BMage\Controller\Adminhtml\Quotation\Quote
{

    /**
     * Empty customer's quote
     *
     * @return void
     */
    private function _emptyShoppingCart()
    {
        try {
            $this->customCart->truncate()->save();
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addError($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addException($exception, __('We can\'t clear the quote.'));
        }
    }

    /**
     * Update customer's quote cart
     *
     * @return void
     */
    private function _updateQuoteCart()
    {
        try {
            $quoteData = $this->getRequest()->getParam('quote');
            $quote = $this->initQuote();
            
            if (is_array($quoteData)) {
                $filter = $this->filterFactory->create(
                    [
                    'locale' => $this->resolver->getLocale()
                    ]
                );
                foreach ($quoteData as $index => $data) {
                    if (isset($data['qty'])) {
                        $quoteData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                
                $quoteModel = $this->helperQuotation->prepareItems($quote, $quoteData);
                $this->quotationRepository->save($quoteModel);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the quote items.'));
            $this->logger->critical($e);
        }
    }

    /**
     * Update quotation cart data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (! $this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        
        $updateAction = (string) $this->getRequest()->getParam('update_cart_action');
        
        switch ($updateAction) {
        case 'empty_quote':
            $this->_emptyShoppingCart();
            break;
        case 'update_qty':
            $this->_updateQuoteCart();
            break;
        default:
            $this->_updateQuoteCart();
        }
        
        $this->messageManager->addSuccess(__('Quote has been successfully updated.'));
        return $this->goBack();
    }
}
