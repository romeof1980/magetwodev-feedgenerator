<?php

namespace MageTwoDev\FeedGenerator\Enum;

use MageTwoDev\FeedGenerator\Data\AttributeConfigData;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\SkuProvider;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\NameProvider;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\DescriptionProvider;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\ManufacturerProvider;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\BrandProvider;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\ProductUrlProvider;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\ImageLinkProvider;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\PriceProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\AdditionalImageLinkProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\CategoryUrlProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\EanProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\GenderProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\ColorProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\IsInStockProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\ItemGroupIdProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\MaterialProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\SizeProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\PatternProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\ProductDetailProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\ProductTypeProvider;
//use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\ShippingProvider;

interface AttributesToImportEnumInterface
{
    public const ATTRIBUTES = [
        /*'category_url' => [
            AttributeConfigData::FIELD_NAME => 'category_url',
            AttributeConfigData::ATTRIBUTE_HANDLER => CategoryUrlProvider::class,
        ],
        'color' => [
            AttributeConfigData::FIELD_NAME => 'color',
            AttributeConfigData::ATTRIBUTE_HANDLER => ColorProvider::class,
        ],*/
        'description' => [
            AttributeConfigData::FIELD_NAME => 'description',
            AttributeConfigData::ATTRIBUTE_HANDLER => DescriptionProvider::class,
        ],
        /*'ean' => [
            AttributeConfigData::FIELD_NAME => 'ean',
            AttributeConfigData::ATTRIBUTE_HANDLER => EanProvider::class,
        ],
        'gender' => [
            AttributeConfigData::FIELD_NAME => 'gender',
            AttributeConfigData::ATTRIBUTE_HANDLER => GenderProvider::class,
        ],
        'item_group_id' => [
            AttributeConfigData::FIELD_NAME => 'item_group_id',
            AttributeConfigData::ATTRIBUTE_HANDLER => ItemGroupIdProvider::class,
        ],*/
        'image_link' => [
            AttributeConfigData::FIELD_NAME => 'image_link',
            AttributeConfigData::ATTRIBUTE_HANDLER => ImageLinkProvider::class,
        ],
        /*'additional_image_link' => [
            AttributeConfigData::FIELD_NAME => 'additional_image_link',
            AttributeConfigData::ATTRIBUTE_HANDLER => AdditionalImageLinkProvider::class,
        ],
        'is_in_stock' => [
            AttributeConfigData::FIELD_NAME => 'is_in_stock',
            AttributeConfigData::ATTRIBUTE_HANDLER => IsInStockProvider::class,
        ],*/
        'manufacturer' => [
            AttributeConfigData::FIELD_NAME => 'manufacturer',
            AttributeConfigData::ATTRIBUTE_HANDLER => ManufacturerProvider::class,
        ],
        /*'material' => [
            AttributeConfigData::FIELD_NAME => 'material',
            AttributeConfigData::ATTRIBUTE_HANDLER => MaterialProvider::class,
        ],
        'size' => [
            AttributeConfigData::FIELD_NAME => 'size',
            AttributeConfigData::ATTRIBUTE_HANDLER => SizeProvider::class,
        ],
        'material_cloth' => [
            AttributeConfigData::FIELD_NAME => 'material_cloth',
            AttributeConfigData::ATTRIBUTE_HANDLER => ManufacturerProvider::class,
        ],*/
        'name' => [
            AttributeConfigData::FIELD_NAME => 'name',
            AttributeConfigData::ATTRIBUTE_HANDLER => NameProvider::class,
        ],
        /*'pattern' => [
            AttributeConfigData::FIELD_NAME => 'pattern',
            AttributeConfigData::ATTRIBUTE_HANDLER => PatternProvider::class,
        ],*/
        'price' => [
            AttributeConfigData::FIELD_NAME => 'price',
            AttributeConfigData::ATTRIBUTE_HANDLER => PriceProvider::class,
        ],
        /*'product_detail' => [
            AttributeConfigData::FIELD_NAME => 'product_detail',
            AttributeConfigData::ATTRIBUTE_HANDLER => ProductDetailProvider::class,
        ],
        'product_type' => [
            AttributeConfigData::FIELD_NAME => 'product_type',
            AttributeConfigData::ATTRIBUTE_HANDLER => ProductTypeProvider::class,
        ],
        'shipping' => [
            AttributeConfigData::FIELD_NAME => 'shipping',
            AttributeConfigData::ATTRIBUTE_HANDLER => ShippingProvider::class,
        ],*/
        'sku' => [
            AttributeConfigData::FIELD_NAME => 'sku',
            AttributeConfigData::ATTRIBUTE_HANDLER => SkuProvider::class,
        ],
        'url' => [
            AttributeConfigData::FIELD_NAME => 'url',
            AttributeConfigData::ATTRIBUTE_HANDLER => ProductUrlProvider::class,
        ],
        'brand' => [
            AttributeConfigData::FIELD_NAME => 'brand',
            AttributeConfigData::ATTRIBUTE_HANDLER => BrandProvider::class,
        ]
    ];

}
