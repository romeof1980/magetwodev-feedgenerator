<?php

namespace MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageTwoDev\FeedGenerator\DataProvider\CurrencyAmountProvider;

class PriceProvider implements AttributeHandlerInterface
{
    private CurrencyAmountProvider $currencyAmountProvider;

    public function __construct(CurrencyAmountProvider $currencyAmountProvider)
    {
        $this->currencyAmountProvider = $currencyAmountProvider;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function get(Product $product): string
    {
        return $this->currencyAmountProvider->get(
            (float)$product->getFinalPrice(),
            (int)$product->getStoreId()
        );
    }

}
