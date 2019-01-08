<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

class CorpusIndexingCommandFactory
{
    /**
     * @var callable
     */
    private $serviceClosure;

    public function __construct(callable $serviceClosure)
    {
        $this->serviceClosure = $serviceClosure;
    }

    public function create()
    {
        return ($this->serviceClosure)();
    }
}
