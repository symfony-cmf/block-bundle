<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Cache;

use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\CacheBundle\Adapter\VarnishCache;
use Sonata\CacheBundle\Cache\CacheElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Cache block through varnish via an esi statement
 */
class BlockVarnishCache extends VarnishCache
{
    /**
     * @var BlockRendererInterface
     */
    protected $blockRenderer;

    /**
     * @var BlockLoaderInterface
     */
    protected $blockLoader;

    /**
     * @var BlockContextManagerInterface
     */
    protected $blockContextManager;

    /**
     * Constructor
     *
     * @param string                        $token                  A token
     * @param RouterInterface               $router                 A router instance
     * @param BlockRendererInterface        $blockRenderer          A block renderer instance
     * @param BlockLoaderInterface          $blockLoader            A block loader instance
     * @param BlockContextManagerInterface  $blockContextManager    A block context manager instance
     * @param array                         $servers                An array of servers
     * @param string                        $purgeInstruction       The purge instruction (purge in Varnish 2, ban in Varnish 3)
     */
    public function __construct(
        $token,
        RouterInterface $router,
        BlockRendererInterface $blockRenderer,
        BlockLoaderInterface $blockLoader,
        BlockContextManagerInterface $blockContextManager,
        array $servers = array(),
        $purgeInstruction
    ) {
        parent::__construct($token, $servers, $router, $purgeInstruction, null);

        $this->blockRenderer       = $blockRenderer;
        $this->blockLoader         = $blockLoader;
        $this->blockContextManager = $blockContextManager;
    }

    /**
     * @throws \RuntimeException
     *
     * @param array $keys
     *
     * @return void
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

        $content = sprintf('<esi:include src="%s" />', $this->router->generate('cmf_block_cache_esi', $keys, true));

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
            'block_id'   => (string)$keys['block_id'],
            'updated_at' => (string)$keys['updated_at'],
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