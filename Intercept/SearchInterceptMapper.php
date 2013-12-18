<?php

namespace Markup\NeedleBundle\Intercept;

/**
* A search intercept mapper that can collect mappers set on it against corpora and delegate through.
*/
class SearchInterceptMapper implements TypedInterceptMapperInterface
{
    const TYPE_SEARCH = 'search';

    /**
     * A list of registered mappers keyed by corpus.
     *
     * @var array
     **/
    private $mappers = array();

    /**
     * {@inheritdoc}
     **/
    public function mapDefinitionToIntercept(DefinitionInterface $definition)
    {
        $properties = $definition->getProperties();
        if (!isset($properties['corpus'])) {
            throw new UnresolvedInterceptException('Definition did not specify corpus, so cannot match mapper.');
        }
        $corpus = $properties['corpus'];
        if (!isset($this->mappers[$corpus])) {
            throw new UnresolvedInterceptException(sprintf('The specified corpus "%s" did not have a corresponding mapper defined.', $corpus));
        }
        $mapper = $this->mappers[$corpus];

        return $mapper->mapDefinitionToIntercept($definition);
    }

    /**
     * Adds a search intercept mapper against a given corpus.
     *
     * @param  string                        $corpus The search corpus this mapper refers to.
     * @param  TypedInterceptMapperInterface $mapper
     * @return self
     **/
    public function addSearchInterceptMapper($corpus, TypedInterceptMapperInterface $mapper)
    {
        if ($mapper->getType() !== self::TYPE_SEARCH) {
            throw new \InvalidArgumentException(sprintf('A mapper with the type "%s" was attempted to be added to the search intercept mapper, expected search type.', $mapper->getType()));
        }
        $this->mappers[$corpus] = $mapper;

        return $this;
    }

    /**
     * {@inheritdoc}
     **/
    public function getType()
    {
        return self::TYPE_SEARCH;
    }
}
