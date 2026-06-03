<?php
$files = ['create.blade.php', 'edit.blade.php', 'cargar.blade.php'];
$dir = 'f:/PROYECTOS_LARAVEL/educard-modular/resources/views/admin/credenciales/';
foreach($files as $file) {
   $content = file_get_contents($dir . $file);
   $content = str_replace(['carnets', 'carnet', 'Carnet', 'Carnets', 'CARNET'], ['credenciales', 'credencial', 'Credencial', 'Credenciales', 'CREDENCIAL'], $content);
   
   // Field mapping
   $content = str_replace(['codigo_estudiante', 'Código Estudiante', 'Código de estudiante'], ['codigo_credencial', 'Código Credencial', 'Código Credencial'], $content);
   $content = str_replace(['carrera', 'Carrera'], ['cargo_principal', 'Cargo Principal'], $content);
   $content = str_replace(['semestre', 'Semestre'], ['cargo_secundario', 'Cargo Secundario'], $content);
   $content = str_replace('type="number" name="cargo_secundario"', 'type="text" name="cargo_secundario"', $content);
   
   // Replace some leftover bits if necessary
   $content = str_replace('Estudiante', 'Personal', $content);
   
   file_put_contents($dir . $file, $content);
}
