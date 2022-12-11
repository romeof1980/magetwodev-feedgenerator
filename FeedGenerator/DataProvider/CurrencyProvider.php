<?php

namespace MageTwoDev\FeedGenerator\DataProvider;

use Magento\Directory\Model\Currency;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class CurrencyProvider
{
    private StoreManagerInterface $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function get(int $storeId): Currency
    {
        return $this->storeManager
            ->getStore($storeId)
            ->getCurrentCurrency();
    }

}
