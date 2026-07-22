const canvas =
document.getElementById("canvas");

const ctx =
canvas.getContext("2d");

canvas.width =
window.innerWidth;

canvas.height =
window.innerHeight;

document.body.style.margin = "0";

document.body.style.overflow = "hidden";

/* ===== CONSTANTES ===== */

const CX =
canvas.width / 2;

const CY =
canvas.height / 2;

const RADIO_CIRCULO =
Math.min(
    canvas.width,
    canvas.height
) * 0.28;

const RAD_AV =
Math.min(
    canvas.width,
    canvas.height
) * 0.032 + 20;

/* ===== AVATARES ===== */

const iconos = [

    "🎨",
    "🖌️",
    "🎭",
    "🎵",
    "📚",
    "🧵",
    "🖼️",
    "✏️",
    "🎤",
    "🎬",
    "🎪",
    "🎯"

];

const colores = [

    "#6c63ff",
    "#c084fc",
    "#f472b6",
    "#60a5fa",
    "#34d399",
    "#fbbf24",
    "#f97316",
    "#a78bfa",
    "#2dd4bf",
    "#fb923c",
    "#e879f9",
    "#38bdf8"

];

const nombres = [

    "Luna",
    "Sol",
    "Aura",
    "Nova",
    "Iris",
    "Rayo",
    "Lienzo",
    "Pixel",
    "Nota",
    "Verso",
    "Gama",
    "Trazo"

];

const frases = [

    "¡Me encanta tu estilo!",
    "¿Usaste acrílico?",
    "Eso es arte puro 🔥",
    "¿Compartes tu técnica?",
    "Increíble trabajo",
    "Me inspira mucho",
    "Colores perfectos",
    "¿Cuánto tiempo?",
    "Eres talentoso",
    "Quiero aprender eso",
    "Hagamos algo juntos",
    "Espectacular 🎨",
    "¿Dónde aprendiste?",
    "Tienes futuro en esto",
    "Eso merece un premio",
    "Me recuerda a…",
    "La composición es genial",
    "Los detalles son únicos",
    "Felicidades artista ✨",
    "Sigue creando",
    "Precioso trabajo",
    "Me encantaría ver más",
    "Eso es innovador",
    "Transmite mucha paz",
    "Lleno de inspiración",
    "¡Me encanta tu arte!",
    "Mi favorito sin duda",
    "Aprendí algo nuevo hoy",
    "Brilla con luz propia",
    "Eres un grande"

];

/* ===== CREAR AVATARES ===== */

const avatares = [];

for(let i = 0; i < 12; i++){

    const angulo =
    (i / 12) * Math.PI * 2 -
    Math.PI / 2;

    const ax =
    CX +
    Math.cos(angulo) *
    RADIO_CIRCULO;

    const ay =
    CY +
    Math.sin(angulo) *
    RADIO_CIRCULO;

    avatares.push({

        x: ax,
        y: ay,
        baseX: ax,
        baseY: ay,
        icono: iconos[i],
        color: colores[i],
        nombre: nombres[i],
        radio: RAD_AV,
        fase:
        Math.random() *
        Math.PI * 2,
        angulo: angulo,
        mirando: false,
        idx: i

    });

}

/* ===== CONEXIONES ===== */

const conexiones = [];

let conexionInicio = 0;

/* ===== BURBUJAS ===== */

const burbujas = [];

function crearRondaBurbujas(){

    /* Elegir 3-4 avatares aleatorios */
    const cantidad =
    3 + Math.floor(
        Math.random() * 2
    );

    const elegidos = [];

    const disponibles = [
        ...avatares
    ];

    for(
        let i = 0;
        i < cantidad &&
        disponibles.length > 0;
        i++
    ){

        const idx =
        Math.floor(
            Math.random() *
            disponibles.length
        );

        elegidos.push(
            disponibles[idx]
        );

        disponibles.splice(idx, 1);

    }

    /* Cada avatar habla con otro */
    for(
        let i = 0;
        i < elegidos.length;
        i++
    ){

        const emisor =
        elegidos[i];

        const receptor =
        elegidos[
            (i + 1) %
            elegidos.length
        ];

        const frase =
        frases[
            Math.floor(
                Math.random() *
                frases.length
            )
        ];

        const dx =
        receptor.x - emisor.x;

        const dy =
        receptor.y - emisor.y;

        const dist =
        Math.sqrt(
            dx * dx + dy * dy
        );

        const midX =
        (emisor.x + receptor.x) /
        2 +
        (Math.random() - 0.5) *
        dist * 0.15;

        const midY =
        (emisor.y + receptor.y) /
        2 +
        (Math.random() - 0.5) *
        dist * 0.15;

        burbujas.push({

            x: emisor.x,
            y: emisor.y -
            emisor.radio,
            destinoX: midX,
            destinoY: midY,
            texto: frase,
            opacidad: 0,
            progreso: 0,
            vel:
            0.02 +
            Math.random() * 0.015,
            color: emisor.color,
            emisor: emisor.idx,
            receptor: receptor.idx,
            fase:
            Math.random() * 50

        });

    }

}

/* ===== ESTRELLAS ===== */

const estrellas = [];

for(let i = 0; i < 50; i++){

    estrellas.push({

        x:
        Math.random() *
        canvas.width,

        y:
        Math.random() *
        canvas.height,

        radio:
        Math.random() * 1.5 + 0.5,

        opacidad:
        Math.random() * 0.2,

        vel:
        0.005 +
        Math.random() * 0.015

    });

}

/* ===== TIEMPO ===== */

let frame = 0;

let textoOpacidad = 0;

let rondaTimer = 0;

/* ===== DIBUJAR ESTRELLAS ===== */

function dibujarEstrellas(){

    estrellas.forEach(e => {

        e.opacidad +=
        Math.sin(
            frame * e.vel +
            e.x
        ) * 0.002;

        e.opacidad =
        Math.max(
            0,
            Math.min(
                0.3,
                e.opacidad
            )
        );

        ctx.save();

        ctx.globalAlpha =
        e.opacidad;

        ctx.fillStyle =
        "#ffffff";

        ctx.beginPath();

        ctx.arc(
            e.x,
            e.y,
            e.radio,
            0,
            Math.PI * 2
        );

        ctx.fill();

        ctx.restore();

    });

}

/* ===== DIBUJAR CONEXIONES ===== */

function dibujarConexiones(){

    avatares.forEach(a => {

        /* Buscar avatares cercanos */
        avatares.forEach(
            otro => {

            if(
                otro.idx <=
                a.idx
            )
                return;

            const dx =
            a.x - otro.x;

            const dy =
            a.y - otro.y;

            const dist =
            Math.sqrt(
                dx * dx +
                dy * dy
            );

            const umbral =
            RADIO_CIRCULO *
            1.3;

            if(
                dist > umbral
            )
                return;

            const pulso =
            Math.sin(
                frame * 0.015 +
                a.idx + otro.idx
            ) * 0.3 + 0.5;

            const opacity =
            (1 - dist / umbral) *
            pulso * 0.3;

            ctx.save();

            ctx.globalAlpha =
            opacity;

            ctx.strokeStyle =
            "#6c63ff";

            ctx.lineWidth = 1;

            ctx.shadowColor =
            "#6c63ff";

            ctx.shadowBlur = 3;

            ctx.beginPath();

            ctx.moveTo(
                a.x,
                a.y
            );

            ctx.lineTo(
                otro.x,
                otro.y
            );

            ctx.stroke();

            ctx.restore();

        });

    });

}

/* ===== DIBUJAR AVATAR ===== */

function dibujarAvatar(a){

    const pulso =
    Math.sin(
        frame * 0.015 +
        a.fase
    ) * 0.04 + 1;

    const r =
    a.radio * pulso;

    ctx.save();

    ctx.shadowColor =
    a.color;

    ctx.shadowBlur = 12;

    ctx.beginPath();

    ctx.arc(
        a.x,
        a.y,
        r,
        0,
        Math.PI * 2
    );

    ctx.fillStyle =
    "rgba(255,255,255,0.06)";

    ctx.fill();

    ctx.strokeStyle =
    a.color;

    ctx.lineWidth = 2.5;

    ctx.stroke();

    ctx.shadowBlur = 0;

    ctx.textAlign =
    "center";

    ctx.textBaseline =
    "middle";

    ctx.font =
    (r * 0.85) +
    "px Arial";

    ctx.fillText(
        a.icono,
        a.x,
        a.y + 1
    );

    ctx.restore();

}

/* ===== DIBUJAR BURBUJA ===== */

function dibujarBurbuja(b){

    if(b.opacidad <= 0) return;

    const x =
    b.x +
    (b.destinoX - b.x) *
    b.progreso;

    const y =
    b.y +
    (b.destinoY - b.y) *
    b.progreso;

    ctx.save();

    ctx.globalAlpha =
    b.opacidad;

    ctx.font =
    "12px Arial";

    const anchoTexto =
    ctx.measureText(b.texto)
    .width;

    const anchoB =
    anchoTexto + 20;

    const altoB = 32;

    const bx =
    x - anchoB / 2;

    const by =
    y - altoB / 2;

    /* SOMBRA */
    ctx.shadowColor =
    "rgba(0,0,0,0.3)";

    ctx.shadowBlur = 10;

    ctx.shadowOffsetY = 2;

    /* FONDO */
    ctx.fillStyle =
    "rgba(30,30,50,0.85)";

    ctx.beginPath();

    ctx.moveTo(
        bx + 10, by
    );

    ctx.lineTo(
        bx + anchoB - 10,
        by
    );

    ctx.quadraticCurveTo(
        bx + anchoB,
        by,
        bx + anchoB,
        by + 10
    );

    ctx.lineTo(
        bx + anchoB,
        by + altoB - 10
    );

    ctx.quadraticCurveTo(
        bx + anchoB,
        by + altoB,
        bx + anchoB - 10,
        by + altoB
    );

    ctx.lineTo(
        bx + 10,
        by + altoB
    );

    ctx.quadraticCurveTo(
        bx,
        by + altoB,
        bx,
        by + altoB - 10
    );

    ctx.lineTo(
        bx,
        by + 10
    );

    ctx.quadraticCurveTo(
        bx,
        by,
        bx + 10,
        by
    );

    ctx.closePath();

    ctx.fill();

    /* BORDE */
    ctx.shadowBlur = 0;

    ctx.shadowOffsetY = 0;

    ctx.strokeStyle =
    b.color;

    ctx.lineWidth = 1.5;

    ctx.stroke();

    /* TEXTO */
    ctx.fillStyle =
    "#ffffff";

    ctx.textAlign =
    "center";

    ctx.textBaseline =
    "middle";

    ctx.fillText(
        b.texto,
        x,
        y + 1
    );

    ctx.restore();

}

/* ===== DIBUJAR TEXTO ===== */

function dibujarTexto(){

    if(textoOpacidad < 1){

        textoOpacidad += 0.008;

    }

    const tamSoyArte =
    Math.min(
        canvas.width * 0.07,
        72
    );

    const tamComunidad =
    Math.min(
        canvas.width * 0.04,
        40
    );

    const textoY =
    CY;

    ctx.save();

    ctx.globalAlpha =
    textoOpacidad;

    ctx.textAlign =
    "center";

    ctx.textBaseline =
    "middle";

    ctx.shadowColor =
    "#6c63ff";

    ctx.shadowBlur = 25;

    ctx.fillStyle =
    "#ffffff";

    ctx.font =
    "bold " +
    tamSoyArte +
    "px Arial";

    ctx.fillText(
        "SoyArte",
        CX,
        textoY
    );

    ctx.shadowBlur = 12;

    ctx.fillStyle =
    "#c084fc";

    ctx.font =
    "bold " +
    tamComunidad +
    "px Arial";

    ctx.fillText(
        "Comunidad",
        CX,
        textoY +
        tamSoyArte * 0.7
    );

    ctx.restore();

}

/* ===== ANIMACION ===== */

function animate(){

    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    );

    frame++;

    /* ESTRELLAS */
    dibujarEstrellas();

    /* CONEXIONES */
    if(frame > 120){

        dibujarConexiones();

    }

    /* AVATARES */
    avatares.forEach(dibujarAvatar);

    /* BURBUJAS */
    if(frame > 120){

        rondaTimer++;

        if(
            rondaTimer > 60
        ){

            crearRondaBurbujas();

            rondaTimer = 0;

        }

    }

    burbujas.forEach(
        (b, index) => {

            if(
                b.progreso < 1
            ){

                b.progreso +=
                b.vel;

                if(
                    b.progreso > 1
                ){

                    b.progreso = 1;

                }

                b.opacidad +=
                0.03;

                if(
                    b.opacidad > 1
                ){

                    b.opacidad = 1;

                }

            } else {

                /* Flotar y desvanecer */
                b.destinoY -=
                0.15;

                b.destinoX +=
                Math.sin(
                    frame * 0.01 +
                    b.fase
                ) * 0.1;

                b.opacidad -=
                0.004;

            }

            if(b.opacidad <= 0){

                burbujas.splice(
                    index,
                    1
                );

                return;

            }

            dibujarBurbuja(b);

        }
    );

    /* TEXTO */
    if(frame > 80){

        dibujarTexto();

    }

    requestAnimationFrame(
        animate
    );

}

animate();

/* ===== FADE ===== */

setTimeout(()=>{

    document.body.style.transition =
    "opacity 1s";

    document.body.style.opacity =
    "0";

},5500);

/* ===== REDIRECCION ===== */

setTimeout(()=>{

    window.location.href =
    "../foro.php";

},6500);
