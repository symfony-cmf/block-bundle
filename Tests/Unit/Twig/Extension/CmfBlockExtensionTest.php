<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Unit\Twig\Extension;

use Symfony\Cmf\Bundle\BlockBundle\Twig\Extension\CmfBlockExtension;

class CmfBlockExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $blockHelper;

    protected function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not available.');
        }
    }

    /**
     * @dataProvider getEmbedFilterData
     */
    public function testEmbedFilter($template, $calls = 1)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String(), array('debug' => true, 'cache' => false, 'autoescape' => true, 'optimizations' => 0));
        $twig->addExtension(new CmfBlockExtension($this->getBlockHelper()));

        $this->getBlockHelper()->expects($this->exactly($calls))
            ->method('embedBlocks');

        try {
            $twig->render($template);
        } catch (\Twig_Error_Runtime $e) {
            throw $e->getPrevious();
        }
    }

    public function getEmbedFilterData()
    {
        return array(
            array('{{ "bar"|cmf_embed_blocks }}'),
            array('{{ "bar"|cmf_embed_blocks }} lorem ipsum {{ "foo"|cmf_embed_blocks }}', 2),
        );
    }

    protected function getBlockHelper()
    {
        if (null === $this->blockHelper) {
            $this->setBlockHelper();
        }

        return $this->blockHelper;
    }

    private function setBlockHelper()
    {
        $this->blockHelper = $this->getMockBuilder('Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
