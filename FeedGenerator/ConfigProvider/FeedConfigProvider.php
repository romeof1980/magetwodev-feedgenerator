<?php

namespace MageTwoDev\FeedGenerator\ConfigProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use function explode;

class FeedConfigProvider
{
    private const CONFIG_PATH_FEED_IS_ENABLED = 'feedgenerator/general/enabled';
    private const CONFIG_PATH_CATEGORY_WHITELIST = 'feedgenerator/general/category_whitelist';
    private const CONFIG_PATH_CATEGORY_BLACKLIST = 'feedgenerator/general/category_blacklist';

    private ScopeConfigInterface $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    public function isEnabled(): bool
    {
        return $this->config->isSetFlag(self::CONFIG_PATH_FEED_IS_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }

    public function isEnabledForStore(int $storeId): bool
    {
        return $this->config->isSetFlag(self::CONFIG_PATH_FEED_IS_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getCategoryWhitelist(int $storeId): array
    {
        $categoriesWhitelistString = $this->config->getValue(
            self::CONFIG_PATH_CATEGORY_WHITELIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $categoriesWhitelistString !== null ? explode(',', $categoriesWhitelistString) : [];
    }

    public function getCategoryBlacklist(int $storeId): array
    {
        $categoriesBlacklistString = $this->config->getValue(
            self::CONFIG_PATH_CATEGORY_BLACKLIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $categoriesBlacklistString !== null ? explode(',', $categoriesBlacklistString) : [];
    }
}
