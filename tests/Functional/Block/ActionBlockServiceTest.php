<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Functional\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Cmf\Bundle\BlockBundle\Block\ActionBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class ActionBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $templating;

    /**
     * @var FragmentHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private $kernel;

    private $requestStack;

    protected function setUp()
    {
        $this->templating = $this->createMock(EngineInterface::class);
        $this->kernel = $this->createMock(FragmentHandler::class);
        $this->requestStack = $this->createMock(RequestStack::class);
    }

    public function testExecutionOfDisabledBlock()
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setEnabled(false);
        $actionBlock->setActionName('CmfBlockBundle:Test:test');

        $this->kernel
            ->expects($this->never())
            ->method('render')
        ;

        $actionBlockService = new ActionBlockService($this->requestStack, 'test-service', $this->templating, $this->kernel);
        $actionBlockService->execute(new BlockContext($actionBlock));
    }

    public function testExecutionOfEnabledBlock()
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setEnabled(true);
        $actionBlock->setActionName('CmfBlockBundle:Test:test');

        $content = 'Rendered Action Block.';

        $request = $this->createMock(Request::class);

        $this->kernel
            ->expects($this->any())
            ->method('render')
            ->will($this->returnValue($content))
        ;

        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request))
        ;

        $actionBlockService = new ActionBlockService($this->requestStack, 'test-service', $this->templating, $this->kernel);

        $response = $actionBlockService->execute(new BlockContext($actionBlock));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals($content, $response->getContent());
    }
}
