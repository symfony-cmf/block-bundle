<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Cache;

use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\CacheBundle\Adapter\SsiCache;
use Sonata\Cache\CacheElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Cache block through an ssi statement.
 */
class BlockSsiCache extends SsiCache
{
    protected $blockRenderer;
    protected $blockLoader;
    protected $blockContextManager;

    /**
     * @param string                       $token
     * @param RouterInterface              $router
     * @param BlockRendererInterface       $blockRenderer
     * @param BlockLoaderInterface         $blockLoader
     * @param BlockContextManagerInterface $blockContextManager
     */
    public function __construct(
        $token,
        RouterInterface $router,
        BlockRendererInterface $blockRenderer,
        BlockLoaderInterface $blockLoader,
        BlockContextManagerInterface $blockContextManager
    ) {
        parent::__construct($token, $router, null);

        $this->blockRenderer = $blockRenderer;
        $this->blockLoader = $blockLoader;
        $this->blockContextManager = $blockContextManager;
    }

    /**
     * @throws \RuntimeException
     *
     * @param array $keys
     */
    private function validateKeys(array $keys)
    {
        foreach (array('block_id', 'updated_at') as $key) {
            if (!isset($keys[$key])) {
                throw new \RuntimeException(sprintf('Please define a `%s` key', $key));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $keys)
    {
        $this->validateKeys($keys);

        $keys['_token'] = $this->computeHash($keys);

        $content = sprintf('<!--# include virtual="%s" -->', $this->router->generate('cmf_block_cache_ssi', $keys, false));

        return new CacheElement($keys, new Response($content));
    }

    /**
     * {@inheritdoc}
     */
    public function set(array $keys, $data, $ttl = 84600, array $contextualKeys = array())
    {
        $this->validateKeys($keys);

        return new CacheElement($keys, $data, $ttl, $contextualKeys);
    }

    /**
     * @param array $keys
     *
     * @return string
     */
    protected function computeHash(array $keys)
    {
        // values are casted into string for non numeric id
        return hash('sha256', $this->token.serialize(array(
            'block_id' => (string) $keys['block_id'],
            'updated_at' => (string) $keys['updated_at'],
        )));
    }

    /**
     * @param Request $request
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     *
     * @return mixed
     */
    public function cacheAction(Request $request)
    {
        $parameters = array_merge($request->query->all(), $request->attributes->all());

        if ($request->get('_token') != $this->computeHash($parameters)) {
            throw new AccessDeniedHttpException('Invalid token');
        }

        $block = $this->blockLoader->load(array('name' => $request->get('block_id')));

        if (!$block) {
            throw new NotFoundHttpException(sprintf('Block not found : %s', $request->get('block_id')));
        }

        $settings = $request->get(BlockContextManagerInterface::CACHE_KEY, array());

        if (!is_array($settings)) {
            throw new \RuntimeException(sprintf(
                'Query string parameter `%s` is not an array',
                BlockContextManagerInterface::CACHE_KEY
            ));
        }

        return $this->blockRenderer->render(
            $this->blockContextManager->get($block, $settings)
        );
    }
}
