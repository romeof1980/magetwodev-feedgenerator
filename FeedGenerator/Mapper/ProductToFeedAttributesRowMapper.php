<?php

namespace MageTwoDev\FeedGenerator\Mapper;

use Magento\Catalog\Model\Product;
use MageTwoDev\FeedGenerator\Data\AttributeConfigDataList;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlerProvider;
use MageTwoDev\FeedGenerator\Exception\HandlerIsNotSpecifiedException;
use MageTwoDev\FeedGenerator\Exception\WrongInstanceException;
use Magento\Catalog\Api\Data\ProductInterface;

class ProductToFeedAttributesRowMapper
{
    private AttributeHandlerProvider $handlerProvider;

    public function __construct(AttributeHandlerProvider $handlerProvider)
    {
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @throws HandlerIsNotSpecifiedException
     * @throws WrongInstanceException
     */
    public function map(Product|ProductInterface $product, AttributeConfigDataList $attributesConfigList): array
    {
        $collectedData = [];

        foreach ($attributesConfigList->getList() as $attribute) {
            $attributeDataProvider = $this->handlerProvider->get($attribute);
            $fieldName = $attribute->getFieldName();
            $collectedData[$fieldName] = $attributeDataProvider->get($product);
        }

        return $collectedData;
    }
}
