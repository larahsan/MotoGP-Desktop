class Noticias
{
    #busqueda;
    #url;
    
    constructor()
    {
        this.#busqueda = "Campeonato del mundo de MotoGP";
        this.#url = "https://api.thenewsapi.com/v1/news/all?api_token=JmxavZrI0BiBIqySweeLVtE096NaGKKRaWAZBX7M&search="
    }
    
    async #buscar()
    {
        try {
            return fetch(this.#url + this.#busqueda).then(response => response.json());
        }
        catch(error) {
            console.error("Error al cargar noticias: ", error.message);
        }
    }

    async #procesarInformacion()
    {
        var resultados = await this.#buscar();
        resultados = resultados.data;
        var noticias = [];

        resultados.forEach(noticia => {
            noticias.push(noticia.title);
            noticias.push(noticia.snippet);
            noticias.push(noticia.url);
            noticias.push(noticia.source);
        });

        return noticias;
    }

    async mostrarNoticias()
    {
        var noticias = await this.#procesarInformacion();

        var section = $("<section>");
        var tituloSeccion = $("<h2>").text("Noticias sobre el campeonato del mundo de MotoGP");
        section.append(tituloSeccion);

        for (let i = 0; i < noticias.length; i += 4) {
            let titulo = noticias[i];
            let entradilla = noticias[i + 1];
            let enlace = noticias[i + 2];
            let fuente = noticias[i + 3];

            let h4 = $("<h3>").text(titulo);
            let pEntradilla = $("<p>").text(entradilla);
            let pFuente = $("<p>").text(`Fuente: ${fuente}`);
            let aEnlace = $("<a>").attr("href", enlace).attr("title", titulo).text("Enlace");

            section.append(h4, pEntradilla, pFuente, aEnlace);
        }

        $("main").append(section);
    }
}