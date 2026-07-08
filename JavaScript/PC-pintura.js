const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");
 
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;
document.body.style.margin = "0";
document.body.style.overflow = "hidden";
document.body.style.background = "#fff8f0";
 
const text1 = "SoyArte";
const text2 = "Pinturas";
let progress = 0;
const inkDrops = [];
const particles = [];
 
function createInk(x, y) {
    inkDrops.push({
        x,
        y,
        r: Math.random() * 5 + 1,
        alpha: 1,
        speed: Math.random() * 2 + 1
    });
}
 
function createParticles(x, y) {
    for (let i = 0; i < 5; i++) {
        particles.push({
            x,
            y,
            vx: (Math.random() - 0.5) * 2,
            vy: (Math.random() - 0.5) * 2,
            r: Math.random() * 2 + 1,
            alpha: 1
        });
    }
}
 
function drawText() {
    ctx.save();
    ctx.strokeStyle = "#e8721a";
    ctx.lineWidth = 3;
    ctx.lineCap = "round";
    ctx.lineJoin = "round";
    ctx.shadowColor = "rgba(232,114,26,0.3)";
    ctx.shadowBlur = 8;
    ctx.font = "bold 140px 'Brush Script MT'";
    ctx.setLineDash([2500]);
    ctx.lineDashOffset = 2500 - progress;
    ctx.strokeText(
        text1,
        canvas.width / 2 - 240,
        canvas.height / 2
    );
    ctx.font = "bold 100px 'Brush Script MT'";
    ctx.strokeText(
        text2,
        canvas.width / 2 - 120,
        canvas.height / 2 + 120
    );
    ctx.restore();
}
 
function drawInk() {
    inkDrops.forEach((d, index) => {
        d.y += d.speed;
        d.alpha -= 0.01;
        ctx.fillStyle = `rgba(232,114,26,${d.alpha})`;
        ctx.beginPath();
        ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2);
        ctx.fill();
        if (d.alpha <= 0) inkDrops.splice(index, 1);
    });
}
 
function drawParticles() {
    particles.forEach((p, index) => {
        p.x += p.vx;
        p.y += p.vy;
        p.alpha -= 0.02;
        ctx.fillStyle = `rgba(232,114,26,${p.alpha})`;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();
        if (p.alpha <= 0) particles.splice(index, 1);
    });
}
 
function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
 
    if (progress < 2600) {
        progress += 14;
        const x = canvas.width / 2 - 200 + progress * 0.18;
        const y = canvas.height / 2 + Math.sin(progress * 0.01) * 20;
        if (Math.random() < 0.5) createInk(x, y);
        if (Math.random() < 0.3) createParticles(x, y);
    }
 
    drawText();
    drawInk();
    drawParticles();
<<<<<<< HEAD

    drawBrush();

    progress += speed;



    // FADE OUT

    if(progress >= 1 && !fadeStarted){

        fadeStarted = true;

        setTimeout(()=>{

            document.body.style.opacity = "0";

        },1200);

        setTimeout(()=>{

            window.location.href = "../pinturas.php";

        },2600);

    }

=======
>>>>>>> 09fa21cb756fa77c17b314ed2b34cbfcc43f546d
    requestAnimationFrame(animate);
}
 
animate();
 
setTimeout(() => {
    document.body.style.transition = "opacity 1s";
    document.body.style.opacity = "0";
}, 4000);
 
setTimeout(() => {
   window.location.href = "../../pinturas.php";
}, 5000);