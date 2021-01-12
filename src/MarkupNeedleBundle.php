<?php

namespace Markup\NeedleBundle;

use Markup\NeedleBundle\DependencyInjection\Compiler as c;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarkupNeedleBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new c\CreateServiceCollectionsPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);
        $container->addCompilerPass(new c\ConfigureServiceGeneratorsPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);
        $container->addCompilerPass(new c\BuildSynonymClientLocatorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 999);
        $container->addCompilerPass(new c\BuildSearchServiceLocatorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 999);
        $container->addCompilerPass(new c\BuildIndexingMessagerLocatorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 999);
        $container->addCompilerPass(new c\AddCorporaPass());
        $container->addCompilerPass(new c\AddFacetValueCanonicalizersPass());
        $container->addCompilerPass(new c\AddFacetSetDecoratorsPass());
        $container->addCompilerPass(new c\AddIndexSchedulingEventsPass());
        $container->addCompilerPass(new c\RegisterSubjectDataMappersPass());
        $container->addCompilerPass(new c\RegisterSearchInterceptMappersPass());
        $container->addCompilerPass(new c\BuildSuggestServiceLocatorPass());
        $container->addCompilerPass(new c\AddTermsPass());
        $container->addCompilerPass(new c\AddSpecializationContextFiltersPass());
    }
}
