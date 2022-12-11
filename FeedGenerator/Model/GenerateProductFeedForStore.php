<?php
declare(strict_types=1);

namespace MageTwoDev\FeedGenerator\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Bundle\Model\Product\Type as BundleProduct;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use MageTwoDev\FeedGenerator\CollectionProvider\ProductsCollectionProvider;
use MageTwoDev\FeedGenerator\Writer\CsvFileWriterProvider;
use MageTwoDev\FeedGenerator\Exception\GenerateFeedForStoreException;
use MageTwoDev\FeedGenerator\Exception\HandlerIsNotSpecifiedException;
use MageTwoDev\FeedGenerator\Exception\WrongInstanceException;
use MageTwoDev\FeedGenerator\ConfigProvider\FeedConfigProvider;
use MageTwoDev\FeedGenerator\DataProvider\AttributesConfigListProvider;
use MageTwoDev\FeedGenerator\Converter\ArrayToCsvConverter;
use MageTwoDev\FeedGenerator\Mapper\ProductToFeedAttributesRowMapper;
use MageTwoDev\FeedGenerator\DataProvider\AllowedCategoryIdsProvider;
//use MageTwoDev\FeedGenerator\ConfigProvider\FeedConfigProvider;
use function in_array;


class GenerateProductFeedForStore
{
    private const STATUS_ENABLED = 1;

    private FeedConfigProvider $configProvider;

    private AttributesConfigListProvider $attributesConfigListProvider;

    private ProductToFeedAttributesRowMapper $productToRowMapper;

    private CsvFileWriterProvider $csvFileWriterProvider;

    private ProductsCollectionProvider $productsCollectionProvider;

    //private AllowedCategoryIdsProvider $allowedCategoryIdsProvider;

    private ArrayToCsvConverter $arrayToCsvConverter;

    private ProductRepositoryInterface $productRepository;

    private AllowedCategoryIdsProvider $allowedCategoryIdsProvider;

    /**
     * @param FeedConfigProvider $configProvider
     * @param AttributesConfigListProvider $attributesConfigListProvider
     * @param ProductToFeedAttributesRowMapper $productToRowMapper
     * @param CsvFileWriterProvider $csvFileWriterProvider
     * @param ProductsCollectionProvider $productsCollectionProvider
     * @param ArrayToCsvConverter $arrayToCsvConverter
     * @param ProductRepositoryInterface $productRepository
     * @param AllowedCategoryIdsProvider $allowedCategoryIdsProvider
     */
    public function __construct(
        FeedConfigProvider $configProvider,
        AttributesConfigListProvider $attributesConfigListProvider,
        ProductToFeedAttributesRowMapper $productToRowMapper,
        CsvFileWriterProvider $csvFileWriterProvider,
        ProductsCollectionProvider $productsCollectionProvider,
        //AllowedCategoryIdsProvider $allowedCategoryIdsProvider,
        ArrayToCsvConverter $arrayToCsvConverter,
        ProductRepositoryInterface $productRepository,
        AllowedCategoryIdsProvider $allowedCategoryIdsProvider
    ) {
        $this->configProvider = $configProvider;
        $this->attributesConfigListProvider = $attributesConfigListProvider;
        $this->productToRowMapper = $productToRowMapper;
        $this->csvFileWriterProvider = $csvFileWriterProvider;
        $this->productsCollectionProvider = $productsCollectionProvider;
        //$this->allowedCategoryIdsProvider = $allowedCategoryIdsProvider;
        $this->arrayToCsvConverter = $arrayToCsvConverter;
        $this->productRepository = $productRepository;
        $this->allowedCategoryIdsProvider = $allowedCategoryIdsProvider;
    }

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(StoreInterface $store, string $vendor=''): void
    {
        $storeId = (int)$store->getId();

        if (!$this->configProvider->isEnabled($storeId)) {
            //this should be logged
            return;
        }

        try {
            $fileWriter = $this->csvFileWriterProvider->get($store, $vendor);
        } catch (NoSuchEntityException $exception) {
            throw new GenerateFeedForStoreException(
                __('The file writer cannot be created for the store with id: %1', $storeId),
                $exception
            );
        }

        try {
            $attributesConfigList = $this->attributesConfigListProvider->get();
        } catch (\InvalidArgumentException $exception) {
            throw new GenerateFeedForStoreException(
                __('Attributes config list is invalid. %1' . $exception->getMessage()),
                $exception
            );
        }

        //romeof1980: whitelistedCategories is not needed for this implementation but very useful for real-use cases
        //we are using it here to limit an enormous nr of products during testing
        $whitelistedCategories = $this->allowedCategoryIdsProvider->get($storeId);

        $currentPage = 1;
        $rows = [];
        do {
            $collection = $this->productsCollectionProvider->get(
                $currentPage,
                $whitelistedCategories,
                $storeId
            );


            /** @var Product[] $items */
            $items = $collection->getItems();

            foreach ($items as $product) {
                if (isset($rows[$product->getId()])) {
                    continue;
                }

                $typeInstance = $product->getTypeInstance();

                // CONFIGURABLE AND GROUPED PRODUCTS FLOW
                if ($typeInstance instanceof Configurable || $typeInstance instanceof Grouped) {
                    $childProducts = $typeInstance instanceof Grouped ?
                        $typeInstance->getAssociatedProducts($product) : $typeInstance->getUsedProducts($product);
                    foreach ($childProducts as $childProduct) {
                        if ((int)$childProduct->getStatus() !== self::STATUS_ENABLED) {
                            continue;
                        }
                        try {
                            $childProduct = $this->productRepository
                                ->get($childProduct->getSku(), false, $childProduct->getStoreId());
                            $rows[$childProduct->getId()] = $this->productToRowMapper
                                ->map($childProduct, $attributesConfigList);
                        } catch (HandlerIsNotSpecifiedException|WrongInstanceException $exception) {
                            throw new GenerateFeedForStoreException(
                                __(
                                    'Product can not be mapped to feed row. Product ID: %1 . Error: %2',
                                    $product->getId(),
                                    $exception->getMessage()
                                ),
                                $exception
                            );
                        }
                    }
                    $currentPage++;
                    continue;
                }

                // BUNDLE PRODUCTS FLOW
                if ($typeInstance instanceof BundleProduct) {
                    $childProductIds = $typeInstance->getChildrenIds($product->getId());
                    foreach ($childProductIds as $productIds) {
                        foreach ($productIds as $childProductId) {
                            try {
                                $childProduct = $this->productRepository
                                    ->getById($childProductId, false, $product->getStoreId());
                                if ((int)$childProduct->getStatus() !== self::STATUS_ENABLED) {
                                    continue;
                                }
                                $rows[$childProduct->getId()] = $this->productToRowMapper
                                    ->map($childProduct, $attributesConfigList);
                            } catch (HandlerIsNotSpecifiedException|WrongInstanceException $exception) {
                                throw new GenerateFeedForStoreException(
                                    __(
                                        'Product can not be mapped to feed row. Product ID: %1 . Error: %2',
                                        $product->getId(),
                                        $exception->getMessage()
                                    ),
                                    $exception
                                );
                            }
                        }
                    }
                    $currentPage++;
                    continue;
                }

                // SIMPLE PRODUCTS FLOW
                try {
                    $rows[$product->getId()] = $this->productToRowMapper->map($product, $attributesConfigList);
                } catch (HandlerIsNotSpecifiedException|WrongInstanceException $exception) {
                    throw new GenerateFeedForStoreException(
                        __(
                            'Product can not be mapped to feed row. Product ID: %1 . Error: %2',
                            $product->getId(),
                            $exception->getMessage()
                        ),
                        $exception
                    );
                }
            }

            $currentPage++;
        } while ($this->canProceed($collection, $currentPage));

        //TODO romeof1980: here we must take care of the csv
        //we do not need the returned value: just checking what we have sorted reflects teh created csv
        $this->arrayToCsvConverter->convertToCsv($rows, $store, $vendor);
        //$fileWriter->write($dataForCsv);
    }

    private function canProceed(ProductCollection $productCollection, int $currentPage): bool
    {
        $pageSize = $productCollection->getPageSize();
        return $pageSize * $currentPage < $productCollection->getSize() + $pageSize;
    }

}
