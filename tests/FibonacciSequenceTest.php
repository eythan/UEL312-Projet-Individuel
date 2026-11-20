<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/FibonacciSequence.php';

class FibonacciSequenceTest extends TestCase {
    public function testFirst(): void {
        $values = iterator_to_array(FibonacciSequence::first(10));

        $this->assertSame([0, 1, 1, 2, 3, 5, 8, 13, 21, 34], $values);
    }

    public function testRange(): void {
        $values = iterator_to_array(FibonacciSequence::range(5, 3));

        $this->assertSame([5, 8, 13], $values);
    }
}
