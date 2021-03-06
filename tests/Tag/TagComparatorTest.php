<?php
/**
 * (c) Steve Nebes <snebes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SN\DaisyDiff\Tag;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/**
 * TagComparator Tests.
 */
class TagComparatorTest extends TestCase
{
    public function testGetAtoms(): void
    {
        $input = '<p>This is a blue book</p>';
        $comparator = new TagComparator($input);

        $this->assertEquals(11, count($comparator->getAtoms()));
    }

    public function testGenerateAtoms(): void
    {
        $input = '<p>This is a blue book</p> test';
        $comparator = new TagComparator($input);

        $this->assertEquals('TagAtom: <p>', strval($comparator->getAtom(0)));
        $this->assertEquals('DelimiterAtom:  ', strval($comparator->getAtom(8)));
        $this->assertEquals('TextAtom: book', strval($comparator->getAtom(9)));
        $this->assertEquals('TagAtom: </p>', strval($comparator->getAtom(10)));
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGenerateAtomsNull(): void
    {
        $input = '';
        $comparator = new TagComparator($input);

        try {
            strval($comparator->getAtom(4));
        } catch (OutOfBoundsException $e) {
            throw $e;
        }
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGenerateAtomsIndexOutOfBounds(): void
    {
        $input = '<p> This is a blue book</p>';
        $comparator = new TagComparator($input);

        try {
            $comparator->getAtom(20);
        } catch (OutOfBoundsException $e) {
            $this->assertEquals('Index: 20, Size: 12', $e->getMessage());
            throw $e;
        }
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGenerateAtomsNegativeIndexOutOfBounds(): void
    {
        $input = '<p> This is a blue book</p>';
        $comparator = new TagComparator($input);

        try {
            $comparator->getAtom(-1);
        } catch (OutOfBoundsException $e) {
            $this->assertEquals('Index: -1, Size: 12', $e->getMessage());
            throw $e;
        }
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGenerateAtomsTwice(): void
    {
        $input = '<p>test</p>';
        $comparator = new TagComparator($input);

        try {
            $refMethod = new ReflectionMethod($comparator, 'generateAtoms');
            $refMethod->setAccessible(true);
            $refMethod->invoke($comparator, $input);
        } catch (ReflectionException $e) {
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function testGetRangeCount(): void
    {
        $input = '<p>This is a blue book</p>';
        $comparator = new TagComparator($input);

        $this->assertEquals(11, $comparator->getRangeCount());
    }

    public function testGetRangeCountZero(): void
    {
        $input = '';
        $comparator = new TagComparator($input);

        $this->assertEquals(0, $comparator->getRangeCount());
    }

    public function testGetSubstring(): void
    {
        $input = '<p>This is a blue book</p>';
        $comparator = new TagComparator($input);

        $this->assertEquals('a blue book</p>', $comparator->substring(5));
        $this->assertEquals('This is a blue ', $comparator->substring(1, 9));
        $this->assertEquals('', $comparator->substring(9, 9));
    }

    public function testRangesEqual(): void
    {
        $input = '<p>This is a blue book</p>';
        $comparator = new TagComparator($input);

        $this->assertFalse($comparator->rangesEqual(0, $comparator, 10));
        $this->assertFalse($comparator->rangesEqual(5, $comparator, 10));
        $this->assertTrue($comparator->rangesEqual(5, $comparator, 5));
    }

    public function testSkipRangeComparison(): void
    {
        $input = '<p>This is a blue book</p>';
        $comparator = new TagComparator($input);

        $this->assertFalse($comparator->skipRangeComparison(0, 5, $comparator));
    }
}
