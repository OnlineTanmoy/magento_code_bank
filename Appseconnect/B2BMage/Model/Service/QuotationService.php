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
namespace Appseconnect\B2BMage\Model\Service;

/**
 * Class QuotationService
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class QuotationService implements \Appseconnect\B2BMage\Api\Quotation\QuotationServiceInterface
{
    
    /**
     * Eventmanager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    public $eventManager;
    
    /**
     * Quotation repository
     *
     * @var \Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface
     */
    public $quotationRepository;

    /**
     * QuotationService constructor.
     *
     * @param \Magento\Framework\Event\ManagerInterface                $eventManager        eventmanager
     * @param \Appseconnect\B2BMage\Model\QuotationRepositoryInterface $quotationRepository quotation repository
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Appseconnect\B2BMage\Api\Quotation\QuotationRepositoryInterface $quotationRepository
    ) {
    
        $this->eventManager = $eventManager;
        $this->quotationRepository = $quotationRepository;
    }

    /**
     * Submit quote by id
     *
     * @param int $id id
     *
     * @return bool
     */
    public function submitQuoteById($id)
    {
        $quote = $this->quotationRepository->get($id);
        if ($quote->submit()) {
            $quote->setUpdatedAt(date('Y-m-d h:i:s'));
            $this->quotationRepository->save($quote);
            
            $this->eventManager->dispatch(
                'sales_quotation_process_after', [
                'quote' => $quote,
                'action' => 'submit'
                ]
            );
            
            return true;
        }
        return false;
    }

    /**
     * Approved quote by id
     *
     * @param int $id id
     *
     * @return bool
     */
    public function approveQuoteById($id)
    {
        $quote = $this->quotationRepository->get($id);
        if ($quote->approve()) {
            $quote->setUpdatedAt(date('Y-m-d h:i:s'));
            $this->quotationRepository->save($quote);
            
            $this->eventManager->dispatch(
                'sales_quotation_process_after', [
                'quote' => $quote,
                'action' => 'approve'
                ]
            );
            
            return true;
        }
        return false;
    }

    /**
     * Hold quote by id
     *
     * @param int $id id
     *
     * @return bool
     */
    public function holdQuoteById($id)
    {
        $quote = $this->quotationRepository->get($id);
        if ($quote->hold()) {
            $quote->setUpdatedAt(date('Y-m-d h:i:s'));
            $this->quotationRepository->save($quote);
            
            $this->eventManager->dispatch(
                'sales_quotation_process_after', [
                'quote' => $quote,
                'action' => 'hold'
                ]
            );
            
            return true;
        }
        return false;
    }

    /**
     * Unhold quote by id
     *
     * @param int $id id
     *
     * @return bool
     */
    public function unholdQuoteById($id)
    {
        $quote = $this->quotationRepository->get($id);
        if ($quote->unhold()) {
            $quote->setUpdatedAt(date('Y-m-d h:i:s'));
            $this->quotationRepository->save($quote);
            
            $this->eventManager->dispatch(
                'sales_quotation_process_after', [
                'quote' => $quote,
                'action' => 'unhold'
                ]
            );
            
            return true;
        }
        return false;
    }

    /**
     * Cancel quote by id
     *
     * @param int $id id
     *
     * @return bool
     */
    public function cancelQuoteById($id)
    {
        $quote = $this->quotationRepository->get($id);
        if ($quote->cancel()) {
            $quote->setUpdatedAt(date('Y-m-d h:i:s'));
            $this->quotationRepository->save($quote);
            
            $this->eventManager->dispatch(
                'sales_quotation_process_after', [
                'quote' => $quote,
                'action' => 'cancel'
                ]
            );
            
            return true;
        }
        return false;
    }
}
