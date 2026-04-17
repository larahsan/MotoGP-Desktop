<?php
    include('cronometro.php');

    class Test
    {
        private $server = "localhost";
        private $user = "DBUSER2025";
        private $pass_local = "DBPSWD2025"; // Despliegue con XAMPP
        private $pass = "dbPSWD2025$"; // Despliegue con Azure
        private $dbname = "UO289684_DB";
        private $conn;
        private $cronometro;

        public function __construct() 
        {
            $this->cronometro = new Cronometro();
        }

        private function connect()
        {
            $this->conn = new mysqli($this->server, $this->user, $this->pass_local, $this->dbname);
        }

        private function disconnect()
        {
            if ($this->conn) 
                $this->conn->close();
        }

        public function mostrarFormularioInicio() {
            echo "<form action='#' method='post' name='test'>
                      <input type='submit' name='iniciar' value='Iniciar prueba'/>
                  </form>";
        }

        public function iniciarPrueba()
        {
            $this->cronometro->arrancar();
            $this->mostrarPreguntas();
        }

        public function mostrarPreguntas()
        {
            echo "<form action='#' method='post'>";
            echo "<h3>Responda a las siguientes preguntas sobre el proyecto:</h3>";
            echo "<label for='p1'>1. ¿En qué circuito se basa el proyecto?</label>";
            echo "<input type='text' id='p1' name='p1'/>";
            echo "<label for='p2'>2. ¿Sobre qué piloto se habla específicamente en el proyecto?</label>";
            echo "<input type='text' id='p2' name='p2'/>";
            echo "<label for='p3'>3. ¿En qué país se encuentra el circuito en el que se basa el proyecto?</label>";
            echo "<input type='text' id='p3' name='p3'/>";
            echo "<label for='p4'>4. ¿Quién fue el ganador de la carrera?</label>";
            echo "<input type='text' id='p4' name='p4'/>";
            echo "<label for='p5'>5. ¿Quién fue el tercer clasificado en la carrera?</label>";
            echo "<input type='text' id='p5' name='p5'/>";
            echo "<label for='p6'>6. ¿Dónde nació el piloto del que se habla en el proyecto?</label>";
            echo "<input type='text' id='p6' name='p6'/>";
            echo "<label for='p7'>7. ¿Qué significa el concepto 'pole' en el contexto de MotoGP?</label>";
            echo "<input type='text' id='p7' name='p7'/>";
            echo "<label for='p8'>8. ¿En qué posición de la carrera quedó el piloto del que se habla en el proyecto?</label>";
            echo "<input type='text' id='p8' name='p8'/>";
            echo "<label for='p9'>9. ¿En qué mes tuvieron lugar los entrenamientos previos a la carrera?</label>";
            echo "<input type='text' id='p9' name='p9'/>";
            echo "<label for='p10'>10. ¿Quién fue el segundo clasificado en la carrera?</label>";
            echo "<input type='text' id='p10' name='p10'/>";
            echo "<input type='submit' name='terminar' value='Terminar prueba'/></form>";
        }

        public function terminarPrueba() 
        {
            $this->cronometro->parar();

            // Guardar las respuestas
            for ($i = 1; $i <= 10; $i++) {
                $campo = 'p' . $i;
                $_SESSION[$campo] = $_POST[$campo] ?? '';
            }

            $this->mostrarFormularioUsuario();
        }

        public function mostrarFormularioUsuario() 
        {
            echo "<form action='#' method='post'>";
            echo "<h3>Complete los siguientes datos:</h3>";
            echo "<label for='codigo_identificacion'>Código de identificación de usuario (1-12):</label>";
            echo "<input type='number' id='codigo_identificacion' name='codigo_identificacion' min='1' max='12' required/>";
            echo "<label for='profesion'>Profesión:</label>";
            echo "<input type='text' id='profesion' name='profesion' required/>";
            echo "<label for='edad'>Edad:</label>";
            echo "<input type='number' id='edad' name='edad' min='0' required/>";
            echo "<label for='genero'>Género:</label>";
            echo "<select id='genero' name='genero'>
                    <option value='Masculino'>Masculino</option>
                    <option value='Femenino'>Femenino</option>
                    <option value='Otro'>Otro</option>
                  </select>";
            echo "<label for='pericia'>Pericia informática (0-10):</label>";
            echo "<input type='number' id='pericia' name='pericia' min='0' max='10' required/>";
            echo "<label for='dispositivo'>Dispositivo:</label>";
            echo "<select id='dispositivo' name='dispositivo'>
                    <option value='Ordenador'>Ordenador</option>
                    <option value='Tableta'>Tableta</option>
                    <option value='Telefono'>Teléfono</option>
                  </select>";
            echo "<label for='comentarios_usuario'>Comentarios del usuario:</label>";
            echo "<textarea id='comentarios_usuario' name='comentarios_usuario'></textarea>";
            echo "<label for='propuestas'>Propuestas de mejora:</label>";
            echo "<textarea id='propuestas' name='propuestas'></textarea>";
            echo "<label for='valoracion'>Valoración (0-10):</label>";
            echo "<input type='number' id='valoracion' name='valoracion' min='0' max='10' required/>";
            echo "<label for='comentarios_observador'>Comentarios del observador:</label>";
            echo "<textarea id='comentarios_observador' name='comentarios_observador'></textarea>";
            echo "<input type='submit' name='guardar' value='Guardar'/></form>";

        }

        public function guardarDatos()
        {
            // Tiempo que ha tardado el usuario en hacer la prueba
            $tiempo = $this->cronometro->getTiempo(); 

            // Comprobar si el usuario ha completado la prueba (si ha respondido a todas las preguntas)
            $completado = 1;
            $respuestas = [];
            for ($i = 1; $i <= 10; $i++) {
                $campo = 'p' . $i;
                $valor = isset($_SESSION[$campo]) ? trim($_SESSION[$campo]) : '';
                if ($valor === '') $completado = 0;
                $respuestas[] = "R$i: " . $valor;
            }
            $respuestasTexto = implode(' | ', $respuestas);

            $this->connect();

            // Insertar usuario

            $stmtU = $this->conn->prepare(
                "INSERT INTO usuarios (codigo_identificacion, profesion, edad, genero, pericia)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmtU->bind_param("isisi",
                $_POST['codigo_identificacion'],
                $_POST['profesion'],
                $_POST['edad'],
                $_POST['genero'],
                $_POST['pericia']
            );

            $stmtU->execute();
            $idUsuario = $stmtU->insert_id;
            $stmtU->close();

            // Insertar resultados

            $stmtR = $this->conn->prepare(
                "INSERT INTO resultados
                (id_usuario, dispositivo, tiempo, completado,
                comentarios_usuario, propuestas, valoracion, respuestas)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $dispositivo = $_POST['dispositivo'];
            $comentarios_usuario = $_POST['comentarios_usuario'] ?? '';
            $propuestas = $_POST['propuestas'] ?? '';
            $valoracion = $_POST['valoracion'];

            $stmtR->bind_param(
                "isdissis",
                $idUsuario,
                $dispositivo,
                $tiempo,
                $completado,
                $comentarios_usuario,
                $propuestas,
                $valoracion,
                $respuestasTexto
            );

            $stmtR->execute();
            $stmtR->close();

            // Insertar comentarios del observador
            $comentarios_observador = $_POST['comentarios_observador'] ?? '';

            $stmtO = $this->conn->prepare(
                "INSERT INTO observaciones (id_usuario, comentarios_observador)
                    VALUES (?, ?)"
            );

            $stmtO->bind_param(
                "is",
                $idUsuario,
                $comentarios_observador
            );

            $stmtO->execute();
            $stmtO->close();

            $this->disconnect();

            echo "<p>Datos de la prueba guardados correctamente.</p>";
        }
    }
?>

<!DOCTYPE HTML>

<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name ="author" content ="Lara Haya Santiago" />
    <meta name ="description" content ="Documento para realizar las pruebas de usabilidad del proyecto" />
    <meta name ="keywords" content ="Test, PHP" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <title>MotoGP-Juegos: Test</title>
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css"/>
    <link rel="icon" href="../multimedia/iconos/juegos.ico" />
</head>

<body>
    <main>
        <h2>Test</h2>

        <?php
            $test = new Test();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['iniciar'])) {
                    $test->iniciarPrueba();
                } elseif (isset($_POST['terminar'])) {
                    $test->terminarPrueba();
                }
                elseif (isset($_POST['guardar'])) {
                    $test->guardarDatos();
                }
            } else {
                $test->mostrarFormularioInicio();
            }
        ?>
    </main>
</body>
</html>