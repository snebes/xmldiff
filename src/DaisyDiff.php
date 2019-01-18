<?php

/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DaisyDiff;

use DaisyDiff\Html\Ancestor\ChangeText;
use DaisyDiff\Html\DelegatingContentHandler;
use DaisyDiff\Html\Dom\DomTreeBuilder;
use DaisyDiff\Html\HtmlDiffer;
use DaisyDiff\Html\HtmlSaxDiffOutput;
use DaisyDiff\Html\TextNodeComparator;
use DaisyDiff\Tag\TagComparator;
use DaisyDiff\Tag\TagDiffer;
use DaisyDiff\Tag\TagSaxDiffOutput;
use DaisyDiff\Xml\XMLReader;
use Exception;

/**
 * Daisy Diff is a library that diffs (compares) HTML.
 */
class DaisyDiff
{
    /**
     * Diffs two HTML strings, returning the result.
     *
     * @param string $oldSource
     * @param string $newSource
     * @return string
     * @throws Exception
     */
    public function diff(string $oldSource, string $newSource): string
    {
        // Parse $old XML.
        $oldHandler = new DomTreeBuilder();
        $reader1 = new XMLReader($oldHandler);
        $reader1->parse($oldSource);

        // Parse $new XML.
        $newHandler = new DomTreeBuilder();
        $reader2 = new XMLReader($newHandler);
        $reader2->parse($newSource);

        // Comparators.
        $leftComparator = new TextNodeComparator($oldHandler);
        $rightComparator = new TextNodeComparator($newHandler);

        $content = new ChangeText();
        $handler = new DelegatingContentHandler($content);
        $output = new HtmlSaxDiffOutput($handler, 'diff');
        $differ = new HtmlDiffer($output);
        $differ->diff($leftComparator, $rightComparator);

        return strval($content);
    }

    /**
     * Diffs two HTML strings for word as source, returning the result.
     *
     * @param  string $oldText
     * @param  string $newText
     * @return string
     * @throws Exception
     */
    public function diffTag(string $oldText, string $newText): string
    {
        $oldComp = new TagComparator($oldText);
        $newComp = new TagComparator($newText);

        $content = new ChangeText();
        $handler = new DelegatingContentHandler($content);
        $output = new TagSaxDiffOutput($handler);
        $differ = new TagDiffer($output);
        $differ->diff($oldComp, $newComp);

        return strval($content);
    }
}
