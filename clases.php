<?php
class Persona {
    public $id;
    public $nombre;
    public $hijos = [];
    public function __construct($id, $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }
}
class ArbolGenealogico {
    public $raiz;
    public function __construct($id, $nombre) {
        $this->raiz = new Persona($id, $nombre);
    }

//CRUD

    public function agregarHijo($idPadre, $idHijo, $nombreHijo) {
        $padre = $this->buscarPersona($this->raiz, $idPadre);
        if ($padre) {
            $padre->hijos[] = new Persona($idHijo, $nombreHijo);
        }
    }

    public function buscarPersona($nodo, $id) {
        if ($nodo->id == $id) return $nodo;
        foreach ($nodo->hijos as $hijo) {
            $res = $this->buscarPersona($hijo, $id);
            if ($res) return $res;
        }
        return null;
    }

    public function eliminarPersona(&$nodo, $id) {
        foreach ($nodo->hijos as $i => $hijo) {
            if ($hijo->id == $id) {
                unset($nodo->hijos[$i]);
                $nodo->hijos = array_values($nodo->hijos);
                return true;
            } else {
                if ($this->eliminarPersona($hijo, $id)) return true;
            }
        }
        return false;
    }

    public function moverSubarbol($idPersona, $idNuevoPadre) {
        $subarbol = $this->extraerSubarbol($this->raiz, $idPersona);
        $nuevoPadre = $this->buscarPersona($this->raiz, $idNuevoPadre);
        if ($subarbol && $nuevoPadre) {
            $nuevoPadre->hijos[] = $subarbol;
        }
    }

    private function extraerSubarbol(&$nodo, $id) {
        foreach ($nodo->hijos as $i => $hijo) {
            if ($hijo->id == $id) {
                $sub = $hijo;
                unset($nodo->hijos[$i]);
                $nodo->hijos = array_values($nodo->hijos);
                return $sub;
            } else {
                $res = $this->extraerSubarbol($hijo, $id);
                if ($res) return $res;
            }
        }
        return null;
    }

    // --- DFS ---
    public function dfs($nodo = null) {
        if (!$nodo) $nodo = $this->raiz;
        echo $nodo->nombre . " ";
        foreach ($nodo->hijos as $hijo) {
            $this->dfs($hijo);
        }
    }

    // --- BFS ---
    public function bfs() {
        $queue = [$this->raiz];
        while (!empty($queue)) {
            $nodo = array_shift($queue);
            echo $nodo->nombre . " ";
            foreach ($nodo->hijos as $hijo) {
                $queue[] = $hijo;
            }
        }
    }

    // --- Profundidad máxima ---
    public function profundidadMaxima($nodo = null) {
        if (!$nodo) $nodo = $this->raiz;
        if (empty($nodo->hijos)) return 1;
        $max = 0;
        foreach ($nodo->hijos as $hijo) {
            $max = max($max, $this->profundidadMaxima($hijo));
        }
        return 1 + $max;
    }

    // --- Contar descendientes ---
    public function contarDescendientes($id) {
        $persona = $this->buscarPersona($this->raiz, $id);
        if (!$persona) return 0;
        return $this->contarRecursivo($persona) - 1;
    }

    private function contarRecursivo($nodo) {
        $total = 1;
        foreach ($nodo->hijos as $hijo) {
            $total += $this->contarRecursivo($hijo);
        }
        return $total;
    }
}