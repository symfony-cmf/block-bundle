<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


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
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('phpcr')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->scalarNode('block_basepath')->defaultValue('/cms/content')->end()
                                ->scalarNode('manager_name')->defaultNull()->end()
                                ->scalarNode('string_document_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock')->end()
                                ->scalarNode('simple_document_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock')->end()
                                ->scalarNode('container_document_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ContainerBlock')->end()
                                ->scalarNode('reference_document_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock')->end()
                                ->scalarNode('action_document_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock')->end()
                                ->scalarNode('slideshow_document_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SlideshowBlock')->end()
                                ->scalarNode('imagine_document_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ImagineBlock')->end()

                                ->enumNode('use_sonata_admin')
                                    ->values(array(true, false, 'auto'))
                                    ->defaultValue('auto')
                                ->end()
                                ->scalarNode('string_admin_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Admin\StringBlockAdmin')->end()
                                ->scalarNode('simple_admin_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Admin\SimpleBlockAdmin')->end()
                                ->scalarNode('container_admin_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Admin\ContainerBlockAdmin')->end()
                                ->scalarNode('reference_admin_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Admin\ReferenceBlockAdmin')->end()
                                ->scalarNode('action_admin_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Admin\ActionBlockAdmin')->end()
                                ->scalarNode('slideshow_admin_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Admin\Imagine\SlideshowBlockAdmin')->end()
                                ->scalarNode('imagine_admin_class')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Admin\Imagine\ImagineBlockAdmin')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cmf_embed_blocks')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('prefix')->defaultValue('%embed-block|')->end()
                                ->scalarNode('postfix')->defaultValue('|end%')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->enumNode('use_imagine')
                    ->values(array(true, false, 'auto'))
                    ->defaultValue('auto')
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
