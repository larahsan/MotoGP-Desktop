<?php
    class Configuracion
    {
        private $server = "localhost";
        private $user = "DBUSER2025";
        private $pass_local = "DBPSWD2025"; // Despliegue con XAMPP
        private $pass = "dbPSWD2025$"; // Despliegue con Azure
        private $dbname = "UO289684_DB";
        private $conn;

        private function connect()
        {
            $this->conn = new mysqli($this->server, $this->user, $this->pass_local, $this->dbname);
        }

        private function disconnect()
        {
            if ($this->conn) 
                $this->conn->close();
        }

        public function reiniciar()
        {
            $this->connect();

            $this->conn->query('SET FOREIGN_KEY_CHECKS = 0');

            $this->conn->query('TRUNCATE TABLE usuarios');
            $this->conn->query('TRUNCATE TABLE resultados');
            $this->conn->query('TRUNCATE TABLE observaciones');

            $this->conn->query('SET FOREIGN_KEY_CHECKS = 1');

            $this->disconnect();

            echo "Base de datos reiniciada.";
        }

        public function eliminar()
        {
            $this->connect();

            $sql = 'DROP DATABASE ' . $this->dbname;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $stmt->close();

            $this->disconnect();

            echo "Base de datos eliminada.";
        }

        public function exportar()
        {
            $this->connect();

            $sql = "SELECT u.id_usuario, u.codigo_identificacion, u.profesion, u.edad, u.genero, u.pericia,
                           r.id_resultado, r.dispositivo, r.tiempo, r.completado, r.comentarios_usuario, r.propuestas, r.valoracion, r.respuestas,
                           o.id_observaciones, o.comentarios_observador
                    FROM usuarios u
                    LEFT JOIN resultados r ON u.id_usuario = r.id_usuario
                    LEFT JOIN observaciones o ON u.id_usuario = o.id_usuario
                    ORDER BY u.id_usuario, r.id_resultado, o.id_observaciones";

            $result = $this->conn->query($sql);

            // Limpiar cualquier salida previa
            if (ob_get_length()) {
                ob_end_clean();
            }

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $this->dbname . '.csv"');

            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'codigo_identificacion',
                'profesion',
                'edad',
                'genero',
                'pericia',
                'dispositivo',
                'tiempo',
                'completado',
                'respuestas',
                'comentarios_usuario',
                'propuestas',
                'valoracion',
                'comentarios_observador'
            ], ',');

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    
                    fputcsv($output, [
                        $row['codigo_identificacion'],
                        $row['profesion'],
                        $row['edad'],
                        $row['genero'],
                        $row['pericia'],
                        $row['dispositivo'],
                        $row['tiempo'],
                        $row['completado'],
                        $row['respuestas'],
                        $row['comentarios_usuario'],
                        $row['propuestas'],
                        $row['valoracion'],
                        $row['comentarios_observador']
                    ], ',');
                }
            }

            fclose($output);
            $this->disconnect();
            
            exit; // Detener el resto del HTML para que no se junte con el csv

            echo "Base de datos exportada.";
        }
    }
?>

<!DOCTYPE HTML>

<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name ="author" content ="Lara Haya Santiago" />
    <meta name ="description" content ="Página para configurar las pruebas de usabilidad" />
    <meta name ="keywords" content ="Configuración, test, PHP" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <title>MotoGP-Juegos: Configuración Test</title>
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css"/>
    <link rel="icon" href="../multimedia/iconos/juegos.ico" />
</head>

<body>
    <main>
        <h2>Configuración Test</h2>

        <form action='#' method='post' name='configuracion'>
            <input type = 'submit' name = 'reiniciar' value = 'Reiniciar base de datos'/>
            <input type = 'submit' name = 'eliminar' value = 'Eliminar base de datos'/>
            <input type = 'submit' name = 'exportar' value = 'Exportar base de datos'/>
        </form>
    </main>

    <?php
        if (count($_POST)>0) 
        {   
            $configuracion = new Configuracion();

            if(isset($_POST['reiniciar'])) $configuracion->reiniciar();
            if(isset($_POST['eliminar'])) $configuracion->eliminar();
            if(isset($_POST['exportar'])) $configuracion->exportar();
        }
    ?>

</body>
</html>