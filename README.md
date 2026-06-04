# EduCard Modular

Sistema de generación de carnets estudiantiles. Consume la API del sistema principal (Trámites).

## 🐳 Instrucciones de Portabilidad (Docker / Laravel Sail)

Este proyecto está **Dockerizado**. Puedes levantarlo en cualquier computadora sin instalar PHP ni MySQL manualmente.

### 1. Requisitos Previos
- Docker Desktop instalado y corriendo.
- (En Windows) WSL2 habilitado.

### 2. Clonar y Preparar
```bash
git clone https://github.com/joaquinweb7/educard-modular.git
cd educard-modular
cp .env.example .env
```
*(Asegúrate de copiar manualmente la carpeta antigua de `storage/app/public` si necesitas restaurar fotos).*

### 3. Instalar Dependencias Iniciales
Si no tienes PHP local, usa este contenedor temporal:
```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs
```

### 4. Levantar el Proyecto
Levanta el ecosistema (esto importará el archivo `database_backup.sql` automáticamente la primera vez):
```bash
./vendor/bin/sail up -d
```
Genera la llave y compila assets:
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

**¡Listo!** El sistema estará disponible en `http://localhost:8001`.

> **Nota sobre la API:** En el panel de administrador, configura la URL de la API de Trámites hacia `http://host.docker.internal:8000` para que los contenedores puedan comunicarse.
