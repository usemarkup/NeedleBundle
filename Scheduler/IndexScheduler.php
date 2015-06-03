<?php

namespace Markup\NeedleBundle\Scheduler;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Entity\ScheduledIndex;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;

class IndexScheduler
{
    private $em;

    /**
     * @param EntityManager $em
     **/
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Adds a new record to the scheduler if one doesn't already exist.
     *
     * @param CorpusInterface|string $corpus
     */
    public function addToSchedule($corpus)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        if (!$this->getScheduledExports($corpus)) {
            $s = new ScheduledIndex($corpus);
            $this->em->persist($s);
            $this->em->flush();

            return true;
        }

        return false;
    }

    /**
     * @deprecated/@todo - this breaks SRP, as there shouldn't be a need to manually update status - rather use a service that executes scheduled indexes and transparently performs status updates
     **/
    public function updateStatus($record,    $status)
    {
        $this->em->clear();
        switch ($this->em->getUnitOfWork()->getEntityState($record)) {
            case UnitOfWork::STATE_MANAGED:
                break;
            case UnitOfWork::STATE_DETACHED:
                $record = $this->em->merge($record);
                break;
            default:
                throw new \Exception('This method must only be used to update existing entities (not new or removed)');
        }
        if (!$record->isValidStatus($status)) {
            throw new \Exception($status . ' is not a valid status');
        }
        $record->setStatus($status);
        $this->em->persist($record);
        $this->em->flush();
    }

    /**
     * @param CorpusInterface|string $corpus
     **/
    public function getScheduledExports($corpus)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        if ($r = $this->em->getRepository('MarkupNeedleBundle:ScheduledIndex')->findBy(array('status' => ScheduledIndex::SCHEDULED, 'corpus' => $corpus))) {
            return $r;
        } else {
            return false;
        }
    }

    /**
     * @param CorpusInterface|string $corpus
     **/
    public function getProcessingExports($corpus)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        if ($r = $this->em->getRepository('MarkupNeedleBundle:ScheduledIndex')->findBy(array('status' => ScheduledIndex::PROCESSING, 'corpus' => $corpus))) {
            return $r;
        } else {
            return false;
        }
    }
}
