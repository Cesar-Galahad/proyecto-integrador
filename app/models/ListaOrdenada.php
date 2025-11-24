<?php
class ListaOrdenada {
    private $datos = [];
    private $campo;
    private $ascendente;

    public function __construct($campo = 'nombre', $ascendente = true) {
        $this->campo = $campo;
        $this->ascendente = $ascendente;
    }

    public function insertar($nuevo) {
        $insertado = false;
        $resultado = [];

        foreach ($this->datos as $actual) {
            $a = $nuevo[$this->campo] ?? '';
            $b = $actual[$this->campo] ?? '';

            if (!$insertado && $this->comparar($a, $b)) {
                $resultado[] = $nuevo;
                $insertado = true;
            }
            $resultado[] = $actual;
        }

        if (!$insertado) {
            $resultado[] = $nuevo;
        }

        $this->datos = $resultado;
    }

    private function comparar($a, $b) {
        return $this->ascendente ? strcmp($a, $b) < 0 : strcmp($a, $b) > 0;
    }

    public function obtenerTodos() {
        return $this->datos;
    }

    public function reiniciarOrden($campo, $ascendente = true) {
        $this->campo = $campo;
        $this->ascendente = $ascendente;

        $reordenada = new ListaOrdenada($campo, $ascendente);
        foreach ($this->datos as $dato) {
            $reordenada->insertar($dato);
        }
        $this->datos = $reordenada->obtenerTodos();
    }
}