<?php

namespace Markup\NeedleBundle\Scheduler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Entity\ScheduledIndex;
use Psr\Log\LoggerInterface;

class IndexScheduler
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Number of days after which a 'processing' export is considered failed
     */
    const PROCESSING_EXPIRY = 90;

    /**
     * @param EntityManager   $em
     * @param LoggerInterface $logger
     **/
    public function __construct(
        EntityManager $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
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
            throw new \Exception($status.' is not a valid status');
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

    /**
     * @param Changes 'processing' exports to failed after the expiry period has elapsed
     **/
    public function failExpiredProcessingExports($corpus)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        $qb = $this->em->getRepository('MarkupNeedleBundle:ScheduledIndex')->createQueryBuilder('i');

        $added = new \DateTime('now');
        $added->sub(new \DateInterval('PT'.self::PROCESSING_EXPIRY.'M'));

        $qb->andWhere($qb->expr()->eq('i.status', ':status'))
            ->andWhere($qb->expr()->eq('i.corpus', ':corpus'))
            ->andWhere($qb->expr()->lte('i.added', ':added'));

        $qb->setParameter('status', ScheduledIndex::PROCESSING)
            ->setParameter('corpus', $corpus)
            ->setParameter('added', $added);

        $r = $qb->getQuery()->getResult();
        if (!$r) {
            return;
        }
        foreach ($r as $scheduled) {
            $scheduled->setStatus(ScheduledIndex::FAILED);
        }
        $this->em->flush();
        $this->logger->error(sprintf('Scheduled index contained a processing engtry older than %s minutes. This was set to failed. Please investigate why the indexing process is taking too long or failing without moving to `failed` status.', self::PROCESSING_EXPIRY));

        return;
    }
}
