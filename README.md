# EduCard Modular

Sistema de generación de carnets estudiantiles. Consume la API del sistema principal (Trámites).

## 📦 Instrucciones de Portabilidad / Instalación

Este proyecto se ha configurado para ser portable **conservando los datos de producción y archivos subidos**. Sigue estos pasos para instalarlo en una nueva computadora:

### 1. Requisitos Previos
- PHP >= 8.1
- Composer
- Node.js y NPM
- MySQL / MariaDB (ej. XAMPP, Laragon)

### 2. Clonar el repositorio
```bash
git clone https://github.com/joaquinweb7/educard-modular.git
cd educard-modular
```

### 3. Instalar dependencias
```bash
composer install
npm install
npm run build
```

### 4. Configurar el Entorno (`.env`)
- Copia el archivo de ejemplo y renómbralo:
  ```bash
  cp .env.example .env
  ```
- Abre el `.env` e ingresa los datos de conexión a tu nueva base de datos MySQL local:
  ```env
  DB_DATABASE=educard
  DB_USERNAME=root
  DB_PASSWORD=
  ```
- Genera la llave de la aplicación:
  ```bash
  php artisan key:generate
  ```

### 5. Restaurar Base de Datos y Archivos (IMPORTANTE)
**NO EJECUTES** `php artisan migrate:fresh --seed` porque perderás los datos reales.
1. Crea la base de datos `educard` vacía en tu MySQL.
2. Importa el archivo `database_backup.sql` que se encuentra en la raíz del proyecto.
3. Copia manualmente tu carpeta antigua de `storage/app/public` al nuevo proyecto para conservar las fotos y plantillas de carnets.

### 6. Iniciar el Servidor
Crea el enlace simbólico para las imágenes:
```bash
php artisan storage:link
```
Dado que este es el sistema satélite, levántalo en el **puerto 8001**:
```bash
php artisan serve --port=8001
```
