<?php

namespace MageTwoDev\FeedGenerator\DataProvider;

use MageTwoDev\FeedGenerator\Data\AttributeConfigData;
use MageTwoDev\FeedGenerator\DataProvider\AttributeHandlers\AttributeHandlerInterface;
use MageTwoDev\FeedGenerator\Exception\HandlerIsNotSpecifiedException;
use MageTwoDev\FeedGenerator\Exception\WrongInstanceException;
use MageTwoDev\FeedGenerator\Factory\AttributeHandlerFactory;

class AttributeHandlerProvider
{
    private AttributeHandlerFactory $factory;

    /**
     * @var AttributeHandlerInterface[]
     */
    private array $handlersPool = [];

    public function __construct(AttributeHandlerFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @throws HandlerIsNotSpecifiedException
     * @throws WrongInstanceException
     */
    public function get(AttributeConfigData $attribute): AttributeHandlerInterface
    {
        $name = $attribute->getFieldName();

        if (isset($this->handlersPool[$name])) {
            return $this->handlersPool[$name];
        }

        $handler = $this->factory->create($attribute);
        $this->handlersPool[$name] = $handler;

        return $handler;
    }
}
