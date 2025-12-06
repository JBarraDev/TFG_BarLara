# Proyecto Aplicación Web - Café Bar Lara

**Desarrollado por:** Javier Barra  
**Para:** CFGS - DAW - Curso 2025-26

Este README describe todo lo necesario para instalar, configurar y desplegar la aplicación.

---

## 📋 Tabla de Contenidos

1. [¿Qué es la aplicación?](#qué-es-la-aplicación)
2. [Funcionalidades](#funcionalidades)
3. [Requisitos del Sistema](#requisitos-del-sistema)
4. [Instalación y Despliegue](#instalación-y-despliegue)
5. [Configuración de la Base de Datos](#configuración-de-la-base-de-datos)
6. [Estructura del Proyecto](#estructura-del-proyecto)
7. [Usuarios de Prueba](#usuarios-de-prueba)
8. [Notas Adicionales](#notas-adicionales)

---

## 🎯 ¿Qué es la aplicación?

**Café Bar Lara** es una aplicación web completa diseñada para la gestión integral de un establecimiento de restauración. La aplicación permite gestionar pedidos, reservas, carta y valoraciones en un entorno profesional de cafetería/bar.

La aplicación está desarrollada en **PHP** con una arquitectura MVC simplificada, utilizando **MariaDB** como base de datos y **Bootstrap 5** para la interfaz de usuario.

---

## ⚙️ Funcionalidades

### 👥 Para Clientes

- **Registro e Inicio de Sesión**: Los clientes pueden registrarse con nombre de usuario, email y teléfono, e iniciar sesión de forma segura.
- **Visualización del Menú**: Navegación por la carta con categorías (Bebidas, Bocadillos, Raciones, Postres, etc.).
- **Carrito de Compras**: Añadir productos al carrito, modificar cantidades y eliminar items.
- **Realizar Pedidos**: Generar pedidos desde el carrito que quedan pendientes de confirmación por el administrador.
- **Gestionar Reservas**: Realizar reservas de mesa indicando fecha, hora, número de personas y zona (interior/exterior).
- **Valoraciones**: Los clientes pueden valorar y comentar los productos del menú.
- **Formulario de Contacto**: Enviar mensajes de contacto al establecimiento.

### 🔐 Para Administradores

- **Panel de Administración (Dashboard)**: Vista general de reservas y pedidos pendientes.
- **Gestión del Menú**:
  - Añadir nuevos elementos al menú con precio, categoría, imagen y disponibilidad.
  - Editar elementos existentes.
- **Gestión de Reservas**: Ver todas las reservas, aceptarlas o rechazarlas.
- **Gestión de Pedidos**: Ver todos los pedidos, aceptarlos o rechazarlos, y gestionar su estado.
- **Seguimiento de Valoraciones**: Visualizar las reseñas y valoraciones aportadas por los clientes.
- **Sistema de Seguridad**: Control de acceso basado en roles con verificación en base de datos.

### 🔒 Seguridad

- **Autenticación segura**: Contraseñas hasheadas con `password_hash()` de PHP.
- **Control de sesiones**: Timeout automático de 5 minutos de inactividad.
- **Validación de datos**: Validación tanto en cliente como en servidor.

---

## 💻 Requisitos del Sistema

### Software Necesario

- **Servidor Web**: Apache 2.4 o superior
- **PHP**: Versión 8.0 o superior
- **Base de Datos**: MySQL 5.7+ o MariaDB 10.3+
- **Navegador Web**: Cualquier navegador moderno (Chrome, Firefox, Edge, Safari)

### Entorno de Desarrollo

Durante el desarrollo se utilizó **XAMPP** (Apache + MySQL/MariDB + PHP), pero la aplicación es compatible con cualquier stack LAMP/WAMP/MAMP.

---

## 🚀 Instalación y Despliegue

### Paso 1: Clonar o Descargar el Proyecto

```bash
# Si usas Git
git clone https://github.com/JBarraDev/TFG_BarLara.git

# O descarga el proyecto y extráelo en tu directorio web
```

### Paso 2: Colocar el Proyecto en el Directorio Web

**Para XAMPP:**
- Copia la carpeta del proyecto a `C:\xampp\htdocs\TFG_BarLara` (Windows) o `/opt/lampp/htdocs/TFG_BarLara` (Linux)

**Para otros servidores:**
- Coloca el proyecto en el directorio raíz de tu servidor web (por ejemplo: `/var/www/html/TFG_BarLara`)

### Paso 3: Configurar la Base de Datos

1. Abre phpMyAdmin o tu cliente MySQL preferido.
2. Importa el archivo `database/database_schema.sql` para crear la bbdd y las tablas.
3. (Opcional) Importa `database/database_sample_data.sql` para cargar datos de ejemplo.

### Paso 4: Configurar la Conexión a la Base de Datos

Edita el archivo `app/config/config.php` y ajusta los valores según tu configuración:

```php
define('DB_HOST', 'localhost');        // Host de la base de datos
define('DB_NAME', 'proyectoDAW_cafe_bar_lara');  // Nombre de la base de datos
define('DB_USER', 'root');             // Usuario de MySQL
define('DB_PASS', '');                 // Contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');       // Charset (no cambiar)
```
### Paso 5: Iniciar el Servidor

**XAMPP:**
1. Inicia Apache y MySQL desde el panel de control de XAMPP.
2. Abre tu navegador y accede a: `http://localhost/TFG_BarLara/`

- Asegúrate de que Apache y MySQL estén corriendo.
- Accede a la URL correspondiente según tu configuración.

### Paso 6: Verificar la Instalación

1. Deberías ver la página principal del Café Bar Lara.
2. Prueba acceder a las diferentes secciones (Carta, Reservas, Contacto).
3. Intenta registrarte como cliente nuevo.
4. Inicia sesión como administrador (ver sección de usuarios de prueba).

---

## 🗄️ Configuración de la Base de Datos

### Estructura de Tablas

La base de datos incluye las siguientes tablas principales:

- **roles**: Roles de usuario (Administrador, Cliente)
- **usuarios**: Información de usuarios y administradores
- **categorias**: Categorías del menú (Bebidas, Tapas, Raciones, etc.)
- **menu_items**: Productos del menú con precios e imágenes
- **alergenos**: Alérgenos disponibles
- **menu_items_alergenos**: Relación entre productos y alérgenos
- **reservas**: Reservas de mesas realizadas por clientes
- **pedidos**: Pedidos realizados por clientes
- **lineas_pedido**: Detalle de cada pedido (productos y cantidades)
- **estados**: Estados de reservas y pedidos (Pendiente, Aceptado, Rechazado)
- **valoraciones**: Valoraciones y comentarios de clientes sobre productos

### Scripts SQL Incluidos

- **`database_schema.sql`**: Crea la estructura completa de la base de datos (tablas, claves foráneas, índices).
- **`database_sample_data.sql`**: Inserta datos de ejemplo para probar la aplicación (usuarios, productos, categorías, etc.).

---

## 📁 Estructura del Proyecto

```
BarLara/
├── app/
│   ├── admin/              # Panel de administración
│   │   ├── dashboard.php   # Panel principal
│   │   ├── add_item.php    # Añadir productos
│   │   ├── edit_items.php  # Lista de productos para editar
│   │   ├── edit_item.php   # Editar un producto
│   │   ├── reservas.php    # Gestión de reservas
│   │   └── pedidos.php     # Gestión de pedidos
│   ├── auth/               # Autenticación de administradores
│   │   ├── login.php
│   │   ├── logout.php
│   │   └── process_login.php
│   ├── config/             # Configuración
│   │   ├── config.php      # Configuración de BD
│   │   └── database.php    # Conexión PDO
│   └── helpers/             # Funciones auxiliares
│       ├── session.php     # Gestión de sesiones
│       ├── carrito.php     # Gestión del carrito
│       └── valoraciones.php
├── database/
│   ├── database_schema.sql      # Esquema de BD
│   └── database_sample_data.sql # Datos de ejemplo
├── public/                  # Archivos públicos (raíz web)
│   ├── index.php           # Página principal
│   ├── carta.php           # Menú/Carta
│   ├── reservas.php        # Reservas (clientes)
│   ├── contacto.php        # Formulario de contacto
│   ├── carrito.php         # Carrito de compras
│   ├── login_cliente.php   # Login de clientes
│   ├── registro.php        # Registro de clientes
│   ├── generar_pedido.php  # Generar pedido desde carrito
│   ├── assets/             # Recursos estáticos
│   │   ├── css/
│   │   ├── img/
│   │   └── js/
│   └── includes/
│       ├── header.php      # Cabecera común
│       └── footer.php      # Pie de página común
└── README.md               # Este archivo
```

---

## 👤 Usuarios de Prueba

### Administrador

- **Usuario:** `admin`
- **Email:** `admin@cafelara.com`
- **Contraseña:** `admin123`
- **Acceso:** `http://localhost/TFG_BarLara/app/auth/login.php`
> **Nota:** No tiene acceso directo desde la aplicación, deberá entrar siempre desde el enlace.

### Clientes de Prueba

**Cliente 1:**
- **Usuario:** `cliente1`
- **Email:** `cliente1@email.com`
- **Contraseña:** `cliente123`

**Cliente 2:**
- **Usuario:** `cliente2`
- **Email:** `cliente2@email.com`
- **Contraseña:** `cliente456`

> **Nota:** Estos usuarios se crean automáticamente al importar `database_sample_data.sql`. Las contraseñas están hasheadas de forma segura en la base de datos.

---

## 📝 Notas Adicionales

### Seguridad de Sesiones

- Las sesiones tienen un timeout de **5 minutos** de inactividad.
- Las sesiones se destruyen automáticamente después del timeout.
- Los administradores deben verificar su rol en cada petición.

### Características Técnicas

- **Arquitectura:** MVC simplificada
- **Base de datos:** MySQL/MariaDB con PDO
- **Frontend:** Bootstrap 5, HTML5, CSS3, JavaScript
- **Seguridad:** Password hashing (bcrypt), protección CSRF básica, validación de entrada
- **Compatibilidad:** PHP 8.0+, MySQL 5.7+

### Desarrollo

- El proyecto fue desarrollado como parte del trabajo de fin de grado (TFG) para el CFGS en Desarrollo de Aplicaciones Web.
- Código estructurado con comentarios explicativos.
- Separación de responsabilidades entre lógica de negocio y presentación.

---

### Política de versiones

Se debe seguir con el estándar de Versionado Semántico (Semantic Versioning) en el formato X.Y.Z, donde:

-	**X (Major/Característica Principal)**: Incrementa para cambios incompatibles con versiones anteriores o reescrituras de la arquitectura. Ejemplo: v0.1.0 -> v1.1.0
-	**Y (Minor/Característica Menor)**: Incrementa para la adición de nuevas funcionalidades (features) de forma compatible. Ejemplo: v0.1.0 -> v0.2.0
-	**Z (Patch/Bug Fix)**: Incrementa para correcciones de errores (bugs) sin impacto en la funcionalidad o interfaz. Ejemplo: v0.1.0 -> v0.1.1

---

## 📄 Licencia

Este proyecto es de carácter educativo y fue desarrollado para el CFGS - DAW - Curso 2025-26.

---

**Desarrollado por Javier Barra**
