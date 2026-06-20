<?php

$archivo = "uploads/musica/1781984380_audio.mp3";

header("Content-Type: audio/mpeg");
header("Content-Length: " . filesize($archivo));

readfile($archivo);
exit;