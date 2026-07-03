const formulario=document.getElementById("formMensaje");
const cajaMensajes=document.getElementById("mensajes");
const conversacion=document.getElementById("conversacion").value;

//==============================
// ENVIAR MENSAJE
//==============================

function enviarMensaje(){

    const mensaje = document.getElementById("mensaje").value.trim();

    const datos = new FormData();

    datos.append("conversacion", conversacion);

    datos.append("mensaje", mensaje);

    if(inputImagen.files.length > 0){

        datos.append("imagen", inputImagen.files[0]);

    }

    if(mensaje === "" && inputImagen.files.length === 0){

        return;

    }

    fetch("php/enviar_mensaje.php",{

        method:"POST",

        body:datos

    })

    .then(res => res.text())

    .then(res =>{

        if(res === "OK"){
            document.getElementById("mensaje").value="";

            inputImagen.value="";

            document.getElementById("previewImagen").style.display="none";

            document.getElementById("imgPreview").src="";

            cargarMensajes();

        }else{

            alert(res);

        }

    });

}

formulario.addEventListener("submit",function(e){

    e.preventDefault();

    enviarMensaje();

});

//==============================
// MARCAR MENSAJES COMO ENTREGADOS
//==============================

function marcarEntregado(){

    const datos = new FormData();

    datos.append("conversacion", conversacion);

    fetch("php/marcar_entregado.php",{
        method:"POST",
        body:datos
    })
    .catch(error => console.error(error));

}
// ACTUALIZACIÓN AUTOMÁTICA //
function cargarMensajes(){

    const estabaAbajo =
        cajaMensajes.scrollHeight - cajaMensajes.scrollTop <= cajaMensajes.clientHeight + 80;

    fetch("php/obtener_mensajes.php?conversacion="+conversacion)

    .then(res=>res.text())

    .then(html=>{

        cajaMensajes.innerHTML = html;

        marcarLeidos();

        if(estabaAbajo){

            cajaMensajes.scrollTop = cajaMensajes.scrollHeight;

        }

    });

}
setInterval(cargarMensajes,20000);

marcarEntregado();
//==============================
// MARCAR MENSAJES COMO LEÍDOS
//==============================

function marcarLeidos(){

    const datos = new FormData();

    datos.append("conversacion", conversacion);

    fetch("php/marcar_leidos.php",{
        method:"POST",
        body:datos
    })
    .then(res => res.text())
    .then(res => {

        if(res === "OK"){
            cargarMensajes();
        }

    })
    .catch(error => console.error(error));

}

cargarMensajes();

//setInterval(actualizarActividad,10000);//

//actualizarActividad();
// ENVIAR CON ENTER //
document.getElementById("mensaje").addEventListener("keydown", function(e){

    if(e.key === "Enter" && !e.shiftKey){

        e.preventDefault();

        formulario.requestSubmit();

    }

});

//==============================
// SELECCIONAR IMAGEN
//==============================

const botonImagen = document.getElementById("btnImagen");

const inputImagen = document.getElementById("imagenChat");

botonImagen.addEventListener("click",function(){

    inputImagen.click();

});

inputImagen.addEventListener("change",function(){

    if(this.files.length===0){

        return;

    }

    const archivo=this.files[0];

    if(archivo.size>5*1024*1024){

        alert("La imagen no puede superar los 5 MB.");

        this.value="";

        return;

    }

    if(!archivo.type.startsWith("image/")){

        alert("Solo se permiten imágenes.");

        this.value="";

        return;

    }

    const lector=new FileReader();

    lector.onload=function(e){

        document.getElementById("imgPreview").src=e.target.result;

        document.getElementById("previewImagen").style.display="block";

    };

    lector.readAsDataURL(archivo);

});

//==============================
// BOTONES DE LA VISTA PREVIA
//==============================

document.getElementById("cancelarImagen").addEventListener("click",function(){

    inputImagen.value="";

    document.getElementById("imgPreview").src="";

    document.getElementById("previewImagen").style.display="none";

});

document.getElementById("enviarImagen").addEventListener("click",function(){

    enviarMensaje();

});

//==============================
// VISOR DE IMÁGENES
//==============================

function abrirImagen(src){

    document.getElementById("imagenGrande").src = src;

    document.getElementById("visorImagen").style.display = "flex";

}

document.getElementById("cerrarVisor").addEventListener("click",function(){

    document.getElementById("visorImagen").style.display="none";

});

document.getElementById("visorImagen").addEventListener("click",function(e){

    if(e.target.id==="visorImagen"){

        this.style.display="none";

    }

});
// ACTUALIZAR ACTIVIDAD DEL USUARIO
/*
function actualizarActividad(){

    fetch("php/actualizar_actividad.php")
    .then(res => res.text())
    .then(data => {
        console.log("Actividad actualizada:", data);
    })
    .catch(error => {
        console.error("Error:", error);
    });

}*/
