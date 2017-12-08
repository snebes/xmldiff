<?php declare(strict_types=1);

namespace DaisyDiff\Html\Dom;

/**
 * This is an artificial text node whose sole purpose is to separate text nodes, so that they cannot be treated as a
 * continuous text flow by the RangeDifferencer.
 *
 * Such nodes will be created between two text nodes, when they really are separate, e.g. in two successive table cells.
 */
class SeparatingNode extends TextNode
{
    /**
     * @param  TagNode $parent
     */
    public function __construct(?TagNode $parent)
    {
        parent::__construct($parent, '');
    }

    /**
     * {@inheritdoc}
     */
    public function equals(?Node $other): bool
    {
        return $other === $this;
    }
}