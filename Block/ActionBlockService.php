<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Kernel;

class ActionBlockService extends BaseBlockService implements BlockServiceInterface
{
    protected $renderer;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param string $name
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $renderer
     */
    public function __construct($name, EngineInterface $templating, $renderer)
    {
        parent::__construct($name, $templating);
        $this->renderer = $renderer;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        throw new \RuntimeException('Not used at the moment, editing using a frontend or backend UI could be changed here');
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        throw new \RuntimeException('Not used at the moment, validation for editing using a frontend or backend UI could be changed here');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }

        $block = $blockContext->getBlock();

        if (!$block->getActionName()) {
            throw new \RuntimeException(sprintf(
                'ActionBlock with id "%s" does not have an action name defined, implement a default or persist it in the document.',
                $block->getId()
            ));
        }

        $locale = $this->request ? $this->request->getLocale() : null;

        if ($block->getEnabled()) {
            $response = new Response($this->renderer->render(new ControllerReference(
                $block->getActionName(),
                array('block' => $block, 'blockContext' => $blockContext, '_locale' => $locale)
            )));
        }

        return $response;
    }
}
