<?php declare(strict_types=1);

class Callback {
    public function __construct(
        private mixed $cb,
        private array $args = [],
    ) {}

    public function call(): void {
        call_user_func_array($this->cb, $this->args);
    }
}

class Defer {
    // Liste des fonctions à appeler à la destruction
    private array $callbacks = [];

    public function __construct() {}

    // Le & permet de créer l'objet directement dans la variable passée en paramètre
    public static function init(?Defer &$defer = null): self {
        $defer = new self();

        return $defer;
    }

    // Appelée quand on utilise l'objet comme une fonction : $defer('world')
    public function __invoke(callable $function, mixed ...$args): void {
        $this->callbacks[] = new Callback($function, $args);
    }

    // Appelée à la sortie de la fonction, on exécute tout dans l'ordre inverse (LIFO)
    public function __destruct() {
        $reversed = array_reverse($this->callbacks);

        foreach ($reversed as $callback) {
            $callback->call();
        }
    }
}
?>
