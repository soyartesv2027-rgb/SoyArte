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

/* TEXTO */
let textOpacity = 0;

/* ENTES */
const orbs = [];

/* CLICK */
let clickPulse = 0;

/* CREAR ENTES */
for(let i = 0; i < 70; i++){

    orbs.push({

        x:
        Math.random() *
        canvas.width,

        y:
        Math.random() *
        canvas.height,

        r:
        Math.random()*5+2,

        speed:
        Math.random()*0.8+0.2,

        angle:
        Math.random()*Math.PI*2

    });

}

/* ===== FONDO VR ===== */

function drawBackground(){

    const grad =
    ctx.createRadialGradient(

        canvas.width/2,
        canvas.height/2,
        100,

        canvas.width/2,
        canvas.height/2,
        canvas.width

    );

    grad.addColorStop(
        0,
        "#111827"
    );

    grad.addColorStop(
        1,
        "#000000"
    );

    ctx.fillStyle = grad;

    ctx.fillRect(
        0,
        0,
        canvas.width,
        canvas.height
    );

}

/* ===== GRID FUTURISTA ===== */

function drawGrid(){

    ctx.save();

    ctx.strokeStyle =
    "rgba(0,255,255,0.15)";

    ctx.lineWidth = 1;

    const spacing = 50;

    for(let x = 0;
        x < canvas.width;
        x += spacing){

        ctx.beginPath();

        ctx.moveTo(x,0);

        ctx.lineTo(
            x,
            canvas.height
        );

        ctx.stroke();

    }

    for(let y = 0;
        y < canvas.height;
        y += spacing){

        ctx.beginPath();

        ctx.moveTo(0,y);

        ctx.lineTo(
            canvas.width,
            y
        );

        ctx.stroke();

    }

    ctx.restore();

}

/* ===== ENTES ===== */

function drawOrbs(){

    orbs.forEach(o=>{

        o.x +=
        Math.cos(o.angle) *
        o.speed;

        o.y +=
        Math.sin(o.angle) *
        o.speed;

        /* REBOTAR */
        if(o.x < 0 ||
           o.x > canvas.width){

            o.angle =
            Math.PI - o.angle;

        }

        if(o.y < 0 ||
           o.y > canvas.height){

            o.angle =
            -o.angle;

        }

        /* BRILLO */
        const glow =
        ctx.createRadialGradient(

            o.x,
            o.y,
            0,

            o.x,
            o.y,
            o.r*6

        );

        glow.addColorStop(
            0,
            "rgba(0,255,255,0.9)"
        );

        glow.addColorStop(
            1,
            "rgba(0,255,255,0)"
        );

        ctx.fillStyle = glow;

        ctx.beginPath();

        ctx.arc(

            o.x,

            o.y,

            o.r*6,

            0,

            Math.PI*2

        );

        ctx.fill();

        /* NUCLEO */
        ctx.fillStyle =
        "#00ffff";

        ctx.beginPath();

        ctx.arc(

            o.x,

            o.y,

            o.r,

            0,

            Math.PI*2

        );

        ctx.fill();

    });

}

/* ===== CLICK NINTENDO ===== */

function drawClick(){

    clickPulse += 0.05;

    const pulse =
    Math.sin(clickPulse)*6;

    ctx.save();

    ctx.translate(
        canvas.width/2,
        canvas.height/2
    );

    /* CIRCULO */
    ctx.strokeStyle =
    "#00ffff";

    ctx.lineWidth = 4;

    ctx.shadowColor =
    "#00ffff";

    ctx.shadowBlur = 20;

    ctx.beginPath();

    ctx.arc(
        0,
        0,
        90 + pulse,
        0,
        Math.PI*2
    );

    ctx.stroke();

    /* CURSOR REAL */
    ctx.fillStyle =
    "#ffffff";

    ctx.beginPath();

    ctx.moveTo(-12,-35);

    ctx.lineTo(38,5);

    ctx.lineTo(15,10);

    ctx.lineTo(28,42);

    ctx.lineTo(12,48);

    ctx.lineTo(0,15);

    ctx.lineTo(-20,32);

    ctx.closePath();

    ctx.fill();

    ctx.restore();

}

/* ===== TEXTO ===== */

function drawText(){

    textOpacity += 0.008;

    ctx.save();

    ctx.globalAlpha =
    textOpacity;

    ctx.textAlign =
    "center";

    ctx.textBaseline =
    "middle";

    /* BRILLO */
    ctx.shadowColor =
    "#00ffff";

    ctx.shadowBlur = 20;

    /* TITULO */
    ctx.fillStyle =
    "#ffffff";

    ctx.font =
    "bold 120px Arial";

    ctx.fillText(

        "SoyArte",

        canvas.width/2,

        canvas.height/2 - 40

    );

    /* SUB */
    ctx.fillStyle =
    "#00ffff";

    ctx.font =
    "bold 60px Arial";

    ctx.fillText(

        "Realidad Virtual",

        canvas.width/2,

        canvas.height/2 + 70

    );

    ctx.restore();

}

/* ===== PARTICULAS ===== */

function drawLines(){

    ctx.strokeStyle =
    "rgba(0,255,255,0.08)";

    for(let i = 0;
        i < orbs.length;
        i++){

        for(let j = i+1;
            j < orbs.length;
            j++){

            const dx =
            orbs[i].x - orbs[j].x;

            const dy =
            orbs[i].y - orbs[j].y;

            const dist =
            Math.sqrt(dx*dx + dy*dy);

            if(dist < 120){

                ctx.beginPath();

                ctx.moveTo(

                    orbs[i].x,

                    orbs[i].y

                );

                ctx.lineTo(

                    orbs[j].x,

                    orbs[j].y

                );

                ctx.stroke();

            }

        }

    }

}

/* ===== ANIMACION ===== */

function animate(){

    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    );

    drawBackground();

    drawGrid();

    drawLines();

    drawOrbs();

    drawClick();

    drawText();

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
    "../realidad.php";

},5000);