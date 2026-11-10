<?php declare(strict_types=1);

// Permet de parcourir les bits (ou les slices) avec un foreach
class BitArrayIterator implements Iterator {
    private array $values;
    private int $position = 0;

    public function __construct(array $values) {
        $this->values = $values;
    }

    public function current(): int|BitArray {
        return $this->values[$this->position];
    }

    public function key(): int {
        return $this->position;
    }

    public function next(): void {
        $this->position++;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        return $this->position < count($this->values);
    }
}

class BitArray implements ArrayAccess, Countable, IteratorAggregate, Stringable {
    private const BYTE_SIZE = 8;
    private const INT_SIZE = PHP_INT_SIZE * self::BYTE_SIZE;

    // Index 0 = bit de poids faible (celui le plus à droite à l'affichage)
    private array $bits = [];

    public function __construct() {}

    // Convertit un entier en binaire puis réutilise fromString
    public static function fromInt(int $from): self {
        return self::fromString(decbin($from));
    }

    public static function fromString(string $from): self {
        if (str_starts_with($from, '0b')) {
            $from = substr($from, 2);
        }

        // On retourne la chaîne car le bit de droite doit être à l'indice 0
        $bitArray = new self();
        $reversed = strrev($from);

        for ($i = 0; $i < strlen($reversed); $i++) {
            $char = $reversed[$i];

            if ($char !== '0' && $char !== '1') {
                throw new ValueError("Caractère invalide : $char");
            }

            $bitArray[$i] = (int) $char;
        }

        return $bitArray;
    }

    // Crée un nouveau BitArray avec une partie des bits
    public function slice(int $start = 0, int $length = -1): self {
        $end = $this->getEnd($start, $length);
        $slice = new self();

        for ($i = $start; $i < $end; $i++) {
            $slice[$i - $start] = $this->bits[$i];
        }

        return $slice;
    }

    // Écrit plusieurs bits d'un coup à partir de $start
    public function set(array $bits, int $start = 0): void {
        $position = $start;

        foreach ($bits as $bit) {
            $this[$position] = $bit;
            $position++;
        }
    }

    // Met à 0 les bits entre $start et la fin calculée
    public function unset(int $start, int $length = -1): void {
        $end = $this->getEnd($start, $length);

        for ($i = $start; $i < $end; $i++) {
            $this->bits[$i] = 0;
        }
    }

    public function offsetExists(mixed $offset): bool {
        return is_int($offset) && $offset >= 0 && $offset < count($this->bits);
    }

    public function offsetGet(mixed $offset): int {
        if (!$this->offsetExists($offset)) {
            throw new OutOfRangeException("Indice invalide");
        }

        return $this->bits[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        // $bits[] = 1 : on ajoute le bit à la fin
        if ($offset === null) {
            $offset = count($this->bits);
        }

        if (!is_int($offset) || $offset < 0) {
            throw new OutOfRangeException("Indice invalide");
        }

        if ($value !== 0 && $value !== 1 && !is_bool($value)) {
            throw new ValueError("Un bit doit valoir 0, 1, true ou false");
        }

        // On remplit les bits manquants avec des 0
        for ($i = count($this->bits); $i < $offset; $i++) {
            $this->bits[$i] = 0;
        }

        $this->bits[$offset] = (int) $value;
    }

    // unset($bits[3]) remet le bit à 0 au lieu de le supprimer
    public function offsetUnset(mixed $offset): void {
        if ($this->offsetExists($offset)) {
            $this->bits[$offset] = 0;
        }
    }

    // Nombre de bits jusqu'au plus haut bit attribué
    public function count(): int {
        return count($this->bits);
    }

    public function getIterator(): BitArrayIterator {
        return new BitArrayIterator($this->bits);
    }

    // Découpe le tableau en morceaux de $size bits pour les parcourir un par un
    public function getSlicesIterator(int $size): BitArrayIterator {
        if ($size <= 0) {
            throw new ValueError("La taille des slices doit être positive");
        }

        $slices = [];

        for ($start = 0; $start < count($this->bits); $start += $size) {
            $slices[] = $this->slice($start, $size);
        }

        return new BitArrayIterator($slices);
    }

    // Permet d'afficher les bits, on inverse pour avoir le bit 0 à droite
    public function __toString(): string {
        return '0b' . implode('', array_reverse($this->bits));
    }

    // Calcule la fin (exclue) à partir du départ et de la taille
    private function getEnd(int $start, int $length): int {
        if ($start < 0) {
            throw new OutOfRangeException("Le départ ne peut pas être négatif");
        }

        if ($length < 0) {
            return count($this->bits);
        }

        $end = $start + $length;

        if ($end > count($this->bits)) {
            $end = count($this->bits);
        }

        return $end;
    }
}
?>
