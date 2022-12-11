<?php
declare(strict_types=1);

namespace MageTwoDev\FeedGenerator\Writer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use MageTwoDev\FeedGenerator\Writer\FileWriterFactory;

use function sprintf;

class CsvFileWriterProvider
{
    public const DIRECTORY_PATH = 'var/feed';

    //romeof1980: filename must be "[store_code]_feed_[vendor].csv"
    public const FILE_NAME_PATTERN = '%s_feed_%s.csv';

    private FileWriterFactory $fileWriterFactory;

    private WebsiteRepositoryInterface $websiteRepository;


    public function __construct(
        FileWriterFactory $fileWriterFactory,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->fileWriterFactory = $fileWriterFactory;
        $this->websiteRepository = $websiteRepository;
    }

    public function get(StoreInterface $store, $vendor): FileWriter
    {
        //$website = $this->websiteRepository->getById($store->getWebsiteId());
        //$fileName = sprintf(self::FILE_NAME_PATTERN, $website->getCode(), $store->getCode());
        $fileName = sprintf(self::FILE_NAME_PATTERN, $store->getCode(), $vendor);
        $destination = self::DIRECTORY_PATH . DIRECTORY_SEPARATOR . $fileName;

        $fileWriter = $this->fileWriterFactory->create();
        $fileWriter->setDestination($destination);

        return $fileWriter;
    }
}
