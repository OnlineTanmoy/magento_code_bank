<?php

namespace Appseconnect\ServiceRequest\Console\Command;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Appseconnect\ServiceRequest\Model\ResourceModel\RequestPost\CollectionFactory;

/**
 * Class CleanOldServiceRequest
 */
class CleanOldServiceRequest extends Command
{
    private $state;
    protected $_customerFactory;

    /**
     * CleanOldServiceRequest constructor.
     * @param State $state
     * @param CollectionFactory $serviceRequestCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        CollectionFactory $serviceRequestCollectionFactory
    ) {
        $this->serviceRequestCollectionFactory = $serviceRequestCollectionFactory;
        parent::__construct();
        $this->state = $state;
    }

    protected function configure()
    {
        $options = [];
        $this->setName('appseconnect:clean-old-service-request');
        $this->setDescription('Clean 30 days old draft service request, e.g [bin/magento appseconnect:clean-old-service-request]');
        $this->setDefinition($options);

        parent::configure();
    }

    /**
     * Clean 30 days old draft service request
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $output->writeln("Clean old draft ServiceRequest, started --------");
        $serviceRequestCollection = $this->serviceRequestCollectionFactory->create();
        $serviceRequestCollection->getSelect()->where('post <= (NOW() - INTERVAL 1 MONTH) and status=1');
        foreach ($serviceRequestCollection as $_serviceRequest) {
            $output->writeln("Deleted id - " . $_serviceRequest->getId() . " of dated " . $_serviceRequest->getPost());
            $_serviceRequest->delete();
        }
        $output->writeln("Clean old draft ServiceRequest, end --------");
    }
}

