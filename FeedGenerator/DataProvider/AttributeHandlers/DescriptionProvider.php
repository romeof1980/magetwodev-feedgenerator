<?php

namespace MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers;

use Magento\Catalog\Model\Product;

class DescriptionProvider implements AttributeHandlerInterface
{

    /**
     * @param Product $product
     * @return string
     */
    public function get(Product $product): string
    {
        return $product->getData('description');
    }

}
