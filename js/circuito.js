class Circuito
{
    constructor()
    {
        this.#comprobarApiFile();

        var inputHTML = document.querySelectorAll("input")[0];
        inputHTML.addEventListener('change', (evento) => this.#leerArchivoHTML(evento));
    }

    #comprobarApiFile()
    {
        if(!(window.File && window.FileReader && window.FileList && window.Blob))
        {
            let p = document.createElement("p");
            p.textContent = "Este navegador no soporta el API File, por lo que no se podrán cargar archivos.";
            document.querySelector("main").append(p);
        }
    }

    #leerArchivoHTML(evento)
    {
        var archivo = evento.target.files[0];
        
        if (archivo && archivo.type.match(/text.*/)) 
        {
            var lector = new FileReader();
            lector.onload = (e) => this.#insertarHTML(e.target.result);
            lector.readAsText(archivo);
        }
        else {
            let p = document.createElement("p");
            p.textContent = "Archivo no válido.";
            document.querySelector("main").append(p);
        }
    }

    #insertarHTML(contenido)
    {
        var parser = new DOMParser();
        var xmlDoc = parser.parseFromString(contenido, "text/html");

        var mainXML = xmlDoc.querySelector("main");
        var main = document.querySelector("main");

        // Corregir las rutas de los archivos multimedia

        mainXML.querySelectorAll("img").forEach(img => {
            img.setAttribute('src', img.getAttribute('src').replace("../", ""));
        });

        mainXML.querySelectorAll("video").forEach(video => {
            video.setAttribute('src', video.getAttribute('src').replace("../", ""));
        });

        // Añadir el contenido del main

        mainXML.querySelectorAll("section").forEach(section => {
            main.appendChild(section.cloneNode(true));
        });
        
    }
}

class CargadorSVG
{
    constructor()
    {
        var inputSVG = document.querySelectorAll("input")[1];
        inputSVG.addEventListener('change', (evento) => this.#leerArchivoSVG(evento));
    }

    #leerArchivoSVG(evento)
    {
        var archivo = evento.target.files[0];
        
        if (archivo && archivo.type === "image/svg+xml") 
        {
            var lector = new FileReader();
            lector.onload = (e) => this.#insertarSVG(e.target.result);
            lector.readAsText(archivo);
        }
        else {
            let p = document.createElement("p");
            p.textContent = "Archivo no válido.";
            document.querySelector("main").append(p);
        }
    }

    #insertarSVG(contenido)
    {
        var parser = new DOMParser();
        var documentoSVG = parser.parseFromString(contenido, 'image/svg+xml');
        var elementoSVG = documentoSVG.documentElement;
        
        elementoSVG.setAttribute("version", "1.1");  

        var main = document.querySelector("main");

        var copiaSVG = document.importNode(elementoSVG, true); // Para evitar errores
        main.appendChild(copiaSVG);
    }
}

class CargadorKML
{
    constructor()
    {
        var inputKML = document.querySelectorAll("input")[2];
        inputKML.addEventListener('change', (evento) => this.#leerArchivoKML(evento));
    }

    #leerArchivoKML(evento)
    {
        var archivo = evento.target.files[0];
        
        if (archivo && archivo.name.endsWith(".kml")) 
        {
            var lector = new FileReader();

            lector.onload = (e) => {
                var parser = new DOMParser();
                var xmlDoc = parser.parseFromString(e.target.result, "text/xml");
                
                // Punto de origen (primer punto)
                
                var placemarks = xmlDoc.getElementsByTagName("Placemark");
                var point = placemarks[0].getElementsByTagName("Point")[0];
                var coordinatesElem = point.getElementsByTagName("coordinates")[0];

                var coordsPunto = coordinatesElem.textContent.trim().split(",");
                var puntoOrigen = {
                    lng: coordsPunto[0],
                    lat: coordsPunto[1]
                }

                // Puntos que definen los tramos del circuito
                
                var lineStringElem = xmlDoc.getElementsByTagName("LineString")[0];
                var coordsElem = lineStringElem.getElementsByTagName("coordinates")[0];

                var coordsTramos = coordsElem.textContent.trim().split("\n");
                var tramosCircuito = coordsTramos.map(tramos => {
                    var coords = tramos.trim().split(",");
                    return {
                        lng: coords[0],
                        lat: coords[1]
                    };
                });

                this.#insertarCapaKML(puntoOrigen, tramosCircuito);
            }

            lector.readAsText(archivo);
        }
        else {
            let p = document.createElement("p");
            p.textContent = "Archivo no válido.";
            document.querySelector("main").append(p);
        }
    }

    #insertarCapaKML(puntoOrigen, tramosCircuito)
    {
        /* IMPORTANTE: el código que crea MapBox al cargar el mapa genera un error de accesibilidad al validarlo con la herramienta Wave. Al tratarse de un servicio externo, este error no puede ser corregido.*/

        // Cargar el mapa

        mapboxgl.accessToken = 'pk.eyJ1IjoidW8yODk2ODQiLCJhIjoiY21pMGk1aHRvMHhwNjJsc2Nubnk0ZnYwNyJ9.-ZTqmC4zBk9v25ZQoLtL7Q'; // This project uses a public Mapbox token restricted by domain for security.

        var div = document.createElement("div");
        var main = document.querySelector("main");
        main.append(div);

        const map = new mapboxgl.Map({
            container: div,
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [12.68920027336592, 50.791838272981984], // Coordenadas del centro del circuito
            zoom: 14.5
        });

        // Marcador del punto de origen

        new mapboxgl.Marker({ color: 'red' })
        .setLngLat([puntoOrigen.lng, puntoOrigen.lat])
        .addTo(map);

        // Polilínea con los tramos del circuito

        const coordinates = tramosCircuito.map(tramo => [tramo.lng, tramo.lat]);

        const geojson = {
            "type": "Feature",
            "geometry": {
                "type": "LineString",
                "coordinates": coordinates
            }
        };

        function añadirPoliLinea()
        {
            map.addSource('ruta-circuito', {
                "type": "geojson",
                "data": geojson
            });

            map.addLayer({
                "id": "linea-circuito",
                "type": "line",
                "source": "ruta-circuito",
                "layout": {
                    "line-join": "round",
                    "line-cap": "round"
                },
                "paint": {
                    "line-color": "#FF0000",
                    "line-width": 4
                }
            });
        }

        if (map.loaded())
            añadirPoliLinea();
        else
            map.on("load", añadirPoliLinea);
    }
}
