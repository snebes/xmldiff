<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Html\Dom;

use SN\DaisyDiff\Html\Modification\Modification;
use SN\DaisyDiff\Html\Modification\ModificationType;
use PHPUnit\Framework\TestCase;

/**
 * TextNode Tests.
 */
class TextNodeTest extends TestCase
{
    public function testCopyTree(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'contents of root node');
        /** @var TextNode $copyRoot */
        $copyRoot = $textRoot->copyTree();

        $this->assertSame($textRoot->getText(), $copyRoot->getText());
        $this->assertSame($textRoot->getModification(), $copyRoot->getModification());
    }

    public function testGetLeftRightMostChild(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'root');

        $this->assertSame($textRoot, $textRoot->getLeftMostChild());
        $this->assertSame($textRoot, $textRoot->getRightMostChild());
    }

    public function testGetModificationText(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'root');
        $textRoot->setModification(null);

        $this->assertNull($textRoot->getModification());
    }

    public function testGetText(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'root');

        $this->assertSame('root', $textRoot->getText());
        $this->assertSame('root', $textRoot->__toString());
    }

    public function testIsSameText(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'root');
        $textBody = new TextNode($root, 'root');

        $this->assertTrue($textRoot->isSameText($textBody));
        $this->assertFalse($textRoot->isSameText(null));
        $this->assertFalse($textRoot->isSameText($root));
    }

    public function testGetMinimalDeletedSet(): void
    {
        $root = new TagNode(null, 'root');
        $textRoot = new TextNode($root, 'contents of root node');
        $intermediate = new TagNode($root, 'intermediate');
        $textIntermediate = new TextNode($root, 'contents of intermediate node');

        $this->assertSame([], $textRoot->getMinimalDeletedSet(0));

        $mod = new Modification(ModificationType::REMOVED, ModificationType::REMOVED);
        $mod->setId(0);
        $textIntermediate->setModification($mod);

        $this->assertSame([$textIntermediate], $textIntermediate->getMinimalDeletedSet(0));
    }
}
