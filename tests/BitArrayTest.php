<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/BitArray.php';

class BitArrayTest extends TestCase {
    public function testExempleEnonce(): void {
        $bits = new BitArray();
        $bits[10] = 1;

        $this->assertSame('0b10000000000', (string) $bits);
        $this->assertSame(11, count($bits));
    }

    public function testFromStringEtFromInt(): void {
        $this->assertSame('0b1010', (string) BitArray::fromString('0b1010'));
        $this->assertSame('0b1010', (string) BitArray::fromInt(10));
    }

    public function testSlices(): void {
        $bits = BitArray::fromString('0b1010010111110000');
        $slices = [];

        foreach ($bits->getSlicesIterator(4) as $slice) {
            $slices[] = (string) $slice;
        }

        $this->assertSame(['0b0000', '0b1111', '0b0101', '0b1010'], $slices);
    }
}
