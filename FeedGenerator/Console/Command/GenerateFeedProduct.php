<?php
declare(strict_types=1);

namespace MageTwoDev\FeedGenerator\Console\Command;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use MageTwoDev\FeedGenerator\Model\GenerateProductFeed;
use MageTwoDev\FeedGenerator\ConfigProvider\FeedConfigProvider;
use Magento\Store\Api\StoreRepositoryInterface;
use Symfony\Component\Console\Input\InputOption;

class GenerateFeedProduct extends \Symfony\Component\Console\Command\Command
{

    /** @var State */
    protected $appState;

    /** @var GenerateProductFeed */
    protected $productFeed;

    /** @var string */
    protected $vendor;

    /** @var array */
    protected $stores;

    /** @var FeedConfigProvider */
    protected $feedConfigProvider;

    /** @var StoreRepositoryInterface */
    private  $storeRepository;

    const STORE = 'store';


    /**
     * @param State $appState
     * @param GenerateProductFeed $productFeed
     * @param FeedConfigProvider $feedConfigProvider
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        State $appState,
        GenerateProductFeed $productFeed,
        FeedConfigProvider $feedConfigProvider,
        StoreRepositoryInterface $storeRepository
    ){
        $this->appState = $appState;
        $this->productFeed = $productFeed;
        $this->feedConfigProvider = $feedConfigProvider;
        $this->storeRepository = $storeRepository;
        parent::__construct();
    }

    /**
     * configures the current command
     * @return void
     */
    protected function configure() : void
    {

        $this->setName('magetwodev:feedgenerator:products:generategooglesh');
        $this->setDescription('Generates feed for chosen vendor (now supports only "googleshopping").');

        $commandOptions = [new InputOption(self::STORE, null, InputOption::VALUE_REQUIRED, 'Store')];
        $this->setDefinition($commandOptions);

        parent::configure();
    }


    /**
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //todo: accepting single input value for now (but passing as array to the receiving module: need refinement if we want to pass multiple values
        $output->setDecorated(true);
        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        //$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($tableToCountHere));
        //$progress->setFormat('<comment>%message%</comment> %current%/%max% [%bar%] %percent:3s%% %elapsed%');

        //todo: if needed check here if module enable based on specific store
        $isModuleEnabled = $this->feedConfigProvider->isEnabled();
        if(!$isModuleEnabled){
            $output->writeln("");
            $output->writeln("<info>FeedGenerator module is not enabled (backend):</info>");
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        }
        else{
            $store = $input->getOption(self::STORE);
            if(!$store){
                $output->writeln("<info>No store selected: choose one of the following options: </info>");
                $this->stores = $this->getStores();
                $output->writeln('<info>List of available stores: [choose using cmd with option --store="store_code". Use --store="all" for all stores.]</info>');
                foreach ($this->stores as $store){
                    $currentStore = $store->getCode();
                    $output->writeln("<info>" . $currentStore . "</info>");
                }
                return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
            }
        }

        $storeCodes = $this->getStoreCodes();
        $storeExist = in_array($store, $storeCodes);
        if($storeExist == 1){
            $output->writeln("<info>Selected store: " . $store . "</info>");
        }
        else{
            $stores = [];
            $output->writeln("<info>No store selected: proceeding with default values [--all].</info>");
        }

        try {
            //here the single vendor and the stores parameters should be passed
            $this->vendor = '';     //todo: should be removed or managed better based on the user input

            //todo: refine: we rather prefer to pass an arrays to allow multiple values
            $stores[] = $store;
            $this->productFeed->execute($this->vendor, $stores);

            $output->writeln("");
            $output->writeln("<info>Feed successfully generated:</info>");
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {

            $output->writeln("");
            $output->writeln("<error>{$exception->getMessage()}</error>");
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }

    //todo: this should probably placed in a separate "source" class, not here in the cmd class
    private function getStores(){
        $stores = $this->storeRepository->getList();
        return $stores;
    }

    //todo: this should probably placed in a separate "source" class, not here in the cmd class
    private function getStoreCodes(){
        $stores = $this->getStores();
        $storeCodes = [];
        foreach ($stores as $store){
            $storeCode = $store->getCode();
            $storeCodes [] = $storeCode;
        }

        return $storeCodes;
    }


}
