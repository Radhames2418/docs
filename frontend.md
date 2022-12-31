# Frontend

- [Introducción](#introduction)
- [Uso de PHP](#using-php)
  - [PHP y Blade](#php-and-blade)
  - [Livewire](#livewire)
  - [Kits de inicio](#php-starter-kits)
- [Uso de Vue / React](#using-vue-react)
  - [Inercia](#inertia)
  - [Kits de inicio](#inertia-starter-kits)
- [Agrupación de activos](#bundling-assets)

[]()

## Introducción

Laravel es un framework de backend que proporciona todas las características que necesitas para construir aplicaciones web modernas, como [enrutamiento](/docs/%7B%7Bversion%7D%7D/routing), [validación](/docs/%7B%7Bversion%7D%7D/validation), [almacenamiento en caché](/docs/%7B%7Bversion%7D%7D/cache), [colas](/docs/%7B%7Bversion%7D%7D/queues), [almacenamiento de archivos](/docs/%7B%7Bversion%7D%7D/filesystem) y más. Sin embargo, creemos que es importante ofrecer a los desarrolladores una hermosa experiencia de pila completa, incluyendo enfoques de gran alcance para la construcción del front-end de su aplicación.

Hay dos formas principales de abordar el desarrollo frontend cuando se construye una aplicación con Laravel, y el enfoque que elijas está determinado por si deseas construir tu frontend aprovechando PHP o utilizando frameworks de JavaScript como Vue y React. Vamos a discutir estas dos opciones a continuación para que pueda tomar una decisión informada sobre el mejor enfoque para el desarrollo frontend para su aplicación.

[]()

## Usando PHP

[]()

### PHP y Blade

En el pasado, la mayoría de las aplicaciones PHP renderizaban HTML al navegador usando simples plantillas HTML intercaladas con sentencias `echo` de PHP que renderizaban datos que eran recuperados de una base de datos durante la petición:

```blade
<div>
    <?php foreach ($users as $user): ?>
        Hello, <?php echo $user->name; ?> <br />
    <?php endforeach; ?>
</div>
```

En Laravel, este enfoque para renderizar HTML todavía se puede lograr utilizando [vistas](/docs/%7B%7Bversion%7D%7D/views) y [Blade](/docs/%7B%7Bversion%7D%7D/blade). Blade es un lenguaje de plantillas extremadamente ligero que proporciona una sintaxis cómoda y corta para mostrar datos, iterar sobre datos y mucho más:

```blade
<div>
    @foreach ($users as $user)
        Hello, {{ $user->name }} <br />
    @endforeach
</div>
```

Cuando se construyen aplicaciones de esta manera, los envíos de formularios y otras interacciones de página típicamente reciben un documento HTML completamente nuevo desde el servidor y la página entera es re-renderizada por el navegador. Incluso hoy en día, muchas aplicaciones pueden ser perfectamente adecuadas para tener sus frontends construidos de esta manera usando simples plantillas Blade.

[]()

#### Expectativas crecientes

Sin embargo, a medida que las expectativas de los usuarios con respecto a las aplicaciones web han ido madurando, muchos desarrolladores se han visto en la necesidad de construir frontends más dinámicos con interacciones que parezcan más pulidas. A la luz de esto, algunos desarrolladores optan por comenzar a construir el frontend de su aplicación utilizando frameworks de JavaScript como Vue y React.

Otros, que prefieren quedarse con el lenguaje de backend con el que se sienten cómodos, han desarrollado soluciones que permiten la construcción de interfaces de usuario de aplicaciones web modernas sin dejar de utilizar principalmente el lenguaje de backend de su elección. Por ejemplo, en el ecosistema [Rails](https://rubyonrails.org/), esto ha impulsado la creación de bibliotecas como [Turbo](https://turbo.hotwired.dev/) [Hotwire](https://hotwired.dev/) y [Stimulus](https://stimulus.hotwired.dev/).

Dentro del ecosistema Laravel, la necesidad de crear frontends modernos y dinámicos utilizando principalmente PHP ha llevado a la creación de [Laravel Live](https://laravel-livewire.com) wire y [Alpine.js](https://alpinejs.dev/).

[]()

### Livewire

[Laravel Livewire](https://laravel-livewire.com) es un marco para la construcción de interfaces de Laravel que se sienten dinámicos, modernos y vivos al igual que los frontales construidos con marcos modernos de JavaScript como Vue y React.

Al utilizar Livewire, crearás "componentes" Livewire que renderizan una porción discreta de tu interfaz de usuario y exponen métodos y datos que pueden ser invocados e interactuados desde el frontend de tu aplicación. Por ejemplo, un simple componente "Contador" podría parecerse a lo siguiente:

```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
```

Y, la plantilla correspondiente para el contador se escribiría así:

```blade
<div>
    <button wire:click="increment">+</button>
    <h1>{{ $count }}</h1>
</div>
```

Como puedes ver, Livewire te permite escribir nuevos atributos HTML como `wire:click` que conectan el frontend y el backend de tu aplicación Laravel. Además, puedes renderizar el estado actual de tu componente usando simples expresiones Blade.

Para muchos, Livewire ha revolucionado el desarrollo frontend con Laravel, permitiéndoles permanecer dentro de la comodidad de Laravel mientras construyen aplicaciones web modernas y dinámicas. Por lo general, los desarrolladores que utilizan Livewire también utilizarán [Alpine.js](https://alpinejs.dev/) para "espolvorear" JavaScript en su frontend sólo cuando sea necesario, por ejemplo, con el fin de representar una ventana de diálogo.

Si eres nuevo en Laravel, te recomendamos familiarizarte con el uso básico de [vistas](/docs/%7B%7Bversion%7D%7D/views) y [Blade](/docs/%7B%7Bversion%7D%7D/blade). Después, consulta la [documentación](https://laravel-livewire.com/docs) oficial de [Laravel Livewire](https://laravel-livewire.com/docs) para aprender a llevar tu aplicación al siguiente nivel con componentes interactivos Livewire.

[]()

### Kits de inicio

Si desea construir su frontend utilizando PHP y Livewire, puede aprovechar nuestros [kits](/docs/%7B%7Bversion%7D%7D/starter-kits) de inicio Breeze o Jetstream para poner en marcha el desarrollo de su aplicación. Ambos kits de inicio organizan el flujo de autenticación del backend y el frontend de tu aplicación utilizando [Blade](/docs/%7B%7Bversion%7D%7D/blade) y [Tailwind](https://tailwindcss.com) para que puedas empezar a construir tu próxima gran idea.

[]()

## Uso de Vue / React

Aunque es posible construir frontends modernos utilizando Laravel y Livewire, muchos desarrolladores siguen prefiriendo aprovechar la potencia de un framework JavaScript como Vue o React. Esto permite a los desarrolladores aprovechar el rico ecosistema de paquetes y herramientas JavaScript disponibles a través de NPM.

Sin embargo, sin herramientas adicionales, emparejar Laravel con Vue o React nos dejaría con la necesidad de resolver una variedad de problemas complicados como el enrutamiento del lado del cliente, la hidratación de datos y la autenticación. El enrutamiento del lado del cliente a menudo se simplifica mediante el uso de frameworks Vue / React como [Nuxt](https://nuxtjs.org/) y [Next](https://nextjs.org/); sin embargo, la hidratación de datos y la autenticación siguen siendo problemas complicados y engorrosos de resolver cuando se empareja un framework backend como Laravel con estos frameworks frontend.

Además, los desarrolladores se ven obligados a mantener dos repositorios de código separados, a menudo con la necesidad de coordinar el mantenimiento, las versiones y los despliegues en ambos repositorios. Aunque estos problemas no son insuperables, no creemos que sea una forma productiva o agradable de desarrollar aplicaciones.

[]()

### Inercia

Afortunadamente, Laravel ofrece lo mejor de ambos mundos. [Inertia](https://inertiajs.com) tiende un puente entre su aplicación Laravel y su moderno frontend Vue o React, permitiéndole construir frontends completos y modernos usando Vue o React mientras aprovecha las rutas y controladores de Laravel para enrutamiento, hidratación de datos y autenticación - todo dentro de un único repositorio de código. Con este enfoque, se puede disfrutar de toda la potencia de Laravel y Vue / React sin paralizar las capacidades de cualquiera de las herramientas.

Después de instalar Inertia en tu aplicación Laravel, escribirás rutas y controladores como de costumbre. Sin embargo, en lugar de devolver una plantilla Blade desde su controlador, devolverá una página Inertia:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        return Inertia::render('Users/Profile', [
            'user' => User::findOrFail($id)
        ]);
    }
}
```

Una página Inertia corresponde a un componente Vue o React, normalmente almacenado en el directorio `resources/js/Pages` de su aplicación. Los datos proporcionados a la página a través del método `Inertia::render` se utilizarán para hidratar las "props" del componente de la página:

```vue
<script setup>
import Layout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';

const props = defineProps(['user']);
</script>

<template>
    <Head title="User Profile" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div class="py-12">
            Hello, {{ user.name }}
        </div>
    </Layout>
</template>
```

Como puede ver, Inertia le permite aprovechar toda la potencia de Vue o React cuando construye su frontend, al tiempo que proporciona un puente ligero entre su backend impulsado por Laravel y su frontend impulsado por JavaScript.

#### Renderizado del lado del servidor

Si le preocupa sumergirse en Inertia porque su aplicación requiere renderizado del lado del servidor, no se preocupe. Inertia ofrece [soporte de](https://inertiajs.com/server-side-rendering) renderizado del lado del servidor. Y, al desplegar su aplicación a través de [Laravel Forge](https://forge.laravel.com), es muy fácil asegurarse de que el proceso de renderizado del lado del servidor de Inertia está siempre en ejecución.

[]()

### Kits de inicio

Si deseas construir tu frontend utilizando Inertia y Vue / React, puedes aprovechar nuestros [kits](/docs/%7B%7Bversion%7D%7D/starter-kits#breeze-and-inertia) de inicio Breeze o Jetstream para poner en marcha el desarrollo de tu aplicación. Ambos kits de inicio organizan el flujo de autenticación del backend y frontend de tu aplicación utilizando Inertia, Vue / React, [Tailwind](https://tailwindcss.com) y [Vite](https://vitejs.dev) para que puedas comenzar a construir tu próxima gran idea.

[]()

## Agrupación de activos

Independientemente de si decide desarrollar su frontend utilizando Blade y Livewire o Vue / React e Inertia, es probable que tenga que agrupar el CSS de su aplicación en activos listos para la producción. Por supuesto, si eliges construir el frontend de tu aplicación con Vue o React, también necesitarás empaquetar tus componentes en activos JavaScript listos para el navegador.

Por defecto, Laravel utiliza [Vite](https://vitejs.dev) para agrupar sus activos. Vite proporciona tiempos de construcción ultrarrápidos y Hot Module Replacement (HMR) casi instantáneo durante el desarrollo local. En todas las nuevas aplicaciones Laravel, incluyendo aquellas que utilizan nuestros [kits de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits), encontrarás un archivo `vite.config.js` que carga nuestro ligero plugin Laravel Vite que hace que Vite sea un placer de usar con aplicaciones Laravel.

La forma más rápida de empezar con Laravel y Vite es comenzar el desarrollo de tu aplicación utilizando [Laravel Breeze](/docs/%7B%7Bversion%7D%7D/starter-kits#laravel-breeze), nuestro kit de inicio más simple que pone en marcha tu aplicación proporcionando un andamiaje de autenticación frontend y backend.

> **Nota**<br/>Para una documentación más detallada sobre la utilización de Vite con Laravel, por favor consulte nuestra [documentación dedicada a la agrupación y compilación de sus activos](/docs/%7B%7Bversion%7D%7D/vite).
