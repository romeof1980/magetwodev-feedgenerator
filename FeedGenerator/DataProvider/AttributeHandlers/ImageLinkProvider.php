<?php

namespace MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers;

use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use MageTwoDev\FeedGenerator\DataProvider\ParentProductProvider;
use MageTwoDev\FeedGenerator\DataProvider\ProductImageUrlProvider;

class ImageLinkProvider implements AttributeHandlerInterface
{
    private ParentProductProvider $parentProductProvider;
    private ProductImageUrlProvider $productImageUrlProvider;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ParentProductProvider $parentProductProvider,
        ProductImageUrlProvider $productImageUrlProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->parentProductProvider = $parentProductProvider;
        $this->productImageUrlProvider = $productImageUrlProvider;
        $this->storeManager = $storeManager;
    }

    public function get(Product $product): string
    {
        $this->storeManager->setCurrentStore($product->getStoreId());

        if ($product->getImage()) {
            return $this->productImageUrlProvider->get($product->getImage());
        }

        $parentProduct = $this->parentProductProvider->get($product);

        if (!$parentProduct->getImage()) {
            return '';
        }

        return $this->productImageUrlProvider->get($parentProduct->getImage());
    }

}
