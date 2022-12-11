<?php

namespace MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers;

use Magento\Catalog\Model\Product;

class BrandProvider implements AttributeHandlerInterface
{

    /**
     * @param Product $product
     * @return string
     */
    public function get(Product $product): string
    {
        try{
            $brand = $product->getData('brand');
            if(is_null($brand) || strlen($brand) < 1){
                return 'brand';
            }
            return $brand;
        }
        catch(\Exception $exception){
            //todo log the exception
            //todo: implement brand logic
            return 'brand';
        }

    }

}
