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
        $rootNode = $treeBuilder->root('cmf_block');

        $rootNode
            ->children()
                ->scalarNode('manager_registry')->defaultValue('doctrine_phpcr')->end()
                ->scalarNode('manager_name')->defaultValue('default')->end()
                ->scalarNode('content_basepath')->defaultValue('/cms/content')->end()
                ->scalarNode('block_basepath')->defaultValue('/cms/content')->end()
                ->scalarNode('imagine')->defaultValue(false)->end()
                ->scalarNode('simple_admin_class')->defaultNull()->end()
                ->scalarNode('simple_document_class')->defaultNull()->end()
                ->scalarNode('container_admin_class')->defaultNull()->end()
                ->scalarNode('container_document_class')->defaultNull()->end()
                ->scalarNode('reference_admin_class')->defaultNull()->end()
                ->scalarNode('reference_document_class')->defaultNull()->end()
                ->scalarNode('action_admin_class')->defaultNull()->end()
                ->scalarNode('action_document_class')->defaultNull()->end()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cmf_embed_blocks')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('prefix')->defaultValue('<span>%embed-block:')->end()
                                ->scalarNode('postfix')->defaultValue('%</span>')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->enumNode('use_sonata_admin')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
                ->end()
                ->arrayNode('multilang')
                    ->fixXmlConfig('locale')
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
                        ->arrayNode('varnish')
                            ->fixXmlConfig('server')
                            ->children()
                                ->scalarNode('token')->defaultValue(hash('sha256', uniqid(mt_rand(), true)))->end()
                                ->scalarNode('version')->defaultValue(2)->end()
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
