<?php declare(strict_types=1);

require 'src/FibonacciSequence.php';
require 'src/Defer.php';
require 'src/BitArray.php';
header('Content-Type: text/plain');

$n=100;
echo("FibonacciSequence ($n) :\n");
$values = iterator_to_array(FibonacciSequence::first($n));

echo implode(', ', $values) . "\n";

echo("\nDefer :\n");

function world() {
    echo "World\n";
}

function hello() {
    Defer::init($abc);
    $abc("world");

    echo "Hello ";
}

hello();

echo("\nBitArray :\n");

$bits = new BitArray();
$bits[10] = 1;
echo $bits . "\n";
echo count($bits) . "\n";

$bits = BitArray::fromString("0b1010010111110000");
foreach ($bits->getSlicesIterator(4) as $slice) {
    echo $slice . "\n";
}
