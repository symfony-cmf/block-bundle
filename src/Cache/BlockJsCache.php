<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Cache;

use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\Cache\CacheAdapterInterface;
use Sonata\Cache\CacheElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Cache a block through Javascript code.
 */
class BlockJsCache implements CacheAdapterInterface
{
    protected $router;
    protected $blockRenderer;
    protected $blockLoader;
    protected $blockContextManager;
    protected $sync;

    /**
     * @param RouterInterface              $router
     * @param BlockRendererInterface       $blockRenderer
     * @param BlockLoaderInterface         $blockLoader
     * @param BlockContextManagerInterface $blockContextManager
     * @param bool                         $sync
     */
    public function __construct(
        RouterInterface $router,
        BlockRendererInterface $blockRenderer,
        BlockLoaderInterface $blockLoader,
        BlockContextManagerInterface $blockContextManager,
        $sync = false
    ) {
        $this->router = $router;
        $this->blockRenderer = $blockRenderer;
        $this->blockLoader = $blockLoader;
        $this->blockContextManager = $blockContextManager;
        $this->sync = $sync;
    }

    /**
     * {@inheritdoc}
     */
    public function flushAll()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flush(array $keys = [])
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function has(array $keys)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $keys)
    {
        $this->validateKeys($keys);

        return new CacheElement($keys, new Response($this->sync ? $this->getSync($keys) : $this->getAsync($keys)));
    }

    /**
     * @throws \RuntimeException
     *
     * @param array $keys
     */
    private function validateKeys(array $keys)
    {
        foreach (['block_id', 'updated_at'] as $key) {
            if (!isset($keys[$key])) {
                throw new \RuntimeException(sprintf('Please define a `%s` key', $key));
            }
        }
    }

    /**
     * @param array $keys
     *
     * @return string
     */
    protected function getSync(array $keys)
    {
        $dashifiedId = $this->dashify($keys['block_id']);

        return sprintf(<<<'CONTENT'
<div id="block%s" >
    <script type="text/javascript">
        /*<![CDATA[*/
            (function () {
                var block, xhr, node, parentNode, replace;
                block = document.getElementById('block%s');
                parentNode = block.parentNode;
                if (window.XMLHttpRequest) {
                    xhr = new XMLHttpRequest();
                } else {
                    xhr = new ActiveXObject('Microsoft.XMLHTTP');
                }

                xhr.open('GET', '%s', false);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send('');

                // create an empty element
                var div = document.createElement("div");
                div.innerHTML = xhr.responseText;

                replace = true;
                for (var node in div.childNodes) {
                    if (div.childNodes[node] && div.childNodes[node].nodeType == 1) {
                        if (replace) {
                            parentNode.replaceChild(div.childNodes[node], block);
                            replace = false;
                        } else {
                            parentNode.appendChild(div.childNodes[node]);
                        }
                    }
                }
            })();
        /*]]>*/
    </script>
</div>
CONTENT
, $dashifiedId, $dashifiedId, $this->router->generate('cmf_block_js_sync_cache', $keys, true));
    }

    /**
     * @param array $keys
     *
     * @return string
     */
    protected function getAsync(array $keys)
    {
        return sprintf(<<<'CONTENT'
<div id="block%s" >
    <script type="text/javascript">
        /*<![CDATA[*/

            (function () {
                var b = document.createElement('script');
                b.type = 'text/javascript';
                b.async = true;
                b.src = '%s'
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(b, s);
            })();

        /*]]>*/
    </script>
</div>
CONTENT
, $this->dashify($keys['block_id']), $this->router->generate('cmf_block_js_async_cache', $keys, true));
    }

    /**
     * {@inheritdoc}
     */
    public function set(array $keys, $data, $ttl = 84600, array $contextualKeys = [])
    {
        $this->validateKeys($keys);

        return new CacheElement($keys, $data, $ttl, $contextualKeys);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cacheAction(Request $request)
    {
        $block = $this->blockLoader->load(['name' => $request->get('block_id')]);

        if (!$block) {
            return new Response('', 404);
        }

        $settings = $request->get(BlockContextManagerInterface::CACHE_KEY, []);

        if (!is_array($settings)) {
            throw new \RuntimeException(sprintf(
                'Query string parameter `%s` is not an array',
                BlockContextManagerInterface::CACHE_KEY
            ));
        }

        $response = $this->blockRenderer->render(
            $this->blockContextManager->get($block, $settings)
        );
        $response->setPrivate(); //  always set to private

        if ($this->sync) {
            return $response;
        }

        $response->setContent(sprintf(<<<'JS'
    (function () {
        var block = document.getElementById('block%s'),
            div = document.createElement("div"),
            parentNode = block.parentNode,
            node,
            replace = true;

        div.innerHTML = %s;

        for (var node in div.childNodes) {
            if (div.childNodes[node] && div.childNodes[node].nodeType == 1) {
                if (replace) {
                    parentNode.replaceChild(div.childNodes[node], block);
                    replace = false;
                } else {
                    parentNode.appendChild(div.childNodes[node]);
                }
            }
        }
    })();
JS
, $this->dashify($block->getId()), json_encode($response->getContent())));

        $response->headers->set('Content-Type', 'application/javascript');

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function isContextual()
    {
        return false;
    }

    /**
     * @param string $src
     *
     * @return mixed
     */
    protected function dashify($src)
    {
        return preg_replace('/[\/\.]/', '-', $src);
    }
}
