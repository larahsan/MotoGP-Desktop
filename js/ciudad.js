class Ciudad
{
    #nombre;
    #pais;
    #gentilicio;
    #poblacion;
    #longitud;
    #latitud;

    constructor(nombre, pais, gentilicio)
    {
        this.#nombre = nombre;
        this.#pais = pais;
        this.#gentilicio = gentilicio;
    }

    completar()
    {
        this.#poblacion = 6.794;
        this.#longitud = 12.723393899880431;
        this.#latitud = 50.78623461653255;
    }

    getCiudad()
    {
        return this.#nombre;
    }

    getPais()
    {
        return this.#pais;
    }

    getInfo()
    {
        return "<ul> <li>Gentilicio: " + this.#gentilicio + "</li> \
                     <li>Población: " + this.#poblacion + " habitantes</li> </ul>";
    }

    coordenadas()
    {
        /*document.write("<p>Coordenadas: " + this.#latitud + ", " + this.#longitud + "</p>");*/
        let p = document.createElement("p")
        p.textContent = `Coordenadas: ${this.#latitud}, ${this.#longitud}`;
        document.querySelector("section").append(p);
    }

    async #getMeteorologiaCarrera()
    {
        const url = `https://archive-api.open-meteo.com/v1/archive?latitude=50.7823&longitude=12.7079&start_date=2025-07-13&end_date=2025-07-13&daily=sunrise,sunset&hourly=temperature_2m,apparent_temperature,rain,relative_humidity_2m,wind_direction_10m,wind_speed_10m`;

        return $.ajax(
        {
            url: url,
            dataType: 'json',
            method: 'GET'
        });
    }

    async #procesarJSONCarrera()
    {
        var datos = await this.#getMeteorologiaCarrera();

        var datosD = datos.daily; // Datos devueltos en días completos
        var datosH = datos.hourly; // Datos devueltos en fracciones horarias
        var unidadesH = datos.hourly_units; // Unidades

        // Hora de la carrera: 14:00

        var infoTiempo = {
            temperatura2m: `Temperatura a 2 metros del suelo: ${datosH.temperature_2m[14]}${unidadesH.temperature_2m}`,
            sensacion: `Sensación térmica: ${datosH.apparent_temperature[14]}${unidadesH.apparent_temperature}`,
            lluvia: `Lluvia: ${datosH.rain[14]}${unidadesH.rain}`,
            humedad2m: `Humedada relativa a 2 metros del suelo: ${datosH.relative_humidity_2m[14]}${unidadesH.relative_humidity_2m}`,
            velocidadViento: `Velocidad del viento a 10 metros del suelo: ${datosH.wind_speed_10m[14]}${unidadesH.wind_speed_10m}`,
            direccionViento: `Dirección del viento a 10 metros del suelo: ${datosH.wind_direction_10m[14]}${unidadesH.wind_direction_10m}`,
            salidaSol: `Hora de salida del sol: ${datosD.sunrise[0]}`,
            puestaSol: `Hora de puesta del sol: ${datosD.sunset[0]}`
        };

        return infoTiempo;
    }

    async mostrarMeteorologiaCarrera()
    {
        var infoTiempo = await this.#procesarJSONCarrera();

        var section = $("<section>");
        var titulo = $("<h3>").text("Información meteorológica del circuito Sachsenring el día de la carrera");
        section.append(titulo);

        for(let dato in infoTiempo) {
            var p = $("<p>").text(infoTiempo[dato]);
            section.append(p);
        }

        $("main").append(section);
    }

    async #getMeteorologiaEntrenos()
    {
        const url = "https://archive-api.open-meteo.com/v1/archive?latitude=50.7823&longitude=12.7079&start_date=2025-07-11&end_date=2025-07-13&hourly=temperature_2m,rain,wind_speed_10m,relative_humidity_2m"

        return $.ajax(
        {
            url: url,
            dataType: 'json',
            method: 'GET'
        });
    }

    async #procesarJSONEntrenos()
    {
        var data = await this.#getMeteorologiaEntrenos();

        var datosH = data.hourly;
        var unidadesH = data.hourly_units;

        var info = [];

        const variables = {
            temperature_2m: "Temperatura a 2 metros del suelo",
            rain: "Lluvia",
            wind_speed_10m: "Velocidad del viento a 10 metros del suelo",
            relative_humidity_2m: "Humedad relativa a 2 metros del suelo"
        };

        for(let dia = 0; dia < 3; dia++) {
            var inicio = dia * 24; 
            var fin = inicio + 24;

            var fecha = datosH.time[inicio].split("T")[0];
            info.push(fecha);

            for(let key in variables) {
                var valoresDia = datosH[key].slice(inicio, fin);
                var media = (valoresDia.reduce((acc, v) => acc + v, 0) / valoresDia.length).toFixed(2);
                info.push(`${variables[key]}: ${media}${unidadesH[key]}`);
            }
        }

        return info;
    }

    async mostrarMeteorologiaEntrenos()
    {
        var info = await this.#procesarJSONEntrenos();

        var section = $("<section>");
        var titulo = $("<h3>").text("Información meteorológica del circuito Sachsenring para los 3 días de entrenamientos previos a la carrera");
        section.append(titulo);

        for (let dia = 0; dia < 3; dia++) {
            var inicio = dia * 5;
            var fin = inicio + 5;

            var valoresDia = info.slice(inicio, fin);
            var tituloDia = $("<h4>").text(`Día ${dia + 1}: ${valoresDia[0]}`);
            section.append(tituloDia);

            valoresDia.slice(1).forEach(valor => {
                const p = $("<p>").text(valor);
                section.append(p);
            });
        }

        $("main").append(section);
    }
}

let ciudad = new Ciudad("Oberlungwitz", "Alemania", "Oberlungwitzense");
ciudad.completar();

let section = document.createElement("section");
let titulo = document.createElement("h3");
titulo.textContent = "Datos sobre la ciudad más cercana al circuito Sachsenring";
section.append(titulo);
document.querySelector("main").append(section);

let p1 = document.createElement("p");
p1.textContent = `Ciudad: ${ciudad.getCiudad()}`;
section.append(p1);

let p2 = document.createElement("p");
p2.textContent = `País: ${ciudad.getPais()}`;
section.append(p2);

section.innerHTML += ciudad.getInfo();
ciudad.coordenadas();

ciudad.mostrarMeteorologiaCarrera();
ciudad.mostrarMeteorologiaEntrenos();
