<?php
declare(strict_types=1);

namespace MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers;

use Magento\Catalog\Model\Product;

interface AttributeHandlerInterface
{
    /**
     * @param Product $product
     * @return mixed
     */
    public function get(Product $product): mixed;
}
