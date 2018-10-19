<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
* A facet that uses a translator to provide its display name.
*/
class TranslatedFacet implements AttributeInterface
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
     * @var string|null
     **/
    private $messageDomain;

    /**
     * A search key for the facet.
     *
     * @var string|null
     **/
    private $searchKey = null;

    /**
     * @param string     $name
     * @param Translator $translator
     * @param string     $translationNamespace
     * @param string     $messageDomain
     * @param string     $searchKey
     **/
    public function __construct($name, Translator $translator, $translationNamespace, $messageDomain = null, $searchKey = null)
    {
        $this->name = $name;
        $this->translator = $translator;
        $this->translationNamespace = $translationNamespace;
        $this->messageDomain = $messageDomain;
        $this->searchKey = $searchKey;
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
            return $this->getTranslator()->trans($this->getTranslatorKey(), [], $this->getMessageDomain());
        }
    }

    public function getSearchKey(array $options = [])
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
     * Gets the translator message domain.  Returns null if message domain not set.
     *
     * @return string|null
     **/
    private function getMessageDomain()
    {
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
        return implode('.', array_filter([$this->getTranslationNamespace(), $this->getName()]));
    }
}
