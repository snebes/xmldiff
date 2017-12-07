<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor\Helper;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\Ancestor\TagChangeSemantic;
use DaisyDiff\Html\Dom\TagNode;
use DaisyDiff\Html\Modification\HtmlLayoutChange;
use DaisyDiff\Html\Modification\HtmlLayoutChangeType;
use DaisyDiff\Xml\Xml;

/**
 * TagToString
 */
class TagToString
{
    /** @var TagNode */
    protected $node;

    /** @var string<TagChangeSemantic> */
    protected $sem;

    /** @var HtmlLayoutChange */
    protected $htmlLayoutChange;

    /**
     * @param  TagNode $node
     * @param  string  $sem
     */
    public function __construct(TagNode $node, string $sem)
    {
        $this->node = $node;
        $this->sem  = $sem;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getString('diff-' . $this->node->getQName());
    }

    /**
     * @param  ChangeText $text
     * @return void
     */
    public function getRemovedDescription(ChangeText $text): void
    {
        $this->htmlLayoutChange = new HtmlLayoutChange();
        $this->htmlLayoutChange->setEndingTag(Xml::closeElement($this->node->getQName()));
        $this->htmlLayoutChange->setOpeningTag(Xml::openElement($this->node->getQName(), $this->node->getAttributes()));
        $this->htmlLayoutChange->setType(HtmlLayoutChangeType::TAG_REMOVED);

        if ($this->sem == TagChangeSemantic::MOVED) {
            $text->addText(sprintf('%s %s ', $this->getMovedOutOf(), mb_strtolower($this->getArticle())));
            $text->addHtml('<b>');
            $text->addText(mb_strtolower($this->getDescription()));
            $text->addHtml('</b>');
        }
        elseif ($this->sem == TagChangeSemantic::STYLE) {
            $text->addHtml('<b>');
            $text->addText(mb_strtolower($this->getDescription()));
            $text->addHtml('</b>');
            $text->addText(sprintf(' %s', mb_strtolower($this->getStyleRemoved())));
        }
        else {
            $text->addHtml('<b>');
            $text->addText(mb_strtolower($this->getDescription()));
            $text->addHtml('</b>');
            $text->addText(sprintf(' %s', mb_strtolower($this->getRemoved())));
        }

        $this->addAttributes($text, $this->node->getAttributes());
        $text->addText('.');
    }

    /**
     * @param  ChangeText $text
     * @return void
     */
    public function getAddedDescription(ChangeText $text): void
    {
        $this->htmlLayoutChange = new HtmlLayoutChange();
        $this->htmlLayoutChange->setEndingTag(Xml::closeElement($this->node->getQName()));
        $this->htmlLayoutChange->setOpeningTag(Xml::openElement($this->node->getQName(), $this->node->getAttributes()));
        $this->htmlLayoutChange->setType(HtmlLayoutChangeType::TAG_ADDED);

        if ($this->sem == TagChangeSemantic::MOVED) {
            $text->addText(sprintf('%s %s ', $this->getMovedTo(), mb_strtolower($this->getArticle())));
            $text->addHtml('<b>');
            $text->addText(mb_strtolower($this->getDescription()));
            $text->addHtml('</b>');
        }
        elseif ($this->sem == TagChangeSemantic::STYLE) {
            $text->addHtml('<b>');
            $text->addText(mb_strtolower($this->getDescription()));
            $text->addHtml('</b>');
            $text->addText(sprintf(' %s', mb_strtolower($this->getStyleAdded())));
        }
        else {
            $text->addHtml('<b>');
            $text->addText(mb_strtolower($this->getDescription()));
            $text->addHtml('</b>');
            $text->addText(sprintf(' %s', mb_strtolower($this->getAdded())));
        }

        $this->addAttributes($text, $this->node->getAttributes());
        $text->addText('.');
    }

    /**
     * @return string
     */
    protected function getMovedTo(): string
    {
        return $this->getString('diff-movedto');
    }

    /**
     * @return string
     */
    protected function getStyleAdded(): string
    {
        return $this->getString('diff-styleadded');
    }

    /**
     * @return string
     */
    protected function getAdded(): string
    {
        return $this->getString('diff-added');
    }

    /**
     * @return string
     */
    protected function getMovedOutOf(): string
    {
        return $this->getString('diff-movedoutof');
    }

    /**
     * @return string
     */
    protected function getSytleRemoved(): string
    {
        return $this->getString('diff-styleremoved');
    }

    /**
     * @return string
     */
    protected function getRemoved(): string
    {
        return $this->getString('diff-removed');
    }

    /**
     * @param  ChangeText $text
     * @param  iterable   $attributes
     * @return void
     */
    protected function addAttributes(ChangeText $text, iterable $attributes = []): void
    {
        if (count($attributes) < 1) {
            return;
        }
        $atext = [];
        foreach ($attributes as $qName => $value) {
            $atext[] = sprintf('%s %s', $this->translateArgument($qName), $value);
        }

        $text->addText(sprintf('%s %s', mb_strtolower($this->getWith()), implode(', ', $atext)));
    }

    /**
     * @return string
     */
    protected function getAnd(): string
    {
        return $this->getString('diff-and');
    }

    /**
     * @return string
     */
    protected function getWith(): string
    {
        return $this->getString('diff-with');
    }

    /**
     * @param  string $name
     * @return string
     */
    protected function translateArgument(string $name): string
    {
        if (0 == strcasecmp($name, 'src')) {
            return mb_strtolower($this->getSource());
        }

        if (0 == strcasecmp($name, 'width')) {
            return mb_strtolower($this->getWidth());
        }

        if (0 == strcasecmp($name, 'height')) {
            return mb_strtolower($this->getHeight());
        }

        return $name;
    }

    /**
     * @return string
     */
    protected function getSource(): string
    {
        return $this->getString('diff-source');
    }

    /**
     * @return string
     */
    protected function getWidth(): string
    {
        return $this->getString('diff-width');
    }

    /**
     * @return string
     */
    protected function getHeight(): string
    {
        return $this->getString('diff-height');
    }

    /**
     * @return string
     */
    protected function getArticle(): string
    {
        return $this->getString(sprintf('diff-%s-article', $this->node->getQName()));
    }

    /**
     * @param  string $key
     * @return string
     */
    public function getString(string $key): string
    {
        return sprintf('!%s!', $key);
    }

    /**
     * @return HtmlLayoutChange
     */
    public function getHtmlLayoutChange(): HtmlLayoutChange
    {
        return $this->htmlLayoutChange;
    }
}
