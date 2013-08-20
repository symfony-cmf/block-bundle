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
    public function renderSimpleAction(Request $request)
    {
        return $this->renderBlock('block-1');
    }
}
