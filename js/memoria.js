class Memoria
{
    #primera_carta;
    #segunda_carta;
    #tablero_bloqueado;

    constructor() 
    {
        this.#primera_carta = null;
        this.#segunda_carta = null;

        this.#barajarCartas();
        this.#tablero_bloqueado = false;

        this.cronometro = new Cronometro();
        this.cronometro.arrancar();

        this.#añadirListeners();
    }

    #añadirListeners()
    {
        var cartas = document.querySelectorAll("main article");
        cartas.forEach(carta =>{
            carta.addEventListener("click", () => {this.#voltearCarta(carta)});
        });
    }

    #voltearCarta(carta)
    {
        let estado = carta.getAttribute("data-estado");
        if((estado != "revelada") && (estado != "volteada") && this.#tablero_bloqueado == false) {
            carta.setAttribute("data-estado", "volteada");
            if(this.#primera_carta == null)
                this.#primera_carta = carta;
            else {
                this.#segunda_carta = carta;
                this.#comprobarPareja();
            }
        }        
    }

    #barajarCartas()
    {
        let main = document.querySelector("main");
        let cartas = Array.from(document.querySelectorAll("main article"));
        
        for (let i = cartas.length - 1; i > 0; i--) {
            let j = Math.floor(Math.random() * (i + 1)); // Índice aleatorio
            [cartas[i], cartas[j]] = [cartas[j], cartas[i]];
        }

        cartas.forEach(carta => main.appendChild(carta));
    }

    #reiniciarAtributos()
    {
        this.#primera_carta = null;
        this.#segunda_carta = null;
        this.#tablero_bloqueado = false;
    }

    #deshabilitarCartas()
    {
        this.#primera_carta.setAttribute("data-estado", "revelada")
        this.#segunda_carta.setAttribute("data-estado", "revelada")
        this.#comprobarJuego();
        this.#reiniciarAtributos();
    }

    #comprobarJuego()
    {
        let cartas = document.querySelectorAll('main article');
        
        let terminado = Array.from(cartas).every(
            carta => carta.getAttribute("data-estado") === "revelada"
        );

        if(terminado)
            this.cronometro.parar();
    }

    #cubrirCartas()
    {
        this.#tablero_bloqueado = true;
        
        setTimeout(() =>
        {
            this.#primera_carta.setAttribute("data-estado", null);
            this.#segunda_carta.setAttribute("data-estado", null);
            this.#reiniciarAtributos();
        }, 1500);
    }

    #comprobarPareja()
    {
        let img1 = this.#primera_carta.querySelector("img").getAttribute("src");
        let img2 = this.#segunda_carta.querySelector("img").getAttribute("src");
        img1 == img2 ? this.#deshabilitarCartas() : this.#cubrirCartas();
    }
}