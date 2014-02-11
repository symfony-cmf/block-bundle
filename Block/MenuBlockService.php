<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class MenuBlockService extends BaseBlockService implements BlockServiceInterface
{
    protected $template = 'CmfBlockBundle:Block:block_menu.html.twig';

    /**
     * @param string                       $name
     * @param EngineInterface              $templating
     * @param BlockRendererInterface       $blockRenderer
     * @param BlockContextManagerInterface $blockContextManager
     */
    public function __construct($name, $templating, $template = null)
    {
        parent::__construct($name, $templating);

        if (null !== $template) {
            $this->template = $template;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        throw new \RuntimeException ('Not used at the moment, editing using a frontend or backend UI could be changed here');
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        throw new \RuntimeException ('Not used at the moment, validation for editing using a frontend or backend UI could be changed here');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if (!$response) {
            $response = new Response();
        }
        $menu = $blockContext->getBlock()->getMenuNode();
        
        // if the reference target block does not exist, we just skip the rendering
        if ($blockContext->getBlock()->getEnabled() && null !== $menu) {
            $response = $this->renderResponse ($blockContext->getTemplate (), array (
                    'menu' => $menu->getId(),
                    'block' => $blockContext->getBlock(),
            ), $response);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults (array(
            'template' => $this->template,
        ));
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
