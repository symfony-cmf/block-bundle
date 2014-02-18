<?php

/*
 * 
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\AbstractBlock;
use Knp\Menu\NodeInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;

/**
 * Block that is a reference to a menu.
 */
class MenuBlock extends AbstractBlock
{
    /**
     * @var MenuNode
     */
    private $menuNode;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cmf.block.menu';
    }

    /**
     * @return NodeInterface|null
     */
    public function getMenuNode()
    {
        return $this->menuNode;
    }

    /**
     * @param NodeInterface $menuNode
     *
     * @return MenuBlock $this
     */
    public function setMenuNode(NodeInterface $menuNode)
    {
        $this->menuNode = $menuNode;

        return $this;
    }
}
