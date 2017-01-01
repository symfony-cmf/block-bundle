<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Tests\Unit\Templating\Helper;

use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\EmbedBlocksParser;

class EmbedBlocksParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var EmbedBlocksParser */
    protected $parser;

    public function setUp()
    {
        $this->parser = new EmbedBlocksParser('%embed-block|', '|end%');
    }

    /**
     * @dataProvider segmentizeProvider
     *
     * @param $text
     * @param $expected
     */
    public function testSegmentize($text, $expected)
    {
        $actual = $this->callMethod($this->parser, 'segmentize', [$text]);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider parseProvider
     *
     * @param $text
     * @param $expected
     */
    public function testParse($text, $expected)
    {
        $actual = $this->parser->parse(
            $text,
            function ($id) {
                return 'this_is_block_content';
            }
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function parseProvider()
    {
        return [
            [
                '%embed-block|/cms/blocks/test|end%',
                'this_is_block_content',
            ],
            [
                '<div>%embed-block|/cms/blocks/test|end%<div>%embed-block|/cms/blocks/test|end%</div>',
                '<div>this_is_block_content<div>this_is_block_content</div>',
            ],
            [
                '/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>',
                '/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>',
            ],
            [
                "%embed-block|/cms/blocks/test\n\n   |end%<div>/cms/blocks/test|end%</div>",
                'this_is_block_content<div>/cms/blocks/test</div>',
            ],
        ];
    }

    /**
     * @return array
     */
    public function segmentizeProvider()
    {
        return [
            [
                '%embed-block|/cms/blocks/test|end%',
                ['', ['/cms/blocks/test']],
            ],
            [
                '<div>%embed-block|/cms/blocks/test|end%</div>',
                ['<div>', ['/cms/blocks/test', '</div>']],
            ],
            [
                '<div>%embed-block|/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>',
                ['<div>', ['/cms/blocks/test', '<div>/cms/blocks/test', '</div>']],
            ],
            [
                '/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>',
                ['/cms/blocks/test|end%<div>/cms/blocks/test|end%</div>'],
            ],
            [
                "%embed-block|/cms/blocks/test\n\n   |end%<div>/cms/blocks/test|end%</div>",
                ['', ['/cms/blocks/test', '<div>/cms/blocks/test', '</div>']],
            ],
            [
                '<div>%embed-block|/cms/blocks/test1|end%</div><div>%embed-block|/cms/blocks/test2|end%</div><div>%embed-block|/cms/blocks/test3|end%</div>',
                [
                    '<div>',
                    ['/cms/blocks/test1', '</div><div>'],
                    ['/cms/blocks/test2', '</div><div>'],
                    ['/cms/blocks/test3', '</div>'],
                ],
            ],
            [
                '<div>%embed-block|/cms/blocks/test1|end%</div><div>/cms/blocks/test2|end%</div><div>%embed-block|/cms/blocks/test3|end%</div>',
                [
                    '<div>',
                    ['/cms/blocks/test1', '</div><div>/cms/blocks/test2', '</div><div>'],
                    ['/cms/blocks/test3', '</div>'],
                ],
            ],
        ];
    }

    /**
     * @param $object
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    private function callMethod($object, $method, array $params)
    {
        $methodCall = \Closure::bind(
            function () use ($method) {
                return call_user_func_array([$this, $method], func_get_args());
            },
            $object,
            $object
        );

        return call_user_func_array($methodCall, $params);
    }
}
