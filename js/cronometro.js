class Cronometro
{
    #tiempo;
    #inicio;
    #corriendo;

    constructor()
    {
        this.#tiempo = 0;
    }

    añadirListeners()
    {
        var botones = document.querySelectorAll("main button");

        botones[0].addEventListener("click", this.arrancar.bind(this));
        botones[1].addEventListener("click", this.parar.bind(this));
        botones[2].addEventListener("click", this.#reiniciar.bind(this));
    }

    arrancar()
    {
        try { 
            this.#inicio = Temporal.Now.instant().epochMilliseconds;
        }
        catch(err) {
            this.#inicio = Date.now();
        }

        this.#corriendo = setInterval(this.#actualizar.bind(this), 100);
    }

    #actualizar()
    {
        try { 
            this.#tiempo = Temporal.Now.instant().epochMilliseconds - this.#inicio;
        }
        catch(err) {
            this.#tiempo = Date.now() - this.#inicio;
        }

        this.#mostrar();
    }

    #mostrar()
    {
        let minutos = parseInt(this.#tiempo / 60000);
        let segundos = parseInt((this.#tiempo % 60000) / 1000);
        let decimas = parseInt((this.#tiempo % 1000) / 100);
        let parrafo = `${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}.${decimas}`;
        document.querySelector('main > p').textContent = parrafo;
    }

    parar()
    {
        clearInterval(this.#corriendo);
    }

    #reiniciar()
    {
        this.parar();
        this.#tiempo = 0;
        this.#mostrar();
    }
}