# Agrupación de activos (Vite)

- [Introducción](#introduction)
- [Instalación y configuración](#installation)
  - [Instalación de Node](#installing-node)
  - [Instalación de Vite y el plugin de Laravel](#installing-vite-and-laravel-plugin)
  - [Configuración de Vite](#configuring-vite)
  - [Cargando sus scripts y estilos](#loading-your-scripts-and-styles)
- [Ejecución de Vite](#running-vite)
- [Trabajar con JavaScript](#working-with-scripts)
  - [Alias](#aliases)
  - [Vue](#vue)
  - [React](#react)
  - [Inercia](#inertia)
  - [Procesamiento de URL](#url-processing)
- [Trabajar con hojas de estilo](#working-with-stylesheets)
- [Trabajando Con Blade & Routes](#working-with-blade-and-routes)
  - [Procesamiento de activos estáticos con Vite](#blade-processing-static-assets)
  - [Actualizar al guardar](#blade-refreshing-on-save)
  - [Aliases](#blade-aliases)
- [URLs base personalizadas](#custom-base-urls)
- [Variables de entorno](#environment-variables)
- [Desactivación de Vite en las tests](#disabling-vite-in-tests)
- [Renderizado del lado del servidor (SSR)](#ssr)
- [Atributos de las etiquetas Script y Style](#script-and-style-attributes)
  - [policy-csp-nonce"> policy seguridad de contenidos (CSP) Nonce](<#content-security-\<glossary variable=>)
  - [Integridad de Subrecursos (SRI)](#subresource-integrity-sri)
  - [Atributos arbitrarios](#arbitrary-attributes)
- [Personalización avanzada](#advanced-customization)

[]()

## Introducción

[Vite](https://vitejs.dev) es una moderna herramienta de construcción frontend que proporciona un entorno de desarrollo extremadamente rápido y agrupa su código para la producción. Cuando construyas aplicaciones con Laravel, normalmente utilizarás Vite para empaquetar los archivos CSS y JavaScript de tu aplicación en activos listos para producción.

Laravel se integra perfectamente con Vite proporcionando un plugin oficial y una directiva Blade para cargar sus activos para desarrollo y producción.

> **Nota**  
> ¿Está ejecutando Laravel Mix? Vite ha sustituido a Laravel Mix en las nuevas instalaciones de Laravel. Para consultar la documentación de Mix, visita el sitio web de Laravel [Mix](https://laravel-mix.com/). Si desea cambiar a Vite, consulte nuestra [guía de migración](https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-laravel-mix-to-vite).

[]()

#### Elegir entre Vite y Laravel Mix

Antes de la transición a Vite, las nuevas aplicaciones Laravel utilizaban [Mix](https://laravel-mix.com/), que funciona con [webpack](https://webpack.js.org/), para empaquetar activos. Vite se centra en proporcionar una experiencia más rápida y productiva en la creación de aplicaciones ricas en JavaScript. Si usted está desarrollando una aplicación de página única (SPA), incluidos los desarrollados con herramientas como [Inertia](https://inertiajs.com), Vite será el ajuste perfecto.

Vite también funciona bien con las tradicionales aplicaciones renderizadas del lado del servidor con JavaScript "sprinkles", incluyendo aquellas que utilizan [Livewire](https://laravel-livewire.com). Sin embargo, carece de algunas características que soporta Laravel Mix, como la capacidad de copiar activos arbitrarios en la compilación que no están referenciados directamente en su aplicación JavaScript.

[]()

#### Migrar de nuevo a Mix

¿Has comenzado una nueva aplicación Laravel utilizando nuestro andamiaje Vite pero necesitas volver a Laravel Mix y webpack? No hay problema. Consulta nuestra [guía oficial sobre la migración de Vite a Mix](https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-vite-to-laravel-mix).

[]()

## Instalación y configuración

> **Nota**  
> La siguiente documentación explica cómo instalar y configurar manualmente el plugin Laravel Vite. Sin embargo, los [kits de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits) de Laravel ya incluyen todo este andamiaje y son la forma más rápida de empezar con Laravel y Vite.

[]()

### Instalación de Node

Debe asegurarse de que Node.js (16+) y NPM están instalados antes de ejecutar Vite y el plugin de Laravel:

```sh
node -v
npm -v
```

Puedes instalar fácilmente la última versión de Node y NPM utilizando sencillos instaladores gráficos desde [el sitio web oficial de Node](https://nodejs.org/en/download/). O, si estás usando [Laravel Sail](https://laravel.com/docs/%7B%7Bversion%7D%7D/sail), puedes invocar Node y NPM a través de Sail:

```sh
./vendor/bin/sail node -v
./vendor/bin/sail npm -v
```

[]()

### Instalación de Vite y el plugin de Laravel

En una instalación nueva de Laravel, encontrarás un archivo package `.j` son en la raíz de la estructura de directorios de tu aplicación. El archivo package. `json` por defecto ya incluye todo lo necesario para empezar a utilizar Vite y el plugin de Laravel. Puedes instalar las dependencias frontales de tu aplicación a través de NPM:

```sh
npm install
```

[]()

### Configuración de Vite

Vite se configura a través de un archivo `vite.config.js` en la raíz de tu proyecto. Eres libre de personalizar este archivo en función de tus necesidades, y también puedes instalar cualquier otro plugin que requiera tu aplicación, como `@vitejs/plugin-vue` o `@vitejs/plugin-react`.

El plugin Laravel Vite requiere que especifiques los puntos de entrada para tu aplicación. Estos pueden ser archivos JavaScript o CSS, e incluyen lenguajes preprocesados como TypeScript, JSX, TSX y Sass.

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});
```

Si estás construyendo una SPA, incluyendo aplicaciones construidas usando Inertia, Vite funciona mejor sin puntos de entrada CSS:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css', // [tl! remove]
            'resources/js/app.js',
        ]),
    ],
});
```

En su lugar, debe importar su CSS a través de JavaScript. Normalmente, esto se haría en el archivo `resources/js/app.js` de su aplicación:

```js
import './bootstrap';
import '../css/app.css'; // [tl! add]
```

El plugin de Laravel también soporta múltiples puntos de entrada y opciones de configuración avanzadas como [puntos de entrada SSR](#ssr).

[]()

#### Trabajar con un servidor de desarrollo seguro

Si su servidor web de desarrollo local está sirviendo su aplicación a través de HTTPS, puede tener problemas para conectarse al servidor de desarrollo de Vite.

Si está utilizando Laravel [Valet](/docs/%7B%7Bversion%7D%7D/valet) para el desarrollo local y ha ejecutado el [comando seguro](/docs/%7B%7Bversion%7D%7D/valet#securing-sites) contra su aplicación, puede configurar el servidor de desarrollo Vite para utilizar automáticamente los certificados TLS generados por Valet:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            valetTls: 'my-app.test', // [tl! add]
        }),
    ],
});
```

Cuando se utiliza otro servidor web, debe generar un certificado de confianza y configurar manualmente Vite para utilizar los certificados generados:

```js
// ...
import fs from 'fs'; // [tl! add]

const host = 'my-app.test'; // [tl! add]

export default defineConfig({
    // ...
    server: { // [tl! add]
        host, // [tl! add]
        hmr: { host }, // [tl! add]
        https: { // [tl! add]
            key: fs.readFileSync(`/path/to/${host}.key`), // [tl! add]
            cert: fs.readFileSync(`/path/to/${host}.crt`), // [tl! add]
        }, // [tl! add]
    }, // [tl! add]
});
```

Si no puedes generar un certificado de confianza para tu sistema, puedes instalar y configurar el [plugin`@vitejs/plugin-basic-ssl`](https://github.com/vitejs/vite-plugin-basic-ssl). Cuando uses certificados no confiables, necesitarás aceptar la advertencia del certificado para el servidor de desarrollo de Vite en tu navegador siguiendo el enlace "Local" en tu consola cuando ejecutes el comando `npm run dev`.

[]()

### Cargando sus scripts y estilos

Con tus puntos de entrada Vite configurados, sólo necesitas referenciarlos en una directiva `@vite()` Blade que añadas al `<head>` de la plantilla raíz de tu aplicación:

```blade
<!doctype html>
<head>
    {{-- ... --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

Si está importando su CSS a través de JavaScript, sólo necesita incluir el punto de entrada JavaScript:

```blade
<!doctype html>
<head>
    {{-- ... --}}

    @vite('resources/js/app.js')
</head>
```

La directiva `@vite` detectará automáticamente el servidor de desarrollo Vite e inyectará el cliente Vite para habilitar la sustitución en caliente de módulos. En el modo de compilación, la directiva cargará tus activos compilados y versionados, incluyendo cualquier CSS importado.

Si es necesario, también puede especificar la ruta de compilación de sus activos compilados al invocar la directiva `@vite`:

```blade
<!doctype html>
<head>
    {{-- Given build path is relative to public path. --}}

    @vite('resources/js/app.js', 'vendor/courier/build')
</head>
```

[]()

## Ejecución de Vite

Hay dos formas de ejecutar Vite. Puede ejecutar el servidor de desarrollo a través del comando `dev`, que es útil mientras se desarrolla localmente. El servidor de desarrollo detectará automáticamente los cambios en sus archivos y los reflejará instantáneamente en cualquier ventana abierta del navegador.

O bien, ejecutar el comando `build` versionará y empaquetará los activos de tu aplicación y los tendrá listos para que los despliegues en producción:

```shell
# Run the Vite development server...
npm run dev

# Build and version the assets for production...
npm run build
```

[]()

## Trabajar con JavaScript

[]()

### Aliases

Por defecto, el plugin de Laravel proporciona un alias común para ayudarte a importar cómodamente los activos de tu aplicación:

```js
{
    '@' => '/resources/js'
}
```

Puedes sobrescribir el alias `'@` ' añadiendo el tuyo propio al archivo de configuración `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel(['resources/ts/app.tsx']),
    ],
    resolve: {
        alias: {
            '@': '/resources/ts',
        },
    },
});
```

[]()

### Vue

Hay algunas opciones adicionales que tendrás que incluir en el archivo de configuración vite.config `.j` s cuando utilices el plugin de Vue con el plugin de Laravel:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel(['resources/js/app.js']),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

> **Nota**  
> Los [kits de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits) de Laravel ya incluyen la configuración adecuada de Laravel, Vue y Vite. Echa un vistazo a [Laravel Breeze](/docs/%7B%7Bversion%7D%7D/starter-kits#breeze-and-inertia) para la forma más rápida de empezar con Laravel, Vue y Vite.

[]()

### React

Cuando se utiliza Vite con React, tendrá que asegurarse de que todos los archivos que contienen JSX tienen una extensión `.jsx` o `.tsx`, recordando actualizar su punto de entrada, si es necesario, como [se muestra arriba](#configuring-vite). También necesitarás incluir la directiva adicional `@viteReactRefresh` Blade junto a tu directiva `@vite` existente.

```blade
@viteReactRefresh
@vite('resources/js/app.jsx')
```

La directiva `@viteReactRefresh` debe invocarse antes que la directiva `@vite`.

> **Nota**  
> Los [kits de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits) de Laravel ya incluyen la configuración adecuada de Laravel, React y Vite. Echa un vistazo a [Laravel Breeze](/docs/%7B%7Bversion%7D%7D/starter-kits#breeze-and-inertia) para la forma más rápida de empezar con Laravel, React, y Vite.

[]()

### Inercia

El plugin Laravel Vite proporciona una práctica función `resolvePageComponent` para ayudarle a resolver sus componentes de página Inertia. A continuación se muestra un ejemplo de la ayuda en uso con Vue 3, sin embargo, también puede utilizar la función en otros marcos como React:

```js
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
});
```

> **Nota**  
> Los [kits de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits) de Laravel ya incluyen la configuración adecuada de Laravel, Inertia y Vite. Echa un vistazo a [Laravel Breeze](/docs/%7B%7Bversion%7D%7D/starter-kits#breeze-and-inertia) para la forma más rápida de empezar con Laravel, Inertia, y Vite.

[]()

### Procesamiento de URL

Cuando utilices Vite y hagas referencia a activos en el HTML, CSS o JS de tu aplicación, debes tener en cuenta un par de advertencias. En primer lugar, si se hace referencia a los activos con una ruta absoluta, Vite no incluirá el activo en la construcción, por lo tanto, usted debe asegurarse de que el activo está disponible en su directorio público.

Cuando se hace referencia a las rutas relativas de los activos, se debe recordar que las rutas son relativas al archivo en el que se hace referencia. Cualquier activo referenciado a través de una ruta relativa será reescrito, versionado y empaquetado por Vite.

Considere la siguiente estructura de proyecto:

```nothing
public/
  taylor.png
resources/
  js/
    Pages/
      Welcome.vue
  images/
    abigail.png
```

El siguiente ejemplo demuestra cómo Vite tratará las URLs relativas y absolutas:

```html
<!-- This asset is not handled by Vite and will not be included in the build -->
<img src="/taylor.png">

<!-- This asset will be re-written, versioned, and bundled by Vite -->
<img src="../../images/abigail.png">
```

[]()

## Trabajar con hojas de estilo

Puedes aprender más sobre el soporte CSS de Vite en la [documentación de Vite](https://vitejs.dev/guide/features.html#css). Si estás usando plugins PostCSS como [Tailwind](https://tailwindcss.com), puedes crear un archivo `postcss.config.js` en la raíz de tu proyecto y Vite lo aplicará automáticamente:

```js
module.exports = {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
```

[]()

## Trabajando Con Blade & Routes

[]()

### Procesamiento de activos estáticos con Vite

Al hacer referencia a los activos en su JavaScript o CSS, Vite automáticamente los procesa y los versiona. Además, al construir aplicaciones basadas en Blade, Vite también puede procesar y versionar los activos estáticos a los que se hace referencia únicamente en las plantillas de Blade.

Sin embargo, con el fin de lograr esto, es necesario hacer Vite consciente de sus activos mediante la importación de los activos estáticos en el punto de entrada de la aplicación. Por ejemplo, si desea procesar y versionar todas las imágenes almacenadas en `resources/images` y todas las fuentes almacenadas en `resources/fonts`, debe añadir lo siguiente en el punto de entrada `resources/js/app.js` de su aplicación:

```js
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);
```

Estos activos serán ahora procesados por Vite cuando ejecutes `npm run build`. A continuación, puede hacer referencia a estos activos en las plantillas Blade utilizando el método `Vite::asset`, que devolverá la URL versionada de un activo determinado:

```blade
<img src="{{ Vite::asset('resources/images/logo.png') }}">
```

[]()

### Actualizar al Guardar

Cuando su aplicación se construye utilizando la tradicional renderización del lado del servidor con Blade, Vite puede mejorar su flujo de trabajo de desarrollo mediante la actualización automática del navegador cuando se realizan cambios en los archivos de vista en su aplicación. Para empezar, sólo tiene que especificar la opción de `actualización` como `verdadera`.

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: true,
        }),
    ],
});
```

Cuando la opción `refresh` es `true`, guardar archivos en los siguientes directorios hará que el navegador realice una actualización completa de la página mientras estás ejecutando `npm run dev`:

- `app/View/Components/**`
- `lang/**`
- `resources/lang/**`
- `resources/views/**`
- `rutas/**`

Observar el directorio `routes/**` es útil si estás utilizando [Ziggy](https://github.com/tighten/ziggy) para generar enlaces de rutas dentro del frontend de tu aplicación.

Si estas rutas por defecto no se ajustan a sus necesidades, puede especificar su propia lista de rutas a vigilar:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: ['resources/views/**'],
        }),
    ],
});
```

Bajo el capó, el plugin Laravel Vite utiliza el paquete [`vite-plugin-full-reload`](https://github.com/ElMassimo/vite-plugin-full-reload), que ofrece algunas opciones de configuración avanzadas para ajustar el comportamiento de esta característica. Si necesitas este nivel de personalización, puedes proporcionar una definición `config`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: [{
                paths: ['path/to/watch/**'],
                config: { delay: 300 }
            }],
        }),
    ],
});
```

[]()

### Aliases

Es común en aplicaciones JavaScript [crear](#aliases) alias para directorios referenciados regularmente. Pero también puede crear alias para utilizarlos en Blade mediante el método `macro` de la clase `Illuminate\Support\Vite`. Normalmente, las "macros" deben definirse dentro del método de `arranque` de un [proveedor de servicios](/docs/%7B%7Bversion%7D%7D/providers):

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Vite::macro('image', fn ($asset) => $this->asset("resources/images/{$asset}"));
    }

Una vez definida una macro, puede ser invocada dentro de sus plantillas. Por ejemplo, podemos utilizar la macro de `imagen` definida anteriormente para hacer referencia a un activo ubicado en `resources/images/logo.png`:

```blade
<img src="{{ Vite::image('logo.png') }}" alt="Laravel Logo">
```

[]()

## URLs Base Personalizadas

Si sus activos compilados Vite se despliegan en un dominio separado de su aplicación, como a través de una CDN, debe especificar la variable de entorno `ASSET_URL` en el archivo `.env` de su aplicación:

```env
ASSET_URL=https://cdn.example.com
```

Una vez configurada la URL de los activos, todas las URL reescritas de sus activos llevarán como prefijo el valor configurado:

```nothing
https://cdn.example.com/build/assets/app.9dce8d17.js
```

Recuerde que [las URL absolutas no son reescritas por Vite](#url-processing), por lo que no llevarán prefijo.

[]()

## Variables de entorno

Puede inyectar variables de entorno en su JavaScript anteponiéndoles `VITE_` en el archivo . `env` de su aplicación:

```env
VITE_SENTRY_DSN_PUBLIC=http://example.com
```

Puede acceder a las variables de entorno inyectadas a través del objeto `import.meta.env`:

```js
import.meta.env.VITE_SENTRY_DSN_PUBLIC
```

[]()

## Desactivación de Vite en tests

La integración Vite de Laravel intentará resolver tus assets mientras ejecutas tus tests, lo que requiere que ejecutes el servidor de desarrollo Vite o que construyas tus assets.

Si prefiere mock Vite durante las pruebas, puede llamar al método `withoutVite`, que está disponible para cualquier tests que extienda la clase `TestCase` de Laravel:

```php
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_without_vite_example()
    {
        $this->withoutVite();

        // ...
    }
}
```

Si desea desactivar Vite para todas las tests, puede llamar al método `withoutVite` desde el método `setUp` en su clase `TestCase` base:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void// [tl! add:start]
    {
        parent::setUp();

        $this->withoutVite();
    }// [tl! add:end]
}
```

[]()

## Renderizado del lado del servidor (SSR)

El plugin Laravel Vite facilita la configuración del renderizado del lado del servidor con Vite. Para empezar, cree un punto de entrada SSR en `resources/js/ssr.js` y especifique el punto de entrada pasando una opción de configuración al plugin de Laravel:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
        }),
    ],
});
```

Para asegurarse de que no se olvida de reconstruir el punto de entrada SSR, le recomendamos que aumente el script "build" en el `package.json` de su aplicación para crear su build SSR:

```json
"scripts": {
     "dev": "vite",
     "build": "vite build" // [tl! remove]
     "build": "vite build && vite build --ssr" // [tl! add]
}
```

A continuación, para construir e iniciar el servidor SSR, puede ejecutar los siguientes comandos:

```sh
npm run build
node bootstrap/ssr/ssr.mjs
```

> **Nota**  
> Los [kits de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits) de Laravel ya incluyen la configuración adecuada de Laravel, Inertia SSR y Vite. Echa un vistazo a [Laravel Breeze](/docs/%7B%7Bversion%7D%7D/starter-kits#breeze-and-inertia) para la forma más rápida de empezar con Laravel, Inertia SSR, y Vite.

[]()

## Atributos de las etiquetas Script y Style

[policy-csp-nonce">]()

### policy seguridad de contenidos (CSP) Nonce

Si desea incluir un [atributo`nonce`](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/nonce) en su script y etiquetas de estilo como parte de su [policy Seguridad de Contenidos](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP), puede generar o especificar un nonce utilizando el método `useCspNonce` dentro de un [middleware](/docs/%7B%7Bversion%7D%7D/middleware) personalizado:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Vite;

class AddContentSecurityPolicyHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Vite::useCspNonce();

        return $next($request)->withHeaders([
            'Content-Security-Policy' => "script-src 'nonce-".Vite::cspNonce()."'",
        ]);
    }
}
```

Después de invocar el método `useCspNonce`, Laravel incluirá automáticamente los atributos `nonce` en todas las etiquetas script y style generadas.

Si necesitas especificar el nonce en otro lugar, incluyendo la [directiva Ziggy `@route`](https://github.com/tighten/ziggy#using-routes-with-a-content-security-policy) incluida con los [kits de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits) de Laravel, puedes recuperarlo usando el método `cspNonce`:

```blade
@routes(nonce: Vite::cspNonce())
```

Si ya tienes un nonce que quieres que Laravel utilice, puedes pasar el nonce al método `useCspNonce`:

```php
Vite::useCspNonce($nonce);
```

[]()

### Integridad de Subrecursos (SRI)

Si su manifiesto Vite incluye hashes de `integridad` para sus activos, Laravel añadirá automáticamente el atributo de `integridad` en cualquier script y etiquetas de estilo que genere con el fin de hacer cumplir la [Integridad de Subrecursos](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity). Por defecto, Vite no incluye el hash de `integridad` en su manifiesto, pero puedes activarlo instalando el plugin NPM [`vite-plugin-manifest-uri`](https://www.npmjs.com/package/vite-plugin-manifest-sri):

```shell
npm install -D vite-plugin-manifest-sri
```

A continuación, puede habilitar este plugin en su archivo `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import manifestSRI from 'vite-plugin-manifest-sri';// [tl! add]

export default defineConfig({
    plugins: [
        laravel({
            // ...
        }),
        manifestSRI(),// [tl! add]
    ],
});
```

Si es necesario, también puede personalizar la clave del manifiesto donde se puede encontrar el hash de integridad:

```php
use Illuminate\Support\Facades\Vite;

Vite::useIntegrityKey('custom-integrity-key');
```

Si desea desactivar completamente esta autodetección, puede pasar `false` al método `useIntegrityKey`:

```php
Vite::useIntegrityKey(false);
```

[]()

### Atributos arbitrarios

Si necesitas incluir atributos adicionales en tus etiquetas script y style, como el atributo [`data-turbo-track`](https://turbo.hotwired.dev/handbook/drive#reloading-when-assets-change), puedes especificarlos mediante los métodos `useScriptTagAttributes` y `useStyleTagAttributes`. Normalmente, estos métodos deben invocarse desde un [proveedor de servicios](/docs/%7B%7Bversion%7D%7D/providers):

```php
use Illuminate\Support\Facades\Vite;

Vite::useScriptTagAttributes([
    'data-turbo-track' => 'reload', // Specify a value for the attribute...
    'async' => true, // Specify an attribute without a value...
    'integrity' => false, // Exclude an attribute that would otherwise be included...
]);

Vite::useStyleTagAttributes([
    'data-turbo-track' => 'reload',
]);
```

Si necesita añadir atributos condicionalmente, puede pasar una llamada de retorno que recibirá la ruta de origen del activo, su URL, su trozo de manifiesto y el manifiesto completo:

```php
use Illuminate\Support\Facades\Vite;

Vite::useScriptTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
    'data-turbo-track' => $src === 'resources/js/app.js' ? 'reload' : false,
]);

Vite::useStyleTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
    'data-turbo-track' => $chunk && $chunk['isEntry'] ? 'reload' : false,
]);
```

> **Advertencia**  
> Los argumentos `$chunk` y `$manifest` serán `nulos` mientras el servidor de desarrollo de Vite esté en ejecución.

[]()

## Personalización avanzada

Fuera de la caja, el plugin Vite de Laravel utiliza convenciones sensatas que deberían funcionar para la mayoría de las aplicaciones; sin embargo, a veces puede que necesites personalizar el comportamiento de Vite. Para habilitar opciones de personalización adicionales, ofrecemos los siguientes métodos y opciones que se pueden utilizar en lugar de la directiva `@vite` Blade:

```blade
<!doctype html>
<head>
    {{-- ... --}}

    {{
        Vite::useHotFile(storage_path('vite.hot')) // Customize the "hot" file...
            ->useBuildDirectory('bundle') // Customize the build directory...
            ->useManifestFilename('assets.json') // Customize the manifest filename...
            ->withEntryPoints(['resources/js/app.js']) // Specify the entry points...
    }}
</head>
```

Dentro del archivo `vite.config.js`, deberá especificar la misma configuración:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            hotFile: 'storage/vite.hot', // Customize the "hot" file...
            buildDirectory: 'bundle', // Customize the build directory...
            input: ['resources/js/app.js'], // Specify the entry points...
        }),
    ],
    build: {
      manifest: 'assets.json', // Customize the manifest filename...
    },
});
```
