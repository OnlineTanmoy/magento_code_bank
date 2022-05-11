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

namespace Appseconnect\B2BMage\Model\ResourceModel\Salesrepgrid\Grid;

use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 *
 * @category B2BMage\Model
 * @package  Appseconnect
 * @author   Insync Magento Team <contact@insync.co.in>
 * @license  Insync https://insync.co.in
 * @link     https://www.appseconnect.com/
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * String
     *
     * @var string
     */
    public $document = Document::class;

    /**
     * Array
     *
     * @var array
     */
    public $_map = ['fields' => ['entity_id' => 'main_table.id']];

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory EntityFactory
     * @param Logger        $logger        Logger
     * @param FetchStrategy $fetchStrategy FetchStrategy
     * @param EventManager  $eventManager  EventManager
     * @param string        $mainTable     MainTable
     * @param string        $resourceModel ResourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'insync_salesrep_grid',
        $resourceModel = \Appseconnect\B2BMage\Model\ResourceModel\Salesrepgrid::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
}
