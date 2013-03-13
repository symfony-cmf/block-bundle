<?php

namespace Symfony\Cmf\Bundle\BlockBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('symfony_cmf_block');

        $rootNode
            ->children()
                ->scalarNode('document_manager_name')->defaultValue('default')->end()
                ->scalarNode('content_basepath')->defaultValue('/cms/content')->end()
                ->scalarNode('block_basepath')->defaultValue('/cms/content')->end()
                ->scalarNode('slideshow')->defaultValue(false)->end()
                ->scalarNode('simple_admin_class')->defaultNull()->end()
                ->scalarNode('simple_document_class')->defaultNull()->end()
                ->scalarNode('container_admin_class')->defaultNull()->end()
                ->scalarNode('container_document_class')->defaultNull()->end()
                ->scalarNode('reference_admin_class')->defaultNull()->end()
                ->scalarNode('reference_document_class')->defaultNull()->end()
                ->scalarNode('action_admin_class')->defaultNull()->end()
                ->scalarNode('action_document_class')->defaultNull()->end()
                ->enumNode('use_sonata_admin')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()
                ->arrayNode('multilang')
                    ->children()
                        ->scalarNode('simple_admin_class')->defaultNull()->end()
                        ->scalarNode('simple_document_class')->defaultNull()->end()
                        ->enumNode('use_sonata_admin')
                            ->values(array(true, false, 'auto'))
                            ->defaultValue('auto')
                        ->end()
                        ->arrayNode('locales')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('caches')
                    ->children()
                        ->arrayNode('esi')
                            ->children()
                                ->scalarNode('token')->defaultValue(hash('sha256', uniqid(mt_rand(), true)))->end()
                                ->arrayNode('servers')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('ssi')
                            ->children()
                                ->scalarNode('token')->defaultValue(hash('sha256', uniqid(mt_rand(), true)))->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
