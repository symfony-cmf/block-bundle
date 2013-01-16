<?php

/*
 * This Rss block is based on the example of the Sonata project.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * @see http://sonata-project.org/bundles/block/master/doc/reference/your_first_block.html
*/

namespace Symfony\Cmf\Bundle\BlockBundle\Block\Rss;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\RssBlockService as BaseRssBlockService;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Symfony\Cmf\Bundle\BlockBundle\Block\Rss\ReaderInterface;

class BlockService extends BaseRssBlockService
{
    protected $blockRenderer;
    protected $feedReader;

    /**
     * @param $name
     * @param \Symfony\Component\Templating\EngineInterface $templating
     */
    public function __construct($name, EngineInterface $templating, BlockRendererInterface $blockRenderer, ReaderInterface $feedReader)
    {
        parent::__construct($name, $templating);
        $this->blockRenderer = $blockRenderer;
        $this->feedReader = $feedReader;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        // Not used at the moment, editing using a frontend or backend UI could be changed here
        $form->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('url', 'url', array('required' => false)),
                array('title', 'text', array('required' => false)),
                array('maxItems', 'number', array('required' => false)),
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // Not used at the moment, validation for editing using a frontend or backend UI could be changed here
        $errorElement
            ->with('settings[url]')
                ->assertNotNull(array())
                ->assertNotBlank()
            ->end()
            ->with('settings[title]')
                ->assertNotNull(array())
                ->assertNotBlank()
                ->assertMaxLength(array('limit' => 50))
            ->end()
            ->with('settings[maxItems]')
                ->assertNotNull(array())
                ->assertNotBlank()
            ->end();
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return 'SymfonyCmfBlockBundle:Block:block_rss.html.twig';
    }

    /**
     * @param \Sonata\BlockBundle\Model\BlockInterface $block
     * @param null|\Symfony\Component\HttpFoundation\Response $response
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        if ($block->getEnabled()) {
            // merge settings
            $settings = array_merge($this->getDefaultSettings(), $block->getSettings());

            $feeds = false;
            if ($settings['url']) {
                $feeds = $this->feedReader->import($block);
            }

            return $this->renderResponse($this->getTemplate(), array(
                'feeds' => $feeds,
                'block' => $block,
                'settings' => $settings
            ), $response);
        }

        return $response;
    }

    /**
     * Returns the default settings link to the service
     *
     * @return array
     */
    public function getDefaultSettings()
    {
        return array_merge(parent::getDefaultSettings(), array(
            'maxItems' => 10
        ));
    }
}
