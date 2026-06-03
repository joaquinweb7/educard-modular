# EduCard Modular - Sistema Laravel de Carnets Estudiantiles

Sistema base en Laravel para:

- Solicitud pública de carnet estudiantil.
- Carga y previsualización de fotografía 4x4 fondo rojo.
- Generación de constancia PDF con número de trámite.
- Revisión manual de solicitudes.
- Aprobación/rechazo/observación de fotografías.
- Asignación automática de código estudiantil desde `202602100`.
- Registro manual y registro masivo por CSV.
- Diseño visual de plantilla de carnet con drag and drop usando Fabric.js.
- Generación de carnets en PDF listos para imprimir por carrera, semestre o grupo.
- Panel administrativo con menú lateral.
- Gestor de plugins instalables, activables y desactivables.
- Ejemplo de plugin: Verificación QR.

> Este ZIP no incluye la carpeta `vendor` ni `node_modules`. Debes ejecutar `composer install` y, si usas herramientas frontend, `npm install`.

---

## 1. Requisitos

- PHP 8.2 o superior.
- Composer.
- MySQL/MariaDB o SQLite.
- Extensión PHP Zip si instalarás plugins en ZIP.
- Extensión PHP GD o Imagick recomendada para manejo de imágenes.

---

## 2. Instalación rápida

```bash
unzip educard-modular.zip
cd educard-modular
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Luego abre:

```txt
http://127.0.0.1:8000
```

---

## 3. Usuario administrador inicial

```txt
Correo: admin@educard.test
Contraseña: password
```

Cambia esta contraseña al implementar en producción.

---

## 4. Configuración de base de datos

Por defecto el `.env.example` usa SQLite:

```env
DB_CONNECTION=sqlite
```

Después de copiar `.env`, crea el archivo:

```bash
mkdir -p database
# Windows PowerShell:
New-Item database/database.sqlite -ItemType File
# Linux/Mac:
touch database/database.sqlite
```

Si deseas MySQL, cambia en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=educard_modular
DB_USERNAME=root
DB_PASSWORD=
```

Luego ejecuta:

```bash
php artisan migrate --seed
```

---

## 5. Flujo del sistema

### Estudiante

1. Entra a `/solicitud-carnet`.
2. Llena datos personales.
3. Sube fotografía 4x4 fondo rojo.
4. Previsualiza la fotografía.
5. Envía la solicitud.
6. Recibe número de trámite.
7. Descarga constancia PDF.

### Administración

1. Entra a `/login`.
2. Revisa solicitudes pendientes.
3. Aprueba, rechaza u observa.
4. Al aprobar, se crea el estudiante y se asigna código automático.
5. Diseña plantilla de carnet.
6. Genera carnets en PDF por carrera, semestre o selección.
7. Administra plugins.

---

## 6. Código estudiantil automático

El sistema inicia con:

```txt
202602100
```

La tabla `student_code_sequences` se crea con:

```txt
prefix: 202602
last_number: 99
```

Al aprobar la primera solicitud, el sistema genera:

```txt
202602100
```

Luego:

```txt
202602101
202602102
202602103
```

La generación usa transacción y bloqueo de fila para evitar duplicados.

---

## 7. Registro masivo por CSV

Desde el panel administrativo entra a:

```txt
Estudiantes > Registro masivo
```

Formato esperado del CSV:

```csv
nombres,apellidos,carnet,carrera,semestre
Juan,Calle,1234567,Contaduría General,1
Ana,Rojas,7654321,Mecánica Automotriz,2
```

---

## 8. Diseñador de carnet

El diseñador permite:

- Subir fondo del carnet.
- Agregar campos dinámicos.
- Moverlos con drag and drop.
- Guardar la plantilla como JSON.
- Generar PDF usando la plantilla.

Campos dinámicos disponibles:

```txt
names
lastnames
ci_number
student_code
career
semester
photo
```

---

## 9. Plugins

Los plugins viven en:

```txt
plugins/NOMBRE_DEL_PLUGIN
```

Cada plugin debe tener:

```txt
plugin.json
routes/admin.php
resources/views/
src/
```

Ejemplo incluido:

```txt
plugins/VerificationQr
```

Archivo `plugin.json` de ejemplo:

```json
{
  "name": "VerificationQr",
  "display_name": "Verificación QR",
  "description": "Ejemplo de plugin para verificar carnets por QR.",
  "version": "1.0.0",
  "author": "EduCard",
  "provider": "Plugins\\VerificationQr\\VerificationQrServiceProvider",
  "menu": [
    {
      "title": "Verificación QR",
      "icon": "qr-code",
      "route": "admin.plugins.verificationqr.index",
      "permission": "plugins.view"
    }
  ]
}
```

Para que un plugin aparezca en el menú:

1. Debe existir en la tabla `plugins`.
2. Debe estar activo.
3. Debe tener menú en `plugin.json`.

---

## 10. Estructura principal

```txt
app/
  Http/Controllers/Admin
  Http/Controllers/Auth
  Http/Controllers/Public
  Models
  Services
plugins/
  VerificationQr
resources/views/
  admin
  auth
  layouts
  public
routes/
  web.php
```

---

## 11. Notas importantes

Este proyecto es una base implementable. Para producción debes añadir:

- HTTPS.
- Políticas más estrictas de roles y permisos.
- Validación biométrica/manual más avanzada de fotografías.
- Antivirus para archivos ZIP de plugins.
- Revisión de seguridad de plugins antes de instalarlos.
- Respaldos automáticos.
- Logs de auditoría.

