<?php
/* para guardar las imaggenes en la database y no como archivo */
 

function imagenSrc($blobImagen) {
    if (empty($blobImagen)) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($blobImagen);
    if (!$mime || strpos($mime, 'image/') !== 0) {
        $mime = 'image/jpeg'; // respaldo por si no se detecta el tipo
    }
    return 'data:' . $mime . ';base64,' . base64_encode($blobImagen);
}