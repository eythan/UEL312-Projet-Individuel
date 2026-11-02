<?php declare(strict_types=1);

class FibonacciSequence implements Iterator {
    private int $count;
    private bool $isInfinite;
    private int $start;
    private int $position = 0;
    // Cache partagé entre toutes les instances, on part des 2 premiers termes
    private static array $values = [0, 1];

    // -1 = suite infinie
    public function __construct(int $n = -1, int $start = 0) {
        $this->count = $n;
        $this->isInfinite = $n === -1;
        $this->start = $start;
    }

    // Les n premiers termes de la suite
    public static function first(int $n): self {
        if ($n < 0) {
            throw new InvalidArgumentException('Le nombre de membres ne peut pas être négatif.');
        }

        return new self($n);
    }

    // Pareil que first mais on commence au terme numéro $start
    public static function range(int $start, int $length = -1): self {
        if ($start < 0) {
            throw new InvalidArgumentException('Le point de départ ne peut pas être négatif.');
        }

        return new self($length, $start);
    }

    public function current(): mixed {
        // Le calcul se fait seulement ici (évaluation paresseuse)
        return $this->fibonacci($this->start + $this->position);
    }

    public function key(): mixed {
        return $this->position;
    }

    public function next(): void {
        $this->position++;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        if ($this->isInfinite) {
            return true;
        }

        return $this->position < $this->count;
    }

    // Calcule seulement les termes qui manquent dans le cache
    private function fibonacci(int $n): int|float {
        for ($i = count(self::$values); $i <= $n; $i++) {
            self::$values[] = self::$values[$i - 1] + self::$values[$i - 2];
        }

        return self::$values[$n];
    }
}
?>
