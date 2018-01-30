<?php

namespace Markup\NeedleBundle\Scheduler;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\UnitOfWork;
use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Entity\ScheduledIndex;
use Psr\Log\LoggerInterface;

class IndexScheduler
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Number of days after which a 'processing' export is considered failed
     */
    const PROCESSING_EXPIRY = 90;

    public function __construct(
        ManagerRegistry $doctrine,
        LoggerInterface $logger
    ) {
        $this->doctrine = $doctrine;
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
            $entityManager = $this->getEntityManager();
            $entityManager->persist($s);
            $entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @deprecated/@todo - this breaks SRP, as there shouldn't be a need to manually update status - rather use a service that executes scheduled indexes and transparently performs status updates
     **/
    public function updateStatus($record,    $status)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->clear();
        switch ($entityManager->getUnitOfWork()->getEntityState($record)) {
            case UnitOfWork::STATE_MANAGED:
                break;
            case UnitOfWork::STATE_DETACHED:
                $record = $entityManager->merge($record);
                break;
            default:
                throw new \Exception('This method must only be used to update existing entities (not new or removed)');
        }
        if (!$record->isValidStatus($status)) {
            throw new \Exception($status.' is not a valid status');
        }
        $record->setStatus($status);
        $entityManager->persist($record);
        $entityManager->flush();
    }

    /**
     * @param CorpusInterface|string $corpus
     **/
    public function getScheduledExports($corpus)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        if ($r = $this->getScheduledIndexRepository()->findBy(['status' => ScheduledIndex::SCHEDULED, 'corpus' => $corpus])) {
            return $r;
        } else {
            return [];
        }
    }

    /**
     * @param CorpusInterface|string $corpus
     **/
    public function getProcessingExports($corpus)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        if ($r = $this->getScheduledIndexRepository()->findBy(['status' => ScheduledIndex::PROCESSING, 'corpus' => $corpus])) {
            return $r;
        } else {
            return false;
        }
    }

    /**
     * @param CorpusInterface|string $corpus Changes 'processing' exports to failed after the expiry period has elapsed
     **/
    public function failExpiredProcessingExports($corpus)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        $qb = $this->getScheduledIndexRepository()->createQueryBuilder('i');

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
        $this->getEntityManager()->flush();
        $this->logger->error(sprintf('Scheduled index contained a processing entry older than %s minutes. This was set to failed. Please investigate why the indexing process is taking too long or failing without moving to `failed` status.', self::PROCESSING_EXPIRY));

        return;
    }

    private function getEntityManager()
    {
        return $this->doctrine->getManager();
    }

    private function getScheduledIndexRepository()
    {
        return $this->doctrine->getRepository(ScheduledIndex::class);
    }
}
