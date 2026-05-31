const canvas =
document.getElementById("canvas");

const ctx =
canvas.getContext("2d");

/* FULLSCREEN */
canvas.width =
window.innerWidth;

canvas.height =
window.innerHeight;

/* FONDO */
document.body.style.background =
"#fffaf0";

document.body.style.margin = "0";

document.body.style.overflow = "hidden";

/* TIJERAS */
let scissorsX = -250;

let scissorsY =
canvas.height / 2;

/* CORTE */
let cutProgress = 0;

/* PARTICULAS */
const paperPieces = [];

/* TEXTO */
let textOpacity = 0;

/* ===== CREAR PAPEL ===== */

function createPaper(x,y){

    paperPieces.push({

        x,
        y,

        size:
        Math.random()*10+4,

        vx:
        (Math.random()-0.5)*6,

        vy:
        Math.random()*-4,

        rotation:
        Math.random()*360,

        alpha:1

    });

}

/* ===== TIJERAS ===== */

function drawScissors(){

    ctx.save();

    ctx.translate(
        scissorsX,
        scissorsY
    );

    ctx.rotate(
        15 * Math.PI/180
    );

    /* MANGO */
    ctx.strokeStyle =
    "#ef4444";

    ctx.lineWidth = 10;

    ctx.beginPath();

    ctx.arc(
        -40,
        -20,
        20,
        0,
        Math.PI*2
    );

    ctx.stroke();

    ctx.beginPath();

    ctx.arc(
        -40,
        20,
        20,
        0,
        Math.PI*2
    );

    ctx.stroke();

    /* HOJAS */
    ctx.strokeStyle =
    "#9ca3af";

    ctx.lineWidth = 8;

    ctx.beginPath();

    ctx.moveTo(-10,0);

    ctx.lineTo(140,-30);

    ctx.stroke();

    ctx.beginPath();

    ctx.moveTo(-10,0);

    ctx.lineTo(140,30);

    ctx.stroke();

    /* CENTRO */
    ctx.fillStyle =
    "#444";

    ctx.beginPath();

    ctx.arc(
        -10,
        0,
        8,
        0,
        Math.PI*2
    );

    ctx.fill();

    ctx.restore();

}

/* ===== LINEA DE CORTE ===== */

function drawCut(){

    ctx.strokeStyle =
    "#d97706";

    ctx.lineWidth = 5;

    ctx.setLineDash([12,12]);

    ctx.beginPath();

    ctx.moveTo(
        0,
        canvas.height/2
    );

    for(let x = 0; x <= cutProgress; x += 15){

        ctx.lineTo(

            x,

            canvas.height/2 +

            Math.sin(x*0.02)*20

        );

    }

    ctx.stroke();

    ctx.setLineDash([]);

}

/* ===== PAPEL RECORTADO ===== */

function drawPaper(){

    ctx.fillStyle =
    "#fef3c7";

    ctx.beginPath();

    ctx.moveTo(
        0,
        canvas.height/2 - 120
    );

    for(let x = 0; x <= cutProgress; x += 20){

        ctx.lineTo(

            x,

            canvas.height/2 - 120 +

            Math.sin(x*0.02)*20

        );

    }

    ctx.lineTo(
        cutProgress,
        canvas.height/2 + 120
    );

    ctx.lineTo(
        0,
        canvas.height/2 + 120
    );

    ctx.closePath();

    ctx.fill();

}

/* ===== PEDAZOS ===== */

function drawPieces(){

    paperPieces.forEach((p,index)=>{

        p.x += p.vx;

        p.y += p.vy;

        p.vy += 0.1;

        p.rotation += 4;

        p.alpha -= 0.015;

        ctx.save();

        ctx.translate(
            p.x,
            p.y
        );

        ctx.rotate(
            p.rotation *
            Math.PI/180
        );

        ctx.fillStyle =

        `rgba(245,158,11,${p.alpha})`;

        ctx.fillRect(

            -p.size/2,

            -p.size/2,

            p.size,

            p.size

        );

        ctx.restore();

        if(p.alpha <= 0){

            paperPieces.splice(index,1);

        }

    });

}

/* ===== TEXTO ===== */

function drawText(){

    textOpacity += 0.01;

    ctx.save();

    ctx.globalAlpha =
    textOpacity;

    ctx.fillStyle =
    "#111";

    ctx.textAlign =
    "center";

    ctx.textBaseline =
    "middle";

    /* SOMBRA */
    ctx.shadowColor =
    "rgba(0,0,0,0.2)";

    ctx.shadowBlur = 10;

    /* TITULO */
    ctx.font =
    "bold 120px Arial";

    ctx.fillText(

        "SoyArte",

        canvas.width/2,

        canvas.height/2 - 20

    );

    /* SUB */
    ctx.font =
    "bold 70px Arial";

    ctx.fillText(

        "Manualidades",

        canvas.width/2,

        canvas.height/2 + 90

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

    /* AVANZAR */
    if(cutProgress < canvas.width + 200){

        cutProgress += 12;

        scissorsX += 12;

        /* PAPELITOS */
        if(Math.random() < 0.7){

            createPaper(

                scissorsX + 120,

                scissorsY

            );

        }

    }

    /* PAPEL */
    drawPaper();

    /* LINEA */
    drawCut();

    /* TEXTO */
    if(cutProgress > canvas.width*0.45){

        drawText();

    }

    /* PEDAZOS */
    drawPieces();

    /* TIJERAS */
    drawScissors();

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
    "manualidades.php";

},5000);