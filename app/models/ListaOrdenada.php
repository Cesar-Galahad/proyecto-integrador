<?php
require_once __DIR__ . '/Node.php';
class ListaOrdenada {
    private $head;
    private $campo;
    private $ascendente;
    public function __construct($campo = 'nombre', $ascendente = true) {
        $this->head = null;
        $this->campo = $campo;
        $this->ascendente = $ascendente;
    }
    public function insertar($nuevo) {
        $nuevoNodo = new Node($nuevo);
        // Caso: lista vacía o insertar al inicio
        if ($this->head === null || $this->comparar($nuevo[$this->campo] ?? '', $this->head->data[$this->campo] ?? '')) {
            $nuevoNodo->next = $this->head;
            $this->head = $nuevoNodo;
            return;
        }
        // Buscar posición correcta
        $actual = $this->head;
        while ($actual->next !== null && !$this->comparar($nuevo[$this->campo] ?? '', $actual->next->data[$this->campo] ?? '')) {
            $actual = $actual->next;
        }
        // Insertar en medio o al final
        $nuevoNodo->next = $actual->next;
        $actual->next = $nuevoNodo;
    }
    private function comparar($a, $b) {
        return $this->ascendente ? strcmp($a, $b) < 0 : strcmp($a, $b) > 0;
    }
    //Aqui obtenermos nuestros datos
    public function obtenerTodos() {
        $resultado = [];
        $actual = $this->head;
        while ($actual !== null) {
            $resultado[] = $actual->data;
            $actual = $actual->next;
        }
        return $resultado;
    }

    public function reiniciarOrden($campo, $ascendente = true) {
        $this->campo = $campo;
        $this->ascendente = $ascendente;

        $reordenada = new ListaOrdenada($campo, $ascendente);
        foreach ($this->obtenerTodos() as $dato) {
            $reordenada->insertar($dato);
        }
        $this->head = null;
        foreach ($reordenada->obtenerTodos() as $dato) {
            $this->insertar($dato);
        }
    }
}