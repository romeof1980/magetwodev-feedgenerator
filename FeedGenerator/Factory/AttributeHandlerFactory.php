<?php

namespace MageTwoDev\FeedGenerator\Factory;

use Magento\Framework\ObjectManagerInterface;
use MageTwoDev\FeedGenerator\Data\AttributeConfigData;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\AttributeHandlerInterface;
use MageTwoDev\FeedGenerator\Exception\HandlerIsNotSpecifiedException;
use MageTwoDev\FeedGenerator\Exception\WrongInstanceException;

class AttributeHandlerFactory
{
    private ObjectManagerInterface $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @throws WrongInstanceException
     * @throws HandlerIsNotSpecifiedException
     */
    public function create(AttributeConfigData $attribute): AttributeHandlerInterface
    {
        if ($attribute->getAttributeHandler() === null) {
            throw new HandlerIsNotSpecifiedException(__('Handler should be specified for each attribute.'));
        }

        $handlerClass = $attribute->getAttributeHandler();
        $instance = $this->objectManager->create($handlerClass);

        if (!$instance instanceof AttributeHandlerInterface) {
            throw new WrongInstanceException(__('Class should implement AttributeHandlerInterface interface.'));
        }

        return $instance;
    }
}
