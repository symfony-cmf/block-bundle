<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Functional\Block;

use Symfony\Component\HttpFoundation\Response;
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

    public function setUp()
    {
        $this->templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\Fragment\FragmentHandler');
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

        $actionBlockService = new ActionBlockService('test-service', $this->templating, $this->kernel);
        $actionBlockService->execute(new BlockContext($actionBlock));
    }

    public function testExecutionOfEnabledBlock()
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setEnabled(true);
        $actionBlock->setActionName('CmfBlockBundle:Test:test');

        $content = "Rendered Action Block.";

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $this->kernel
            ->expects($this->once())
            ->method('render')
            ->will($this->returnValue($content))
        ;

        $actionBlockService = new ActionBlockService('test-service', $this->templating, $this->kernel);
        $actionBlockService->setRequest($request);

        $response = $actionBlockService->execute(new BlockContext($actionBlock));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals($content, $response->getContent());
    }

}
