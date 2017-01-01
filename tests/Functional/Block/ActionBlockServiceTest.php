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

use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\Templating\EngineInterface;
use Sonata\BlockBundle\Block\BlockContext;
use Symfony\Cmf\Bundle\BlockBundle\Block\ActionBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;

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

    public function setUp()
    {
        $this->templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $this->kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\Fragment\FragmentHandler')
            ->disableOriginalConstructor()->getMock();
        $this->requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
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

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

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
