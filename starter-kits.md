# Kits de inicio

- [Introducción](#introduction)
- [Laravel Breeze](#laravel-breeze)
  - [Instalación](#laravel-breeze-installation)
  - [Breeze y Blade](#breeze-and-blade)
  - [Breeze y React / Vue](#breeze-and-inertia)
  - [Breeze y Next.js / API](#breeze-and-next)
- [Laravel Jetstream](#laravel-jetstream)

[]()

## Introducción

Para darle una ventaja inicial en la construcción de su nueva aplicación Laravel, estamos encantados de ofrecerle kits de autenticación y de inicio de aplicaciones. Estos kits componen automáticamente tu aplicación con las rutas, controladores y vistas que necesitas para registrar y autenticar a los usuarios de tu aplicación.

Aunque le invitamos a utilizar estos kits de inicio, no son obligatorios. Eres libre de construir tu propia aplicación desde cero simplemente instalando una copia nueva de Laravel. De cualquier manera, ¡sabemos que construirás algo genial!

[]()

## Laravel Breeze

Laravel[Breeze](https://github.com/laravel/breeze) es una implementación mínima y simple de todas las [características de autenticación](/docs/%7B%7Bversion%7D%7D/authentication) de Laravel, incluyendo inicio de sesión, registro, restablecimiento de contraseña, verificación de correo electrónico y confirmación de contraseña. Además, Breeze incluye una sencilla página de "perfil" donde el usuario puede actualizar su nombre, dirección de correo electrónico y contraseña.

La capa de vista por defecto de Laravel Breeze se compone de simples [plantillas Blade](/docs/%7B%7Bversion%7D%7D/blade) estilizadas con [CSS Tailwind](https://tailwindcss.com). O, Breeze puede andamiar tu aplicación usando Vue o React e [Inertia](https://inertiajs.com).

Breeze proporciona un maravilloso punto de partida para comenzar una aplicación Laravel fresca y también es una gran opción para proyectos que planean llevar sus plantillas Blade al siguiente nivel con [Laravel Livewire](https://laravel-livewire.com).

<img src="https://laravel.com/img/docs/breeze-register.png"/>

#### Laravel Bootcamp

Si eres nuevo en Laravel, no dude en saltar en el [Laravel Bootcamp](https://bootcamp.laravel.com). El Laravel Bootcamp te guiará a través de la construcción de tu primera aplicación Laravel utilizando Breeze. Es una gran manera de obtener un recorrido por todo lo que Laravel y Breeze tienen para ofrecer.

[]()

### Instalación

En primer lugar, debes [crear una nueva](/docs/%7B%7Bversion%7D%7D/installation) aplicación Laravel, configurar tu base de datos y ejecutar tus [migraciones de base de datos](/docs/%7B%7Bversion%7D%7D/migrations). Una vez que hayas creado una nueva aplicación Laravel, puedes instalar Laravel Breeze utilizando Composer:

```shell
composer require laravel/breeze --dev
```

Una vez instalado Breeze, puede andamiar su aplicación utilizando una de las "pilas" Breeze discutidos en la documentación a continuación.

[]()

### Breeze y Blade

Después de que Composer haya instalado el paquete Laravel Breeze, puedes ejecutar el comando `breeze:install` Artisan. Este comando publica las vistas de autenticación, rutas, controladores y otros recursos para tu aplicación. Laravel Breeze publica todo su código en tu aplicación para que tengas control total y visibilidad sobre sus características e implementación.

La "pila" Breeze por defecto es la pila Blade, que utiliza [plantillas Blade](/docs/%7B%7Bversion%7D%7D/blade) simples para renderizar el frontend de tu aplicación. La pila Blade puede instalarse invocando el comando `breeze:install` sin otros argumentos adicionales. Después de instalar el andamiaje de Breeze, también debe compilar los activos frontales de su aplicación:

```shell
php artisan breeze:install

php artisan migrate
npm install
npm run dev
```

A continuación, puedes navegar a las URLs `/login` o `/register` de tu aplicación en tu navegador web. Todas las rutas de Breeze se definen en el archivo `routes/auth.php`.

[]()

#### Modo oscuro

Si desea que Breeze incluya compatibilidad con el "modo oscuro" al crear el andamiaje del frontend de su aplicación, simplemente proporcione la directiva `--dark` al ejecutar el comando `breeze`:install:

```shell
php artisan breeze:install --dark
```

> **Nota**  
> Para obtener más información sobre la compilación de CSS y JavaScript de tu aplicación, consulta la [documentación Vite](/docs/%7B%7Bversion%7D%7D/vite#running-vite) de Laravel.

[]()

### Breeze y React / Vue

Laravel Breeze también ofrece andamiaje React y Vue a través de una implementación de frontend [Inertia](https://inertiajs.com). Inertia te permite construir modernas aplicaciones React y Vue de una sola página utilizando enrutamiento y controladores clásicos del lado del servidor.

Inertia te permite disfrutar de la potencia frontend de React y Vue combinada con la increíble productividad backend de Laravel y la rapidísima compilación [de Vite](https://vitejs.dev). Para utilizar un stack de Inertia, especifica `vue` o `react` como tu stack deseado cuando ejecutes el comando breeze `:install` Artisan. Después de instalar el andamiaje de Breeze, también debes compilar los activos frontales de tu aplicación:

```shell
php artisan breeze:install vue

# Or...

php artisan breeze:install react

php artisan migrate
npm install
npm run dev
```

A continuación, puede ir a las direcciones URL `/login` o `/register` de su aplicación en el navegador web. Todas las rutas de Breeze están definidas en el archivo routes/auth `.` php.

[]()

#### Renderizado del lado del servidor

Si deseas que Breeze soporte el andamiaje para [Inertia SSR](https://inertiajs.com/server-side-rendering), puedes proporcionar la opción `ssr` al invocar el comando breeze `:` install:

```shell
php artisan breeze:install vue --ssr
php artisan breeze:install react --ssr
```

[]()

### Breeze y Next.js / API

Laravel Breeze también puede scaffold una API de autenticación que está listo para autenticar aplicaciones JavaScript modernas como las impulsadas por [Next](https://nextjs.org), [Nuxt](https://nuxtjs.org), y otros. Para empezar, especifica la pila `api` como tu pila deseada cuando ejecutes el comando `breeze`:install Artisan:

```shell
php artisan breeze:install api

php artisan migrate
```

Durante la instalación, Breeze añadirá una variable de entorno `FRONTEND_URL` al archivo `.env` de su aplicación. Esta URL debe ser la URL de su aplicación JavaScript. Normalmente será `http://localhost:3000` durante el desarrollo local. Además, debes asegurarte de que tu `APP_URL` esté configurada en `http://localhost:8000`, que es la URL por defecto utilizada por el comando `serve` Artisan.

[]()

#### Implementación de referencia de Next.js

Finalmente, estás listo para emparejar este backend con el frontend de tu elección. Una próxima implementación de referencia del frontend Breeze está [disponible en GitHub](https://github.com/laravel/breeze-next). Este frontend es mantenido por Laravel y contiene la misma interfaz de usuario que los stacks tradicionales Blade e Inertia proporcionados por Breeze.

[]()

## Laravel Jetstream

Mientras que Laravel Breeze proporciona un punto de partida simple y mínimo para construir una aplicación Laravel, Jetstream aumenta esa funcionalidad con características más robustas y pilas de tecnología frontend adicionales. **Para aquellos que son nuevos en Laravel, recomendamos aprender las cuerdas con Laravel Breeze antes de graduarse a Laravel Jetstream.**

Jetstream proporciona un andamiaje de aplicación bellamente diseñado para Laravel e incluye inicio de sesión, registro, verificación de correo electrónico, autenticación de dos factores, gestión de sesiones, soporte de API a través de Laravel Sanctum y gestión de equipos opcional. Jetstream está diseñado utilizando [Tailwind CSS](https://tailwindcss.com) y ofrece su elección de andamiaje frontend impulsado por [Livewire](https://laravel-livewire.com) o [Inertia](https://inertiajs.com).

La [documentación](https://jetstream.laravel.com/2.x/introduction.html) completa para la instalación de Laravel Jetstream se puede encontrar en la [documentación oficial de Jetstream](https://jetstream.laravel.com/2.x/introduction.html).
