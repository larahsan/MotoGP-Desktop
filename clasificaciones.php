<?php
    class Clasificacion
    {
        private $documento;
        
        public function __construct() {
            $this->documento = "xml/circuitoEsquema.xml";
        }

        public function consultar() {

            $datos = file_get_contents($this->documento);

            if($datos == null) 
                echo "<p>Error en el archivo XML recibido.</p>";
            else {
                $datos =preg_replace("/>\s*</",">\n<",$datos);
                $xml = new SimpleXMLElement($datos);

                echo "<p>Ganador de la carrera: " . $xml->victoria['vencedor'] . "</p>";

                // Tiempo formateado
                $tiempo = $xml->victoria['tiempo'];
                if (preg_match('/PT(?:(\d+)M)?(?:(\d+(?:\.\d+)?)S)?/', $tiempo, $m))
                    $tiempo_formateado = (int)$m[1] . "min " . (float)$m[2] . " s";
                echo "<p>Tiempo empleado por el ganador para completar la carrera: " . $tiempo_formateado . "</p>";

                echo "<h3>Clasificación del mundial tras la carrera</h3>";
                $clasificados = $xml->clasificados->clasificado;

                for($i = 0; $i < count($clasificados); $i++) {
                    echo "<p>Clasificado " . $i + 1 . ": " . $clasificados[$i] . "</p>";
                }
            }
        }
    }
?>

<!DOCTYPE HTML>

<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name ="author" content ="Lara Haya Santiago" />
    <meta name ="description" content ="Documento sobre la clasificación de la carrera de MotoGP" />
    <meta name ="keywords" content ="Clasificaciones" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <title>MotoGP-Clasificaciones</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="estilo/layout.css"/>
    <link rel="icon" href="multimedia/iconos/clasificacion.ico" />
</head>

<body>
    <header>
        <h1><a href="index.html" title="Inicio">MotoGP Desktop</a></h1>

        <nav>
            <a href="index.html" title="Inicio">Inicio</a>
            <a href="piloto.html" title="Piloto">Piloto</a>
            <a href="circuito.html" title="Circuito">Circuito</a>
            <a href="meteorologia.html" title="Meteorología">Meteorología</a>
            <a href="clasificaciones.php" title="Clasificaciones" class="active">Clasificaciones</a>
            <a href="juegos.html" title="Juegos">Juegos</a>
            <a href="ayuda.html" title="Ayuda">Ayuda</a>
        </nav>
    </header>

    <p>Estás en: <a href="index.html" title="Inicio">Inicio</a> >> <strong>Clasificaciones</strong></p>

    <main>
        <h2>Clasificaciones de MotoGP Desktop</h2>

        <?php
            $clasificacion = new Clasificacion();
            $clasificacion->consultar();
        ?>
    </main>
   
</body>
</html>