<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CmfBlockExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all Bundles
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['SonataBlockBundle'])) {
            $config = array(
                'templates' => array(
                    'block_base' => 'CmfBlockBundle:Block:block_base.html.twig',
                ),
                'blocks_by_class' => array(
                    0 => array(
                        'class'     => "Symfony\\Cmf\\Bundle\\BlockBundle\\Doctrine\\Phpcr\\RssBlock",
                        'settings'  => array(
                            'title'     => 'Insert the rss title',
                            'url'       => false,
                            'maxItems'  => 10,
                            'template'  => 'CmfBlockBundle:Block:block_rss.html.twig',
                            'itemClass' => 'Symfony\\Cmf\\Bundle\\BlockBundle\\Model\\FeedItem',
                        ),
                    ),
                ),
            );
            $container->prependExtensionConfig('sonata_block', $config);
        }
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter($this->getAlias() . '.twig.cmf_embed_blocks.prefix', $config['twig']['cmf_embed_blocks']['prefix']);
        $container->setParameter($this->getAlias() . '.twig.cmf_embed_blocks.postfix', $config['twig']['cmf_embed_blocks']['postfix']);

        // detect bundles
        $bundles = $container->getParameter('kernel.bundles');
        if (true === $config['use_imagine'] ||
            ('auto' === $config['use_imagine'] && isset($bundles['LiipImagineBundle']))
        ) {
            $useImagine = true;
        } else {
            $useImagine = false;
        }

        // load config
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container, $useImagine);
        }

        if ($useImagine) {
            $loader->load('imagine.xml');
        }

        $this->loadSonataCache($config, $loader, $container);
    }

    public function loadPhpcr($config, XmlFileLoader $loader, ContainerBuilder $container, $useImagine)
    {
        $container->setParameter($this->getAlias() . '.backend_type_phpcr', true);

        $keys = array(
            'string_document_class' => 'string_document.class',
            'simple_document_class' => 'simple_document.class',
            'container_document_class' => 'container_document.class',
            'reference_document_class' => 'reference_document.class',
            'menu_document_class' => 'menu_document.class',
            'action_document_class' => 'action_document.class',
            'imagine_document_class' => 'imagine_document.class',
            'slideshow_document_class' => 'slideshow_document.class',
            'string_admin_class' => 'string_admin.class',
            'simple_admin_class' => 'simple_admin.class',
            'container_admin_class' => 'container_admin.class',
            'reference_admin_class' => 'reference_admin.class',
            'menu_admin_class' => 'menu_admin.class',
            'action_admin_class' => 'action_admin.class',
            'imagine_admin_class' => 'imagine_admin.class',
            'slideshow_admin_class' => 'slideshow_admin.class',
            'block_basepath' => 'block_basepath',
            'manager_name' => 'manager_name',
        );

        foreach ($keys as $sourceKey => $targetKey) {
            if (isset($config[$sourceKey])) {
                $container->setParameter(
                    $this->getAlias() . '.persistence.phpcr.'. $targetKey,
                    $config[$sourceKey]
                );
            }
        }

        $loader->load('persistence-phpcr.xml');

        if ($config['use_sonata_admin']) {
            $this->loadSonataPhpcrAdmin($config, $loader, $container, $useImagine);
        }

        $blockLoader = $container->getDefinition('cmf.block.service');
        $blockLoader->replaceArgument(0, new Reference('doctrine_phpcr'));
        $blockLoader->addMethodCall('setManagerName', array('%cmf_block.persistence.phpcr.manager_name%'));

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['CmfCreateBundle'])) {
            $blockLoader = $container->getDefinition('cmf.block.simple');
            $blockLoader->addMethodCall('setTemplate', array('CmfBlockBundle:Block:block_simple_createphp.html.twig'));
            $blockLoader = $container->getDefinition('cmf.block.string');
            $blockLoader->addMethodCall('setTemplate', array('CmfBlockBundle:Block:block_string_createphp.html.twig'));

        }

        if (isset($bundles['CmfMenuBundle'])) {
            $loader->load('menu.xml');
        }
    }

    public function loadSonataPhpcrAdmin($config, XmlFileLoader $loader, ContainerBuilder $container, $useImagine = false, $prefix = '')
    {
        $bundles = $container->getParameter('kernel.bundles');
        if ('auto' === $config['use_sonata_admin'] && !isset($bundles['SonataDoctrinePHPCRAdminBundle'])) {
            return;
        }

        $loader->load('admin.xml');

        if ($useImagine) {
            $loader->load('admin-imagine.xml');
        }

        if (isset($bundles['CmfMenuBundle'])) {
            $loader->load('admin-menu.xml');
        }
    }

    public function loadSonataCache($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (!isset($bundles['SonataCacheBundle'])) {
            return;
        }

        $loader->load('cache.xml');

        if (isset($config['caches']['varnish'])) {
            $container
                ->getDefinition('cmf.block.cache.varnish')
                ->replaceArgument(0, $config['caches']['varnish']['token'])
                ->replaceArgument(6, $config['caches']['varnish']['servers'])
                ->replaceArgument(7, 3 === $config['caches']['varnish']['version'] ? 'ban' : 'purge');
            ;
        } else {
            $container->removeDefinition('cmf.block.cache.varnish');
        }

        if (isset($config['caches']['ssi'])) {
            $container
                ->getDefinition('cmf.block.cache.ssi')
                ->replaceArgument(0, $config['caches']['ssi']['token'])
            ;
        } else {
            $container->removeDefinition('cmf.block.cache.ssi');
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/block';
    }
}
