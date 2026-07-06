
const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");

function resize(){

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

}

resize();

window.addEventListener("resize", resize);



// ======================================================
// CONFIG
// ======================================================

const angle = 25 * Math.PI / 180;

let progress = 0;

const speed = 0.008;

const centerX = canvas.width / 2;
const centerY = canvas.height / 2;

const travelDistance = canvas.height * 1.2;

const startX =
    centerX - Math.sin(angle) * travelDistance;

const startY =
    centerY - Math.cos(angle) * travelDistance;

const particles = [];

let fadeStarted = false;



// ======================================================
// BRUSH POSITION
// ======================================================

function getBrushPosition(){

    const distance = progress * travelDistance;

    return{

        x : startX + Math.sin(angle) * distance,
        y : startY + Math.cos(angle) * distance

    };

}



// ======================================================
// BACKGROUND
// ======================================================

function drawBackground(){

    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0,0,canvas.width,canvas.height);

}



// ======================================================
// TEXT
// ======================================================

function drawText(){

    const pos = getBrushPosition();

    ctx.save();

    // SOLO mostrar texto cuando el pincel llegue al centro

    const reveal =
        Math.min(
            1,
            Math.max(
                0,
                (progress - 0.35) * 5
            )
        );

    ctx.globalAlpha = reveal;

    // TITULO

    ctx.font =
        `bold ${Math.min(canvas.width * 0.12,150)}px Brush Script MT`;

    ctx.textAlign = "center";
    ctx.textBaseline = "middle";

    ctx.fillStyle = "#000000";

    ctx.shadowColor = "rgba(0,0,0,0.18)";
    ctx.shadowBlur = 12;

    ctx.fillText(
        "SoyArte",
        canvas.width / 2,
        canvas.height / 2 - 20
    );



    // SUBTITULO

    ctx.font =
        `italic ${Math.min(canvas.width * 0.04,55)}px Georgia`;

    ctx.fillStyle = "rgba(0,0,0,0.72)";

    ctx.fillText(
        "Pintura",
        canvas.width / 2,
        canvas.height / 2 + 70
    );

    ctx.restore();

}



// ======================================================
// BLUE PAINT
// ======================================================

function drawPaint(){

    const pos = getBrushPosition();

    ctx.save();

    ctx.translate(pos.x,pos.y);

    ctx.rotate(angle);

    const gradient =
        ctx.createLinearGradient(-420,0,420,0);

    gradient.addColorStop(0,"#1e3a8a");
    gradient.addColorStop(0.4,"#2563eb");
    gradient.addColorStop(0.7,"#3b82f6");
    gradient.addColorStop(1,"#93c5fd");

    ctx.fillStyle = gradient;

    ctx.shadowColor = "#2563eb";
    ctx.shadowBlur = 28;

    ctx.beginPath();

    ctx.moveTo(-380,-130);

    for(let i=-380;i<=380;i+=14){

        const wave =
            Math.sin(i * 0.04 + progress * 12) * 18 +
            Math.cos(i * 0.02) * 10;

        ctx.lineTo(i,-130 + wave);

    }

    for(let i=380;i>=-380;i-=14){

        const wave =
            Math.cos(i * 0.05 + progress * 12) * 18 +
            Math.sin(i * 0.03) * 10;

        ctx.lineTo(i,130 + wave);

    }

    ctx.closePath();

    ctx.fill();



    // TEXTURE

    for(let i=0;i<35;i++){

        ctx.globalAlpha = 0.08;

        ctx.fillStyle = "#0f172a";

        ctx.beginPath();

        ctx.arc(

            (Math.random()-0.5)*700,

            (Math.random()-0.5)*180,

            Math.random()*20,

            0,

            Math.PI*2

        );

        ctx.fill();

    }

    ctx.restore();

}



// ======================================================
// PARTICLES
// ======================================================

function createParticles(){

    const pos = getBrushPosition();

    if(
        pos.y > canvas.height/2 - 160 &&
        pos.y < canvas.height/2 + 160
    ){

        for(let i=0;i<5;i++){

            particles.push({

                x : pos.x + (Math.random()-0.5)*120,

                y : pos.y + (Math.random()-0.5)*50,

                vx : (Math.random()-0.5)*6,

                vy : Math.random()*-5,

                size : Math.random()*10 + 3,

                alpha : 1

            });

        }

    }

}



function drawParticles(){

    for(let i=particles.length-1;i>=0;i--){

        const p = particles[i];

        p.x += p.vx;

        p.y += p.vy;

        p.vy += 0.16;

        p.alpha -= 0.018;

        if(p.alpha <= 0){

            particles.splice(i,1);
            continue;

        }

        ctx.globalAlpha = p.alpha;

        ctx.fillStyle = "#000000";

        ctx.beginPath();

        ctx.arc(
            p.x,
            p.y,
            p.size,
            0,
            Math.PI * 2
        );

        ctx.fill();

        ctx.globalAlpha = 1;

    }

}



// ======================================================
// BRUSH
// ======================================================

function drawBrush(){

    const pos = getBrushPosition();

    ctx.save();

    ctx.translate(pos.x,pos.y);

    ctx.rotate(angle + Math.PI / 2);



    // HANDLE

    const wood =
        ctx.createLinearGradient(-250,0,0,0);

    wood.addColorStop(0,"#4a2c16");
    wood.addColorStop(0.5,"#8b5a2b");
    wood.addColorStop(1,"#d6a26b");

    ctx.fillStyle = wood;

    ctx.shadowColor = "rgba(0,0,0,0.3)";
    ctx.shadowBlur = 14;

    ctx.beginPath();

    ctx.roundRect(-250,-13,250,26,13);

    ctx.fill();



    // METAL

    const metal =
        ctx.createLinearGradient(0,-35,70,35);

    metal.addColorStop(0,"#ffffff");
    metal.addColorStop(0.5,"#9ca3af");
    metal.addColorStop(1,"#374151");

    ctx.fillStyle = metal;

    ctx.beginPath();

    ctx.roundRect(0,-35,70,70,12);

    ctx.fill();



    // BRISTLES

    const bristles =
        ctx.createLinearGradient(70,0,220,0);

    bristles.addColorStop(0,"#1d4ed8");
    bristles.addColorStop(0.5,"#2563eb");
    bristles.addColorStop(1,"#93c5fd");

    ctx.fillStyle = bristles;

    ctx.shadowColor = "#2563eb";
    ctx.shadowBlur = 20;

    ctx.beginPath();

    ctx.moveTo(70,-45);

    ctx.quadraticCurveTo(220,-20,230,0);

    ctx.quadraticCurveTo(220,20,70,45);

    ctx.closePath();

    ctx.fill();



    // HAIRS

    for(let i=0;i<28;i++){

        ctx.strokeStyle =
            "rgba(255,255,255,0.22)";

        ctx.lineWidth = 1;

        ctx.beginPath();

        ctx.moveTo(
            80 + i*5,
            -25 + Math.random()*50
        );

        ctx.lineTo(
            220,
            -15 + Math.random()*30
        );

        ctx.stroke();

    }

    ctx.restore();

}



// ======================================================
// ANIMATION
// ======================================================

function animate(){

    ctx.clearRect(0,0,canvas.width,canvas.height);

    drawBackground();

    drawPaint();

    drawText();

    createParticles();

    drawParticles();

    drawBrush();

    progress += speed;



    // FADE OUT

    if(progress >= 1 && !fadeStarted){

        fadeStarted = true;

        setTimeout(()=>{

            document.body.style.opacity = "0";

        },1200);

        setTimeout(()=>{

            window.location.href = "soyarte/soyarte/pinturas.php";

        },2600);

    }

    requestAnimationFrame(animate);

}

animate();
