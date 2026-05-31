
const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

const text = "SoyArte\nMúsica";

const tempCanvas = document.createElement("canvas");
const tempCtx = tempCanvas.getContext("2d");

tempCanvas.width = canvas.width;
tempCanvas.height = canvas.height;

const fontSize =
window.innerWidth < 768 ? 60 : 120;

tempCtx.fillStyle = "white";
tempCtx.textAlign = "center";
tempCtx.textBaseline = "middle";

tempCtx.font =
`bold ${fontSize}px Arial`;

const lines = text.split("\n");
const startY =
canvas.height / 2 - 60;
lines.forEach((line, i) => {
    tempCtx.fillText(
        line,
        canvas.width / 2,
        startY + i * 120
    );
});

const imageData = tempCtx.getImageData(
    0,
    0,
    canvas.width,
    canvas.height
);
const particles = [];

for(let y = 0; y < canvas.height; y += 8){
    for(let x = 0; x < canvas.width; x += 8){
        const index =
        (y * canvas.width + x) * 4;
        if(imageData.data[index] > 128){
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                targetX: x,
                targetY: y,
                size: 18,
                note: ["♪","♫","♩","♬"][
                    Math.floor(Math.random() * 4)
                ]
            });
        }
    }
}

const sound =
document.getElementById("sound");
setTimeout(() => {
    sound.play().catch(() => {});
}, 200);

function animate(){
    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    );
    particles.forEach(p => {
        p.x +=
        (p.targetX - p.x) * 0.05;
        p.y +=
        (p.targetY - p.y) * 0.05;
        ctx.fillStyle = "black";
        ctx.font =
        `${p.size}px Arial`;
        ctx.fillText(
            p.note,
            p.x,
            p.y
        );
    });
    requestAnimationFrame(
        animate
    );
}
animate();
setTimeout(() => {
    document.body.style.transition =
    "opacity 1s";
    document.body.style.opacity =
    "0";
}, 3000);

setTimeout(() => {
    window.location.href =
    "../php/musica.php";
}, 4000);