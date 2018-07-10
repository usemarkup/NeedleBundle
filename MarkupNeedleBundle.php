<?php

namespace Markup\NeedleBundle;

use Markup\NeedleBundle\DependencyInjection\Compiler\AddCorporaPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddFacetValueCanonicalizersPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddIndexSchedulingEventsPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddSolariumPluginsPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddSpecializationContextFiltersPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddSuggestersPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddTermsPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\RegisterSearchInterceptMappersPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\RegisterSubjectDataMappersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarkupNeedleBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddCorporaPass());
        $container->addCompilerPass(new AddFacetValueCanonicalizersPass());
        $container->addCompilerPass(new AddIndexSchedulingEventsPass());
        $container->addCompilerPass(new RegisterSubjectDataMappersPass());
        $container->addCompilerPass(new RegisterSearchInterceptMappersPass());
        $container->addCompilerPass(new AddSolariumPluginsPass());
        $container->addCompilerPass(new AddSuggestersPass());
        $container->addCompilerPass(new AddTermsPass());
        $container->addCompilerPass(new AddSpecializationContextFiltersPass());
    }
}
