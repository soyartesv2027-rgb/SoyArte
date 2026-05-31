const canvas =
document.getElementById("canvas");

const ctx =
canvas.getContext("2d");

/* FULLSCREEN */
canvas.width =
window.innerWidth;

canvas.height =
window.innerHeight;

/* BODY */
document.body.style.margin = "0";

document.body.style.overflow = "hidden";

/* CARRITO */
let cartX = -300;

const cartY =
canvas.height/2 + 50;

/* OBJETOS */
const items = [];

/* TEXTO */
let textOpacity = 0;

/* ICONOS */
const emojis = [

    "🎨",
    "🖌️",
    "🖼️",
    "🎵",
    "📚",
    "🧵",
    "✨",
    "🛍️"

];

/* ===== CREAR OBJETOS ===== */

function createItem(){

    items.push({

        x:
        cartX + 120,

        y:
        cartY - 80,

        vx:
        Math.random()*4-2,

        vy:
        Math.random()*-8-2,

        size:
        Math.random()*25+20,

        emoji:
        emojis[
            Math.floor(
                Math.random()*emojis.length
            )
        ],

        rotation:
        Math.random()*360,

        alpha:1

    });

}

/* ===== FONDO ===== */

function drawBackground(){

    /* SUELO */
    ctx.fillStyle =
    "#d1d5db";

    ctx.fillRect(

        0,

        canvas.height - 120,

        canvas.width,

        120

    );

    /* LINEAS */
    ctx.strokeStyle =
    "rgba(0,0,0,0.08)";

    ctx.lineWidth = 2;

    for(let x = 0;
        x < canvas.width;
        x += 80){

        ctx.beginPath();

        ctx.moveTo(
            x,
            canvas.height - 120
        );

        ctx.lineTo(
            x+40,
            canvas.height
        );

        ctx.stroke();

    }

}

/* ===== CARRITO ===== */

function drawCart(){

    ctx.save();

    ctx.translate(
        cartX,
        cartY
    );

    /* SOMBRA */
    ctx.shadowColor =
    "rgba(0,0,0,0.2)";

    ctx.shadowBlur = 15;

    /* BASE */
    ctx.strokeStyle =
    "#374151";

    ctx.lineWidth = 8;

    ctx.beginPath();

    ctx.moveTo(0,0);

    ctx.lineTo(140,0);

    ctx.lineTo(110,-80);

    ctx.lineTo(20,-80);

    ctx.stroke();

    /* MANGO */
    ctx.beginPath();

    ctx.moveTo(0,0);

    ctx.lineTo(-30,-40);

    ctx.stroke();

    /* RUEDAS */
    ctx.fillStyle =
    "#111827";

    ctx.beginPath();

    ctx.arc(
        30,
        25,
        18,
        0,
        Math.PI*2
    );

    ctx.fill();

    ctx.beginPath();

    ctx.arc(
        105,
        25,
        18,
        0,
        Math.PI*2
    );

    ctx.fill();

    /* DETALLES */
    ctx.fillStyle =
    "#60a5fa";

    for(let i = 0; i < 5; i++){

        ctx.fillRect(

            20 + i*18,

            -70,

            6,

            65

        );

    }

    ctx.restore();

}

/* ===== OBJETOS ===== */

function drawItems(){

    items.forEach((i,index)=>{

        i.x += i.vx;

        i.y += i.vy;

        i.vy += 0.2;

        i.rotation += 4;

        i.alpha -= 0.01;

        ctx.save();

        ctx.translate(
            i.x,
            i.y
        );

        ctx.rotate(
            i.rotation *
            Math.PI/180
        );

        ctx.globalAlpha =
        i.alpha;

        ctx.font =
        `${i.size}px Arial`;

        ctx.fillText(
            i.emoji,
            0,
            0
        );

        ctx.restore();

        if(i.alpha <= 0){

            items.splice(index,1);

        }

    });

}

/* ===== TEXTO ===== */

function drawText(){

    if(textOpacity < 1){

        textOpacity += 0.01;

    }

    ctx.save();

    ctx.globalAlpha =
    textOpacity;

    ctx.textAlign =
    "center";

    ctx.textBaseline =
    "middle";

    /* BRILLO */
    ctx.shadowColor =
    "#60a5fa";

    ctx.shadowBlur = 20;

    /* TITULO */
    ctx.fillStyle =
    "#111827";

    ctx.font =
    "bold 120px Arial";

    ctx.fillText(

        "SoyArte",

        canvas.width/2,

        canvas.height/2 - 40

    );

    /* SUB */
    ctx.fillStyle =
    "#2563eb";

    ctx.font =
    "bold 70px Arial";

    ctx.fillText(

        "Shop",

        canvas.width/2,

        canvas.height/2 + 60

    );

    ctx.restore();

}

/* ===== VELOCIDAD ===== */

let speed = 10;

/* ===== ANIMACION ===== */

function animate(){

    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    );

    drawBackground();

    /* MOVER */
    if(cartX < canvas.width + 300){

        cartX += speed;

        /* OBJETOS */
        if(Math.random() < 0.4){

            createItem();

        }

    }

    /* TEXTO */
    if(cartX > canvas.width*0.25){

        drawText();

    }

    /* OBJETOS */
    drawItems();

    /* CARRITO */
    drawCart();

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

},4000);

/* ===== REDIRECCION ===== */

setTimeout(()=>{

    window.location.href =
    "../php/tienda.php";

},5000);