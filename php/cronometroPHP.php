<!DOCTYPE HTML>

<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name ="author" content ="Lara Haya Santiago" />
    <meta name ="description" content ="Documento con el juego del cronómetro de PHP" />
    <meta name ="keywords" content ="Cronómetro, PHP" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <title>MotoGP-Juegos: Cronómetro PHP</title>
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css"/>
    <link rel="icon" href="../multimedia/iconos/juegos.ico" />
</head>

<body>
    <header>
        <h1><a href="index.html" title="Inicio">MotoGP Desktop</a></h1>

        <nav>
            <a href="../index.html" title="Inicio">Inicio</a>
            <a href="../piloto.html" title="Piloto">Piloto</a>
            <a href="../circuito.html" title="Circuito">Circuito</a>
            <a href="../meteorologia.html" title="Meteorología">Meteorología</a>
            <a href="../clasificaciones.php" title="Clasificaciones">Clasificaciones</a>
            <a href="../juegos.html" title="Juegos" class="active">Juegos</a>
            <a href="../ayuda.html" title="Ayuda">Ayuda</a>
        </nav>
    </header>

    <p>Estás en: <a href="../index.html" title="Inicio">Inicio</a> >> <a href="../juegos.html" title="Juegos">Juegos</a> >> <strong>Cronómetro PHP</strong></p>

    <main>
        <h2>Cronómetro PHP</h2>

        <form action='#' method='post' name='botones'>
            <input type = 'submit' name = 'arrancar' value = 'Arrancar'/>
            <input type = 'submit' name = 'parar' value = 'Parar'/>
            <input type = 'submit' name = 'mostrar' value = 'Mostrar tiempo'/>
        </form>
    </main>

    <?php
        include('cronometro.php');

        if (count($_POST)>0) 
        {   
            $cronometro = new Cronometro();

            if(isset($_POST['arrancar'])) $cronometro->arrancar();
            if(isset($_POST['parar'])) $cronometro->parar();
            if(isset($_POST['mostrar'])) $cronometro->mostrar();
        }
    ?>
    
</body>
</html>