<?php

namespace Symfony\Cmf\Bundle\BlockBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add bundle settings to the BlockContextManager
 */
class BundleSettingsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sonata.block.context_manager')) {
            return;
        }

        $contextManagerDefinition = $container->findDefinition('sonata.block.context_manager');

        // add rss bundle configuration
        $contextManagerDefinition->addMethodCall('addBundleSettingsByClass', array(
            "Symfony\\Cmf\\Bundle\\BlockBundle\\Document\\RssBlock",
            array(
                'title'     => 'Insert the rss title',
                'url'       => false,
                'maxItems'  => 10,
                'template'  => 'SymfonyCmfBlockBundle:Block:block_rss.html.twig',
                'itemClass' => 'Symfony\Cmf\Bundle\BlockBundle\Model\FeedItem',
            )
        ));
    }
}
