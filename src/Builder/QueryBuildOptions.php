<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Builder;

/**
 * An object encapsulating options that can be passed to a query builder.
 */
final class QueryBuildOptions
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options = [])
    {
        $defaultOptions = [
            'useWildcardSearch' => false,
        ];
        $this->options = array_merge($defaultOptions, $options);
    }

    public function useWildcardSearch(): bool
    {
        return (bool) $this->options['useWildcardSearch'];
    }
}
