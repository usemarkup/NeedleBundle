<?php

namespace Markup\NeedleBundle\Synonyms;

use Markup\NeedleBundle\Client\SolrManagedSynonymsClient;
use Markup\NeedleBundle\Event\CorpusSynonymsUpdatedEvent;
use Markup\NeedleBundle\Event\SearchEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SolrSynonymClient implements SynonymClientInterface
{
    /**
     * @var SolrManagedSynonymsClient
     */
    private $client;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        SolrManagedSynonymsClient $solrManagedSynonymsClient,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->client = $solrManagedSynonymsClient;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return string[]
     */
    public function getStoredLocales(): array
    {
        return $this->client->fetchResourceIds();
    }

    /**
     * @return array
     */
    public function getSynonyms(string $locale): array
    {
        return $this->client->fetchData($locale);
    }

    /**
     * @param string $locale
     * @param array  $data
     * @return bool
     */
    public function updateSynonyms(string $locale, array $data): bool
    {
        $existingTerms = array_keys($this->getSynonyms($locale));
        $termsToRemove = array_diff_key($existingTerms, $data);

        foreach ($termsToRemove as $term) {
            $this->client->delete($locale, $term);
        }

        //filter out any empty values
        $data = array_map(
            function ($termList) {
                if (!is_array($termList)) {
                    return [];
                }

                return array_values(array_filter($termList));
            },
            $data
        );

        $response = $this->client->add($locale, $data);
        $wasSuccess = $response->getStatusCode() === 200;

        if ($wasSuccess) {
            $this->eventDispatcher->dispatch(
                SearchEvents::CORPUS_SYNONYMS_UPDATED,
                new CorpusSynonymsUpdatedEvent($locale)
            );
        }

        return $wasSuccess;
    }
}
