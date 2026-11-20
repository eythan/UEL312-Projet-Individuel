<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/Defer.php';

class DeferTest extends TestCase {
    public function testHelloWorld(): void {
        $this->expectOutputString("Hello World");

        $hello = function () {
            Defer::init($abc);
            $abc(function () {
                echo "World";
            });

            echo "Hello ";
        };

        $hello();
    }

    public function testOrdreInverse(): void {
        $this->expectOutputString("321");

        $test = function () {
            $defer = Defer::init();
            $defer(function () { echo "1"; });
            $defer(function () { echo "2"; });
            $defer(function () { echo "3"; });
        };

        $test();
    }
}
