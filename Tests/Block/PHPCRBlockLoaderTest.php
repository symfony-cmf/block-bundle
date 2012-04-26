<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Block;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Cmf\Bundle\ContentBundle\Document\StaticContent,
    Symfony\Cmf\Bundle\BlockBundle\Block\PHPCRBlockLoader,
    Symfony\Cmf\Bundle\BlockBundle\Document\SimpleBlock;

class PHPCRBlockLoaderTest extends \PHPUnit_Framework_TestCase
{

    private function getSimpleBlockLoaderInstance()
    {
        $containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $dmMock = $this->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        return new PHPCRBlockLoader($containerMock, $dmMock);
    }

    public function testSupport()
    {
        $blockLoader = $this->getSimpleBlockLoaderInstance();

        $this->assertFalse($blockLoader->support('name'));
        $this->assertFalse($blockLoader->support(array()));
        $this->assertTrue($blockLoader->support(array(
            'name' => 'someName'
        )));
    }

    public function testFindByNameWithAbsolutePath()
    {
        $absoluteBlockPath = '/some/absolute/path';

        $containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $dmMock = $this->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();
        $dmMock->expects($this->once())
            ->method('find')
            ->with(
                    $this->equalTo(null),
                    $this->equalTo($absoluteBlockPath)
            );

        $blockLoader = new PHPCRBlockLoader($containerMock, $dmMock);
        $blockLoader->findByName($absoluteBlockPath);
    }

    public function testFindByNameWithRelativePath()
    {
        $contentPath = '/absolute/content';
        $relativeBlockPath = 'some/relative/path';

        $content = new StaticContent();
        $content->setPath($contentPath);

        $parameterBagMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();
        $parameterBagMock->expects($this->once())
            ->method('get')
            ->with(
                    $this->equalTo('contentDocument')
            )
            ->will($this->returnValue($content));

        $request = new Request();
        $request->attributes = $parameterBagMock;

        $containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $containerMock->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('request')
            )
            ->will($this->returnValue($request));

        $dmMock = $this->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();
        $dmMock->expects($this->once())
            ->method('find')
            ->with(
                    $this->equalTo(null),
                    $this->equalTo($contentPath . '/' . $relativeBlockPath)
            );

        $blockLoader = new PHPCRBlockLoader($containerMock, $dmMock);
        $blockLoader->findByName($relativeBlockPath);
    }

    public function testLoadValidBlock()
    {
        $simpleBlock = new SimpleBlock();
        $absoluteBlockPath = '/some/absolute/path';

        $containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $dmMock = $this->getMockBuilder('Doctrine\ODM\PHPCR\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();
        $dmMock->expects($this->once())
            ->method('find')
            ->with(
                    $this->equalTo(null),
                    $this->equalTo($absoluteBlockPath)
            )
            ->will($this->returnValue($simpleBlock));

        $blockLoader = new PHPCRBlockLoader($containerMock, $dmMock);
        $receivedBlock = $blockLoader->load(array(
            'name' => $absoluteBlockPath
        ));

        $this->assertEquals($simpleBlock, $receivedBlock);
    }

    public function testLoadInvalidBlock()
    {
        $blockLoader = $this->getSimpleBlockLoaderInstance();
        $this->assertNull($blockLoader->load('name'));
    }

}
