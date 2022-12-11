<?php

namespace MageTwoDev\FeedGenerator\DataProvider;


use InvalidArgumentException;
use MageTwoDev\FeedGenerator\Data\AttributeConfigData;
use MageTwoDev\FeedGenerator\Data\AttributeConfigDataFactory;
use MageTwoDev\FeedGenerator\Data\AttributeConfigDataList;
use MageTwoDev\FeedGenerator\Enum\AttributesToImportEnumInterface;

class AttributesConfigListProvider
{
    private AttributeConfigDataFactory $attributeDataFactory;

    public function __construct(AttributeConfigDataFactory $attributeDataFactory)
    {
        $this->attributeDataFactory = $attributeDataFactory;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(): AttributeConfigDataList
    {
        $attributes = [];

        foreach (AttributesToImportEnumInterface::ATTRIBUTES as $attributeConfig) {
            $attributes[] = $this->attributeDataFactory->create(
                [
                    'data' => [
                        AttributeConfigData::FIELD_NAME => $attributeConfig[ AttributeConfigData::FIELD_NAME ],
                        AttributeConfigData::ATTRIBUTE_HANDLER =>
                            $attributeConfig [ AttributeConfigData::ATTRIBUTE_HANDLER ],
                    ],
                ],
            );
        }

        return new AttributeConfigDataList($attributes);
    }

}
