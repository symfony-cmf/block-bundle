<?php
namespace Symfony\Cmf\Bundle\BlockBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class SymfonyCmfBlockExtension extends Extension
{
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

        $blockLoader = $container->getDefinition('symfony_cmf.block.service');
        $blockLoader->addMethodCall('setDocumentManager', array($config['document_manager_name']));

        // TODO: Symfony 2.1 compatibility
        if (!class_exists('Symfony\Component\HttpKernel\Fragment\FragmentHandler')) {
            $blockAction = $container->getDefinition('symfony_cmf.block.action');
            $blockAction->replaceArgument(2, new Reference('http_kernel'));
        }

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['SymfonyCmfCreateBundle'])) {
            $blockLoader = $container->getDefinition('symfony_cmf.block.simple');
            $blockLoader->addMethodCall('setTemplate', array('SymfonyCmfBlockBundle:Block:block_simple_createphp.html.twig'));
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
                ->getDefinition('symfony_cmf.block.cache.varnish')
                ->replaceArgument(0, $config['caches']['varnish']['token'])
                ->replaceArgument(4, $config['caches']['varnish']['servers'])
            ;
        } else {
            $container->removeDefinition('symfony_cmf.block.cache.varnish');
        }

        if (isset($config['caches']['ssi'])) {
            $container
                ->getDefinition('symfony_cmf.block.cache.ssi')
                ->replaceArgument(0, $config['caches']['ssi']['token'])
            ;
        } else {
            $container->removeDefinition('symfony_cmf.block.cache.ssi');
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
