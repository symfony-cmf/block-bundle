<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class TestController extends Controller
{
    /**
     * @param  Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('::index.html.twig');
    }

    /**
     * Generic way to render blocks
     *
     * @param  $id
     * @return Response
     */
    protected function renderBlock($id)
    {
        $block = $this->get('doctrine_phpcr')->getManager()->find(null, '/test/blocks/' . $id);

        return $this->render('::tests/render.html.twig', array('block' => $block));
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function renderSimpleBlockAction(Request $request)
    {
        return $this->renderBlock('block-1');
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function renderActionBlockAction(Request $request)
    {
        return $this->renderBlock('action-block-1');
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function renderContainerBlockAction(Request $request)
    {
        return $this->renderBlock('container-block-1');
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function renderReferenceBlockAction(Request $request)
    {
        return $this->renderBlock('reference-block-1');
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function renderStringBlockAction(Request $request)
    {
        return $this->renderBlock('string-block-1');
    }
}
