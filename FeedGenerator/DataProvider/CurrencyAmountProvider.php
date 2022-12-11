<?php
declare(strict_types=1);

namespace MageTwoDev\FeedGenerator\DataProvider;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data;

use function str_replace;

class CurrencyAmountProvider
{
    private CurrencyProvider $currencyProvider;
    private Data $priceHelper;

    public function __construct(
        CurrencyProvider $currencyProvider,
        Data $priceHelper
    ) {
        $this->currencyProvider = $currencyProvider;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function get(float $amount, int $storeId): array|string
    {
        $price = $this->priceHelper->currencyByStore($amount, $storeId, true, false);
        $currency = $this->currencyProvider->get($storeId);

        return str_replace($currency->getCurrencySymbol(), $currency->getCode(), $price);
    }
}
