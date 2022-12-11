<?php

namespace MageTwoDev\FeedGenerator\Converter;

use Magento\Framework\Exception\FileSystemException;
use MageTwoDev\FeedGenerator\Writer\CsvFileWriterProvider;
use MageTwoDev\FeedGenerator\Writer\FileWriter;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Catalog\Model\Product\LinkTypeProvider;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\ImportExport\Model\Export\Adapter\AbstractAdapter;
use Magento\ImportExport\Model\Import;
use Magento\Store\Model\Store;
use MageTwoDev\FeedGenerator\Model\Export\RowCustomizer\Composite;
use Psr\Log\LoggerInterface;
use Magento\ImportExport\Model\Export\ConfigInterface;
use Magento\ImportExport\Model\Export\Adapter\Factory as ExportAdapterFactory;


class ArrayToCsvConverter
{

    //private DateTime $dateTime;
    private $rowCustomizer;

    private $logger;

    /** @var FileWriter */
    protected $writer;

    /** @var CsvFileWriterProvider */
    protected $csvFileWriterProvider;

    protected $headerColumns = [];

    protected $fieldsMap = [
        'image' => 'base_image',
        'image_label' => "base_image_label",
        'thumbnail' => 'thumbnail_image',
        'thumbnail_label' => 'thumbnail_image_label',
        //self::COL_MEDIA_IMAGE => 'additional_images',
        '_media_image_label' => 'additional_image_labels',
        self::COL_STORE => 'store_view_code',
        self::COL_ATTR_SET => 'attribute_set_code',
        //self::COL_TYPE => 'product_type',
        //self::COL_CATEGORY => 'categories',
        self::COL_PRODUCT_WEBSITES => 'product_websites',
        'status' => 'product_online',
        'news_from_date' => 'new_from_date',
        'news_to_date' => 'new_to_date',
        'options_container' => 'display_product_options_in',
        'minimal_price' => 'map_price',
        'msrp' => 'msrp_price',
        'msrp_enabled' => 'map_enabled',
        'special_from_date' => 'special_price_from_date',
        'special_to_date' => 'special_price_to_date',
        'min_qty' => 'out_of_stock_qty',
        'backorders' => 'allow_backorders',
        'min_sale_qty' => 'min_cart_qty',
        'max_sale_qty' => 'max_cart_qty',
        'notify_stock_qty' => 'notify_on_stock_below',
        'meta_keyword' => 'meta_keywords',
        'tax_class_id' => 'tax_class_name',
    ];

    protected $exportMainAttrCodes = [
        self::COL_SKU,
        'name',
        'description',
        //'short_description',
        //'weight',
        //'product_online',
        //'tax_class_name',
        //'visibility',
        //'price',
        //'special_price',
        //'special_price_from_date',
        //'special_price_to_date',
        'url_key',
        //'meta_title',
        //'meta_keywords',
        //'meta_description',
        'base_image',
        //'base_image_label',
        //'small_image',
        //'small_image_label',
        //'thumbnail_image',
        //'thumbnail_image_label',
        //'swatch_image',
        //'swatch_image_label',
        //'created_at',
        //'updated_at',
        //'new_from_date',
        //'new_to_date',
        //'display_product_options_in',
        //'map_price',
        //'msrp_price',
        //'map_enabled',
        //'special_price_from_date',
        //'special_price_to_date',
        //'gift_message_available',
        //'custom_design',
        //'custom_design_from',
        //'custom_design_to',
        //'custom_layout_update',
        //'page_layout',
        //'product_options_container',
        //'msrp_price',
        //'msrp_display_actual_price_type',
        //'map_enabled',
        //'country_of_manufacture',
        //'map_price',
        //'display_product_options_in',
    ];

    private $imageLabelAttributes = [
        'base_image_label',
        //'small_image_label',
        //'thumbnail_image_label',
        //'swatch_image_label',
    ];
    protected $websiteIdToCode = [];

    /**
     * Provider of product link types
     *
     * @var LinkTypeProvider
     */
    protected $linkTypeProvider;

    protected $parameters = [];

    public const COL_STORE = '_store';

    public const COL_ATTR_SET = '_attribute_set';

    public const COL_PRODUCT_WEBSITES = '_product_websites';

    public const COL_SKU = 'sku';

    public const COL_ADDITIONAL_ATTRIBUTES = 'additional_attributes';

    public const COL_TYPE = '_type';

    public const COL_CATEGORY = '_category';

    protected $exportConfig;


    /**
     * @param Composite $rowCustomizer
     * @param LoggerInterface $logger
     * @param ConfigInterface $exportConfig
     * @param FileWriter $writer
     * @param CsvFileWriterProvider $csvFileWriterProvider
     */
    public function __construct(
        //DateTime $dateTime,
        Composite $rowCustomizer,
        LoggerInterface $logger,
        ConfigInterface $exportConfig,
        FileWriter $writer,
        CsvFileWriterProvider $csvFileWriterProvider
    )
    {
        //$this->dateTime = $dateTime;
        $this->rowCustomizer = $rowCustomizer;
        $this->logger = $logger;
        $this->exportConfig = $exportConfig;
        $this->writer = $writer;
        $this->csvFileWriterProvider = $csvFileWriterProvider;
    }


    /**
     * @throws LocalizedException
     * @throws FileSystemException
     */
    public function convertToCsv(array $productData, StoreInterface $store, $vendor) : void
    {
        //Execution time may be very long
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        set_time_limit(0);

        $i = 1;
        foreach ($productData as $prodRow) {
            if ($i == 1) {
                $this->writer = $this->csvFileWriterProvider->get($store, $vendor);
                $this->setHeaderColumns($customOption = []);
                $headerColumns = implode("," , $this->headerColumns);
                //todo: refine (as per requirements we do not add datetime to filename)
                //first line: open without appending (overwrite existing file content if present: good for testing)
                $this->writer->write($headerColumns);
                $this->writer->write("\n", "a");
            }
            $i++;
            /** @array $row */
            $row = $this->_customFieldsMapping($prodRow);
            /** @string $convertedRow */
            $convertedRow = (implode(",", $row));
            //appending new lines (product)
            $this->writer->write($convertedRow, "a");
            $this->writer->write("\n", "a");
        }

        return;

    }

    public function _getHeaderColumns()
    {
        return $this->_customHeadersMapping($this->rowCustomizer->addHeaderColumns($this->headerColumns));
    }

    protected function _customHeadersMapping($rowData)
    {
        foreach ($rowData as $key => $fieldName) {
            if (isset($this->_fieldsMap[$fieldName])) {
                $rowData[$key] = $this->_fieldsMap[$fieldName];
            }
        }
        return $rowData;
    }

    protected function setHeaderColumns($customOptionsData, /*$stockItemRows*/)
    {
        $exportAttributes = (
            array_key_exists("skip_attr", $this->parameters) && count($this->parameters["skip_attr"])
        ) ?
            array_intersect(
                $this->getExportMainAttrCodes(),
            //todo romeof1980: skipping the following array merge for now: probably not needed
            /*array_merge(
                $this->_customHeadersMapping($this->getExportAttrCodes()),
                $this->getNonSystemAttributes()
            )*/
            ) :
            $this->getExportMainAttrCodes();

        if (!$this->headerColumns) {
            $this->headerColumns = array_merge(
                [
                    self::COL_SKU,
                    self::COL_STORE,
                    self::COL_ATTR_SET,
                    //self::COL_TYPE,
                    //self::COL_CATEGORY,
                    self::COL_PRODUCT_WEBSITES,
                ],
                $exportAttributes,
                [self::COL_ADDITIONAL_ATTRIBUTES],
            /*reset($stockItemRows) ? array_keys(end($stockItemRows)) : [],
            [
                'related_skus',
                'related_position',
                'crosssell_skus',
                'crosssell_position',
                'upsell_skus',
                'upsell_position',
                'additional_images',
                'additional_image_labels',
                'hide_from_product_page',
                'custom_options'
            ]*/
            );
        }
    }

    protected function getExportMainAttrCodes()
    {
        return $this->exportMainAttrCodes;
    }

    protected function _customFieldsMapping($rowData)
    {
        foreach ($this->fieldsMap as $systemFieldName => $fileFieldName) {
            if (isset($rowData[$systemFieldName])) {
                $rowData[$fileFieldName] = $rowData[$systemFieldName];
                unset($rowData[$systemFieldName]);
            }
        }
        return $rowData;
    }

}
