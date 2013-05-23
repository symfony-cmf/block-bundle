<?php

namespace Symfony\Cmf\Bundle\BlockBundle;

use Symfony\Cmf\Bundle\BlockBundle\DependencyInjection\Compiler\BundleSettingsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SymfonyCmfBlockBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new BundleSettingsCompilerPass());
    }
}
