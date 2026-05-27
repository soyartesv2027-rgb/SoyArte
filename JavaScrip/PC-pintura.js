const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

document.body.style.margin = "0";
document.body.style.overflow = "hidden";
document.body.style.background = "#ffffff";

let brushX = -350;
let brushY = canvas.height * 0.25;
let progress = 0;

const drops = [];
const splashes = [];

window.addEventListener("resize",()=>{
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});

function createDrop(x,y){
    drops.push({
        x,
        y,
        radius:
        Math.random()*10+4,
        speed:
        Math.random()*4+2,
        alpha:1
    });
}

function createSplash(x,y){
    for(let i = 0; i < 8; i++){
        splashes.push({
            x,
            y,
            radius:
            Math.random()*8+2,
            vx:
            (Math.random()-0.5)*6,
            vy:
            Math.random()*-6,
            alpha:1
        });
    }
}

function drawBrush(){

    ctx.save();
    ctx.translate(
        brushX,
        brushY
    );

    ctx.rotate(
        50 * Math.PI / 180
    );

    ctx.shadowColor =
    "rgba(0,0,0,0.25)";
    ctx.shadowBlur = 18;
    const wood =
    ctx.createLinearGradient(
        -240,
        0,
        0,
        0
    );
    wood.addColorStop(0,"#4b2e16");
    wood.addColorStop(0.5,"#8b5a2b");
    wood.addColorStop(1,"#c08457");
    ctx.fillStyle = wood;
    ctx.beginPath();
    ctx.roundRect(
        -240,
        -14,
        240,
        28,
        15
    );
    ctx.fill();
    const metal =
    ctx.createLinearGradient(
        0,
        -30,
        50,
        30
    );
    metal.addColorStop(0,"#f3f4f6");
    metal.addColorStop(0.5,"#9ca3af");
    metal.addColorStop(1,"#6b7280");
    ctx.fillStyle = metal;
    ctx.beginPath();
    ctx.roundRect(
        -5,
        -30,
        70,
        60,
        10
    );
    ctx.fill();
    const bristles =
    ctx.createLinearGradient(
        60,
        0,
        180,
        0
    );
    bristles.addColorStop(0,"#1d4ed8");
    bristles.addColorStop(0.5,"#2563eb");
    bristles.addColorStop(1,"#60a5fa");
    ctx.fillStyle = bristles;
    ctx.beginPath();
    ctx.moveTo(60,-40);
    ctx.quadraticCurveTo(
        180,
        -20,
        180,
        0
    );
    ctx.quadraticCurveTo(
        180,
        20,
        60,
        40
    );
    ctx.closePath();
    ctx.fill();
    for(let i = 0; i < 25; i++){
        ctx.strokeStyle =
        "rgba(255,255,255,0.25)";
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(
            70 + i*4,
            -28 + Math.random()*56
        );
        ctx.lineTo(
            170,
            -20 + Math.random()*40
        );
        ctx.stroke();
    }
    ctx.restore();
}

function drawPaint(){
    ctx.fillStyle =
    "#2563eb";
    ctx.beginPath();
    ctx.moveTo(
        0,
        canvas.height * 0.45
    );
    for(let x = 0; x <= progress; x += 12){
        const wave =
        Math.sin(x * 0.015) * 25 +
        Math.cos(x * 0.008) * 15 +
        Math.sin(x * 0.05) * 8;
        const incline =
        x * 0.08;
        ctx.lineTo(
            x,
            canvas.height * 0.45 +
            wave +
            incline
        );
    }

    ctx.lineTo(
        progress,
        canvas.height
    );
    ctx.lineTo(
        0,
        canvas.height
    );
    ctx.closePath();
    ctx.fill();
}

function drawDrops(){
    drops.forEach((d,index)=>{
        d.y += d.speed;
        d.alpha -= 0.008;
        ctx.fillStyle =
        `rgba(37,99,235,${d.alpha})`;
        ctx.beginPath();
        ctx.arc(
            d.x,
            d.y,
            d.radius,
            0,
            Math.PI*2
        );
        ctx.fill();
        if(d.alpha <= 0){
            drops.splice(index,1);
        }
    });
}

function drawSplashes(){
    splashes.forEach((s,index)=>{
        s.x += s.vx;
        s.y += s.vy;
        s.vy += 0.15;
        s.alpha -= 0.02;
        ctx.fillStyle =
        `rgba(37,99,235,${s.alpha})`;
        ctx.beginPath();
        ctx.arc(
            s.x,
            s.y,
            s.radius,
            0,
            Math.PI*2
        );
        ctx.fill();
        if(s.alpha <= 0){
            splashes.splice(index,1);
        }
    });
}

function drawText(){

    ctx.save();
    ctx.fillStyle =
    "black";
    ctx.textAlign =
    "center";
    ctx.textBaseline =
    "middle";
    ctx.shadowColor =
    "rgba(0,0,0,0.15)";
    ctx.shadowBlur = 10;
    ctx.font =
    "bold 140px Arial";
    ctx.fillText(
        "SoyArte",
        canvas.width/2,
        canvas.height/2
    );
    ctx.font =
    "bold 70px Arial";
    ctx.fillText(
        "Pintura",
        canvas.width/2,
        canvas.height/2 + 110
    );
    ctx.restore();
}

function animate(){
    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    );

    if(progress < canvas.width + 300){
        progress += 16;
        brushX += 16;

        if(Math.random() < 0.5){
            createDrop(
                brushX + 80,
                brushY + 180
            );
        }
        if(Math.random() < 0.15){
            createSplash(
                brushX + 120,
                brushY + 170
            );
        }
    }

    drawPaint();
    if(progress > canvas.width * 0.45){
        drawText();
    }
    drawDrops();
    drawSplashes();
    drawBrush();
    requestAnimationFrame(
        animate
    );
}
animate();

setTimeout(()=>{
    document.body.style.transition =
    "opacity 1s";
    document.body.style.opacity =
    "0";
},3000);

setTimeout(()=>{
    window.location.href =
    "../pinturas.php";
},4000);