<?php

namespace Markup\NeedleBundle\Intercept;

/**
* A matcher that takes a list of exact terms, though matched case-insensitively.
*/
class NormalizedListMatcher implements MatcherInterface
{
    /**
     * @var array
     **/
    private $list = array();

    /**
     * {@inheritdoc}
     **/
    public function matches($queryString)
    {
        $normalizedList = $this->getNormalizedList();

        return false !== array_search($this->normalizeString($queryString), $normalizedList);
    }

    /**
     * Sets a list of terms on the matcher.
     *
     * @param  array $list
     * @return self
     **/
    public function setList(array $list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Gets the list in a normal form.
     *
     * @return array
     **/
    private function getNormalizedList()
    {
        $normalized = array();
        foreach ($this->list as $term) {
            $normalized[] = $this->normalizeString($term);
        }

        return $normalized;
    }

    /**
     * Normalizes a string by lowercasing and removing accents/ letter variants.
     *
     * @param  string $str
     * @return string
     **/
    private function normalizeString($str)
    {
        return preg_replace('/\p{Mn}/u', '', \Normalizer::normalize(mb_strtolower($str, 'UTF-8'), \Normalizer::FORM_KD));
    }
}
