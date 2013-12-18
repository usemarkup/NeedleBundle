<?php

namespace Markup\NeedleBundle\Facet;

use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
* A facet that uses a translator to provide its display name.
*/
class TranslatedFacet implements FacetInterface
{
    /**
     * The name for the facet.
     *
     * @var string
     **/
    private $name;

    /**
     * A translator.
     *
     * @var Translator
     **/
    private $translator;

    /**
     * A namespace for a translation key.
     *
     * @var string
     **/
    private $translationNamespace;

    /**
     * A translator message domain.
     *
     * @var string
     **/
    private $messageDomain;

    /**
     * A search key for the facet.
     *
     * @var string
     **/
    private $searchKey = null;

    /**
     * @param string     $name
     * @param Translator $translator
     * @param string     $translation_namespace
     * @param string     $message_domain
     * @param string     $search_key
     **/
    public function __construct($name, Translator $translator, $translation_namespace, $message_domain = null, $search_key = null)
    {
        $this->name = $name;
        $this->translator = $translator;
        $this->translationNamespace = $translation_namespace;
        $this->messageDomain = $message_domain;
        $this->searchKey = $search_key;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        if (!$this->hasMessageDomain()) {
            return $this->getTranslator()->trans($this->getTranslatorKey());
        } else {
            return $this->getTranslator()->trans($this->getTranslatorKey(), array(), $this->getMessageDomain());
        }
    }

    public function getSearchKey()
    {
        if (null === $this->searchKey) {
            return $this->getName();
        }

        return $this->searchKey;
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }

    /**
     * @return Translator
     **/
    private function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return string
     **/
    private function getTranslationNamespace()
    {
        return $this->translationNamespace;
    }

    /**
     * Gets the translator message domain.  Returns false if message domain not set.
     *
     * @return string|bool
     **/
    private function getMessageDomain()
    {
        if (null === $this->messageDomain) {
            return false;
        }

        return $this->messageDomain;
    }

    /**
     * Gets whether this has an explicit translator message domain set.
     *
     * @return bool
     **/
    private function hasMessageDomain()
    {
        return null !== $this->messageDomain;
    }

    /**
     * Gets the translator key to use for the translation for this facet.
     *
     * @return string
     **/
    private function getTranslatorKey()
    {
        return implode('.', array_filter(array($this->getTranslationNamespace(), $this->getName())));
    }
}
