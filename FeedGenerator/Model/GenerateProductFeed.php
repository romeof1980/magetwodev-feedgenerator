<?php
declare(strict_types=1);

namespace MageTwoDev\FeedGenerator\Model;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use MageTwoDev\FeedGenerator\Registry\FeedRegistry;
use MageTwoDev\FeedGenerator\Model\GenerateProductFeedForStore as GenerateFeedForStore;
//romeof1980: the call to the below function defies design patterns
//but could be an insight for performance leaks during development: https://www.php.net/manual/en/features.gc.collecting-cycles.php
use function gc_collect_cycles;

class GenerateProductFeed
{
    //private StoreManagerInterface $storeManager;

    private StoreRepositoryInterface $storeRepository;

    private GenerateFeedForStore $generateFeedForStore;

    private FeedRegistry $registry;

    private Emulation $emulation;

    private array $storeModelList;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param GenerateProductFeedForStore $generateFeedForStore
     * @param FeedRegistry $registry
     * @param Emulation $emulation
     */
    public function __construct(
        //StoreManagerInterface $storeManager,
        StoreRepositoryInterface $storeRepository,
        GenerateFeedForStore $generateFeedForStore,
        FeedRegistry $registry,
        Emulation $emulation
    ) {
        //$this->storeManager = $storeManager;
        $this->storeRepository = $storeRepository;
        $this->generateFeedForStore = $generateFeedForStore;
        $this->registry = $registry;
        $this->emulation = $emulation;
        $this->storeModelList = [];
    }

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(string $vendor='', array $stores=null): void
    {

        //here only the stores selected by the user should be passed (if present)
        //and vendor should be checked against as well
        //if no stores is passed generate for all stores
        if(is_null($stores) || count($stores) === 0){
            $stores = $this->storeRepository->getList();
        }
        else{
            $z=0;
            foreach ($stores as $store){
                $storeModel = $this->storeRepository->getById($store);
                $this->storeModelList[$z] = $storeModel;
                $z++;
            }
            if ($this->storeModelList[0]->getId() || count($this->storeModelList >= 1)){
                unset($stores);
                $stores = $this->storeModelList;
            }

        }
        //todo: implement possibility to choose against a specific vendor at command output (i.e. vendor == google-sh)
        //for now by default using an "googleshopping" as vendor value if no vendor is selected only for testing purposes (testing file name)
        if (strlen($vendor) === 0){
            $vendor = "googleshopping";
        }

        foreach ($stores as $store) {
            $this->emulation->startEnvironmentEmulation($store->getId());

            /** @var \Magento\Store\Api\Data\StoreInterface $store */
            $this->generateFeedForStore->execute($store, $vendor);
            $this->registry->cleanForStore((int)$store->getId());

            $this->emulation->stopEnvironmentEmulation();

            gc_collect_cycles();
        }
    }
}
