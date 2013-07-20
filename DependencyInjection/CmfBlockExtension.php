<?php
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

        $container->setParameter($this->getAlias() . '.content_basepath', $config['content_basepath']);
        $container->setParameter($this->getAlias() . '.block_basepath', $config['block_basepath']);
        $container->setParameter($this->getAlias() . '.twig.cmf_embed_blocks.prefix', $config['twig']['cmf_embed_blocks']['prefix']);
        $container->setParameter($this->getAlias() . '.twig.cmf_embed_blocks.postfix', $config['twig']['cmf_embed_blocks']['postfix']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($config['use_sonata_admin']) {
            $this->loadSonataAdmin($config, $loader, $container);
        }

        $this->loadSonataCache($config, $loader, $container);

        if (isset($config['multilang'])) {
            if ($config['multilang']['use_sonata_admin']) {
                $this->loadSonataAdmin($config['multilang'], $loader, $container, 'multilang.');
            }
            if (isset($config['multilang']['simple_document_class'])) {
                $container->setParameter($this->getAlias() . '.multilang.document_class', $config['multilang']['simple_document_class']);
            }

            $container->setParameter($this->getAlias() . '.multilang.locales', $config['multilang']['locales']);
        }

        if ($config['imagine']) {
            $loader->load('imagine.xml');
        }

        if (isset($config['simple_document_class'])) {
            $container->setParameter($this->getAlias() . '.simple_document_class', $config['simple_document_class']);
        }

        if (isset($config['container_document_class'])) {
            $container->setParameter($this->getAlias() . '.container_document_class', $config['container_document_class']);
        }

        if (isset($config['container_admin_class'])) {
            $container->setParameter($this->getAlias() . '.' . 'container_admin_class', $config['container_admin_class']);
        }

        if (isset($config['reference_document_class'])) {
            $container->setParameter($this->getAlias() . '.reference_document_class', $config['reference_document_class']);
        }

        if (isset($config['reference_admin_class'])) {
            $container->setParameter($this->getAlias() . '.' . 'reference_admin_class', $config['reference_admin_class']);
        }

        if (isset($config['action_document_class'])) {
            $container->setParameter($this->getAlias() . '.action_document_class', $config['action_document_class']);
        }

        if (isset($config['action_admin_class'])) {
            $container->setParameter($this->getAlias() . '.' . 'action_admin_class', $config['action_admin_class']);
        }

        $blockLoader = $container->getDefinition('cmf.block.service');
        $blockLoader->replaceArgument(1, new Reference($config['manager_registry']));
        $blockLoader->addMethodCall('setManagerName', array($config['manager_name']));

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['CmfCreateBundle'])) {
            $blockLoader = $container->getDefinition('cmf.block.simple');
            $blockLoader->addMethodCall('setTemplate', array('CmfBlockBundle:Block:block_simple_createphp.html.twig'));
        }
    }

    public function loadSonataAdmin($config, XmlFileLoader $loader, ContainerBuilder $container, $prefix = '')
    {
        $bundles = $container->getParameter('kernel.bundles');
        if ('auto' === $config['use_sonata_admin'] && !isset($bundles['SonataDoctrinePHPCRAdminBundle'])) {
            return;
        }

        $loader->load($prefix . 'admin.xml');
        $loader->load('container.admin.xml');
        $loader->load('reference.admin.xml');
        $loader->load('action.admin.xml');
        $loader->load('string.admin.xml');

        if (isset($config['simple_admin_class'])) {
            $container->setParameter($this->getAlias() . '.' . $prefix . 'simple_admin_class', $config['simple_admin_class']);
        }

        if (isset($config['imagine']) && $config['imagine']) {
            $loader->load('imagine.admin.xml');
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
