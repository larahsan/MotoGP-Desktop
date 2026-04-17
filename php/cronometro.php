<?php
    class Cronometro
    {
        private $tiempo;
        private $inicio;
        
        public function __construct() {
            session_start();
            $this->tiempo = 0;
        }

        public function getTiempo() {
            if(isset($_SESSION['cronometro_tiempo'])) {
                return $_SESSION['cronometro_tiempo'];
            }
            return null;
        }

        public function arrancar() {
            $this->inicio = microtime(true);
            $_SESSION['cronometro_inicio'] = $this->inicio;
        }

        public function parar() {
            if(isset($_SESSION['cronometro_inicio'])) { // Si se ha iniciado el cronómetro
                $this->tiempo = microtime(true) - $_SESSION['cronometro_inicio'];
                $_SESSION['cronometro_tiempo'] = $this->tiempo;
            }
        }

        public function mostrar() {
            $this->tiempo = $this->getTiempo();
            if($this->tiempo != null) { // Si se ha parado el cronómetro
                $minutos = floor($this->tiempo / 60);
                $segundos = floor($this->tiempo % 60);
                $decimas = floor(($this->tiempo - floor($this->tiempo)) * 10);

                $tiempoFormateado = sprintf('%02d:%02d.%1d', $minutos, $segundos, $decimas);
                echo "<p>Tiempo transcurrido: $tiempoFormateado</p>";
            }
        }
    }
?>