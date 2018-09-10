<?php

namespace Markup\NeedleBundle\Intercept;

use Markup\NeedleBundle\Event\SearchEvents;
use Markup\NeedleBundle\Event\UnresolvedInterceptEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
* An interceptor object which can provide intercepts for searches.
*/
class Interceptor implements InterceptorInterface
{
    /**
     * @var EventDispatcherInterface
     **/
    private $eventDispatcher;

    /**
     * @var \SplObjectStorage<DefinitionInterface>
     **/
    private $definitions;

    /**
     * @var TypedInterceptMapperInterface[]
     **/
    private $interceptMappers = [];

    /**
     * @param EventDispatcherInterface $eventDispatcher
     **/
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->definitions = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     **/
    public function matchQueryToIntercept($queryString)
    {
        foreach ($this->definitions as $definition) {
            if ($definition->getMatcher()->matches($queryString)) {
                $interceptMapper = $this->getInterceptMapperForType($definition->getType());
                if (null === $interceptMapper) {
                    continue;
                }
                try {
                    $intercept = $interceptMapper->mapDefinitionToIntercept($definition);
                } catch (UnresolvedInterceptException $e) {
                    $event = new UnresolvedInterceptEvent($definition, $queryString, $e->getMessage());
                    $this->eventDispatcher->dispatch(SearchEvents::UNRESOLVED_INTERCEPT, $event);
                    continue;
                }

                return $intercept;
            }
        }
    }

    /**
     * Adds an intercept definition.
     *
     * @param  DefinitionInterface $definition
     * @return self
     **/
    public function addDefinition(DefinitionInterface $definition)
    {
        $this->definitions->attach($definition);

        return $this;
    }

    /**
     * Adds a mapper object to map a definition to a specific intercept.
     *
     * @param  TypedInterceptMapperInterface $interceptMapper
     * @return self
     **/
    public function addInterceptMapper(TypedInterceptMapperInterface $interceptMapper)
    {
        $this->interceptMappers[$interceptMapper->getType()] = $interceptMapper;

        return $this;
    }

    /**
     * Gets the intercept mapper for the provided type, if available.
     *
     * @param  string                             $type
     * @return TypedInterceptMapperInterface|null
     **/
    private function getInterceptMapperForType($type)
    {
        if (!isset($this->interceptMappers[$type])) {
            return null;
        }

        return $this->interceptMappers[$type];
    }
}
