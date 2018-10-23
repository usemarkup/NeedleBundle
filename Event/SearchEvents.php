<?php

namespace Markup\NeedleBundle\Event;

/**
* A set of names for search events.
*/
final class SearchEvents
{
    const UNRESOLVED_INTERCEPT = 'markup_needle.unresolved_intercept';

    const CORPUS_PRE_UPDATE = 'markup_needle.corpus_pre_update';
    const CORPUS_POST_UPDATE = 'markup_needle.corpus_post_update';

    const CORPUS_SYNONYMS_UPDATED = 'markup_needle.corpus_synonyms_updated';
}
