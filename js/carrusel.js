class Carrusel
{
    #busqueda;
    #actual;
    #maximo;

    constructor()
    {
        this.#busqueda = "Sachsenring,MotoGP";
        this.#actual = 0;
        this.#maximo = 4;
    }

    async #getFotografias()
    {
        return $.getJSON("https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?", 
        {
            tags: this.#busqueda,
            tagmode: "any",
            format: "json"
        });
    }

    async #procesarJSONFotografias()
    {
        var jsonFotos = await this.#getFotografias();
        var fotos = [];

        for (var item of jsonFotos.items) {
            var urlFoto = item.media.m.replace('_m', '_z');
            fotos.push({
                titulo: item.title,
                url: urlFoto,
                descripcion: item.description
            });
            if (fotos.length === this.#maximo) break;
        }
        return fotos;
    }

    async mostrarFotografias()
    {
        var fotos = await this.#procesarJSONFotografias();
        var primeraFoto = fotos[0];
        
        var main = $("<main>");
        $("body").append(main);

        var article = $("<article>");
        var titulo = $("<h2>").text("Imágenes del circuito de Sachsenring");
        var imagen = $("<img>")
            .attr("src", primeraFoto.url)
            .attr("alt", primeraFoto.titulo);

        article.append(titulo, imagen);
        main.append(article);

        setInterval(this.#cambiarFotografia.bind(this), 3000);
    }

    async #cambiarFotografia()
    {
        var fotos = await this.#procesarJSONFotografias();

        if(this.#actual === this.#maximo)
            this.#actual = 0;
        else
            this.#actual++;

        $("article img").attr("src", fotos[this.#actual].url)
                             .attr("alt", fotos[this.#actual].titulo);
    }
}