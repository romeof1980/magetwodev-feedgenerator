<?php
declare(strict_types=1);

namespace MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers;

use Magento\Catalog\Model\Product;

class SkuProvider implements AttributeHandlerInterface
{

    /**
     * @param Product $product
     * @return string
     */
    public function get(Product $product): string
    {
        return $product->getSku();
    }

}
