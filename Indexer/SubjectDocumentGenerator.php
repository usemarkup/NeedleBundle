<?php

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Exception\IllegalSubjectException;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

/**
* An object that can generate a Solarium document for a subject.
*/
class SubjectDocumentGenerator implements SubjectDocumentGeneratorInterface
{
    /**
     * @var SubjectDataMapperInterface
     **/
    private $subjectToDataMapper;

    /**
     * @var bool
     */
    private $allowNullValues;

    /**
     * @var UpdateQuery
     **/
    private $updateQuery = null;

    public function __construct(SubjectDataMapperInterface $subjectToDataMapper, bool $allowNullValues = true)
    {
        $this->subjectToDataMapper = $subjectToDataMapper;
        $this->allowNullValues = $allowNullValues;
    }

    public function createDocumentForSubject($subject)
    {
        try {
            $data = $this->getSubjectToDataMapper()->mapSubjectToData($subject);
        } catch (IllegalSubjectException $e) {
            return null;
        }
        if (!$this->allowNullValues && false !== array_search(null, $data, true)) {
            //pretty much forced to do this manually. thanks for nothing, php.
            $filteredData = [];
            foreach ($data as $key => $value) {
                if ($value !== null) {
                    $filteredData[$key] = $value;
                }
            }
            $data = $filteredData;
        }

        return $this->getUpdateQuery()->createDocument($data);
    }

    public function setUpdateQuery(UpdateQuery $updateQuery)
    {
        $this->updateQuery = $updateQuery;
    }

    /**
     * @return UpdateQuery
     * @throws \RuntimeException if update query not previously set.
     **/
    private function getUpdateQuery()
    {
        if (null === $this->updateQuery) {
            throw new \RuntimeException(sprintf('You need to set an update query on an instance of %s before using it.', __CLASS__));
        }

        return $this->updateQuery;
    }

    /**
     * @return SubjectDataMapperInterface
     **/
    private function getSubjectToDataMapper()
    {
        return $this->subjectToDataMapper;
    }
}
