# Vistas

- [Introducción](#introduction)
  - [Escribiendo Vistas en React / Vue](#writing-views-in-react-or-vue)
- [Creación y renderizado de vistas](#creating-and-rendering-views)
  - [Directorios de Vistas Anidadas](#nested-view-directories)
  - [Creación de la primera vista disponible](#creating-the-first-available-view)
  - [Determinación de la Existencia de una Vista](#determining-if-a-view-exists)
- [Pasando Datos a Vistas](#passing-data-to-views)
  - [Compartir datos con todas las vistas](#sharing-data-with-all-views)
- [Compositores de Vistas](#view-composers)
  - [Creadores de vistas](#view-creators)
- [Optimización de vistas](#optimizing-views)

[]()

## Introducción

Por supuesto, no es práctico devolver cadenas enteras de documentos HTML directamente desde tus rutas y controladores. Afortunadamente, las vistas proporcionan una forma conveniente de colocar todo nuestro HTML en archivos separados.

Las vistas separan tu lógica de controlador / aplicación de tu lógica de presentación y se almacenan en el directorio `resources/views`. Cuando se utiliza Laravel, las plantillas de vistas se escriben normalmente utilizando el [lenguaje de plantillas Blade](/docs/%7B%7Bversion%7D%7D/blade). Una vista sencilla podría tener este aspecto:

```blade
<!-- View stored in resources/views/greeting.blade.php -->

<html>
    <body>
        <h1>Hello, {{ $name }}</h1>
    </body>
</html>
```

Dado que esta vista está almacenada en `resources/views/greeting.blade.php`, podemos devolverla utilizando el ayudante de `vista` global de esta forma:

    Route::get('/', function () {
        return view('greeting', ['name' => 'James']);
    });

> **Nota**  
> ¿Buscas más información sobre cómo escribir plantillas Blade? Echa un vistazo a la [documentación](/docs/%7B%7Bversion%7D%7D/blade) completa de Blade para empezar.

[]()

### Escribiendo Vistas en React / Vue

En lugar de escribir sus plantillas frontales en PHP a través de Blade, muchos desarrolladores han comenzado a preferir escribir sus plantillas utilizando React o Vue. Laravel hace esto sin dolor gracias a [Inertia](https://inertiajs.com/), una biblioteca que hace que sea un juego de niños para atar su frontend React / Vue a su backend Laravel sin las complejidades típicas de la construcción de un SPA.

Nuestros [kits de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits) Breeze y Jetstream le dan un gran punto de partida para su próxima aplicación Laravel impulsada por Inertia. Además, el [Laravel Bootcamp](https://bootcamp.laravel.com) proporciona una demostración completa de la construcción de una aplicación Laravel impulsada por Inertia, incluyendo ejemplos en Vue y React.

[]()

## Creando y renderizando vistas

Puede crear una vista colocando un archivo con la extensión .blade `.` php en el directorio `resources/views` de su aplicación. La extensión . `blade.` php informa al framework de que el archivo contiene una [plantilla Blade](/docs/%7B%7Bversion%7D%7D/blade). Las plantillas Blade contienen HTML así como directivas Blade que le permiten fácilmente hacer eco de valores, crear sentencias "if", iterar sobre datos y más.

Una vez que has creado una vista, puedes devolverla desde una de las rutas o controladores de tu aplicación usando el ayudante de `vista` global:

    Route::get('/', function () {
        return view('greeting', ['name' => 'James']);
    });

Las vistas también pueden ser devueltas usando la facade `View`:

    use Illuminate\Support\Facades\View;

    return View::make('greeting', ['name' => 'James']);

Como puede ver, el primer argumento pasado al ayudante de `vista` corresponde al nombre del archivo de vista en el directorio `resources/views`. El segundo argumento es un array de datos que deben estar disponibles para la vista. En este caso, estamos pasando la variable `nombre`, que se muestra en la vista utilizando la [sintaxis Blade](/docs/%7B%7Bversion%7D%7D/blade).

[]()

### Directorios de Vistas Anidadas

Las vistas también pueden anidarse dentro de subdirectorios del directorio `resources/views`. Se puede utilizar la notación "punto" para hacer referencia a las vistas anidadas. Por ejemplo, si su vista está almacenada en `resources/views/admin/profile.blade.`php, puede devolverla desde una de las rutas / controladores de su aplicación de esta forma:

    return view('admin.profile', $data);

> **Advertencia**  
> Los nombres de directorio de las vistas no deben contener el carácter `.`.

[]()

### Creando la Primera Vista Disponible

Utilizando el método `first` de la facade `View`, puedes crear la primera vista que exista en un array de vistas dado. Esto puede ser útil si su aplicación o paquete permite que las vistas sean personalizadas o sobrescritas:

    use Illuminate\Support\Facades\View;

    return View::first(['custom.admin', 'admin'], $data);

[]()

### Determinación de la Existencia de una Vista

Si necesita determinar si una vista existe, puede utilizar la facade `View`. El método `exists` devolverá `true` si la vista existe:

    use Illuminate\Support\Facades\View;

    if (View::exists('emails.customer')) {
        //
    }

[]()

## Pasando Datos a Vistas

Como has visto en los ejemplos anteriores, puedes pasar un array de datos a las vistas para que esos datos estén disponibles para la vista:

    return view('greetings', ['name' => 'Victoria']);

Cuando se pasa información de esta manera, los datos deben ser un array con pares clave / valor. Después de proporcionar datos a una vista, puede acceder a cada valor dentro de su vista utilizando las claves de los datos, como `<?php echo $nombre; ?>`.

Como alternativa a pasar un array completo de datos a la función de ayuda de `la` vista, puede utilizar el método `with` para añadir datos individuales a la vista. El método `with` devuelve una instancia del objeto view para que pueda continuar encadenando métodos antes de devolver la vista:

    return view('greeting')
                ->with('name', 'Victoria')
                ->with('occupation', 'Astronaut');

[]()

### Compartir datos con todas las vistas

Ocasionalmente, puede necesitar compartir datos con todas las vistas que son renderizadas por su aplicación. Puede hacerlo utilizando el método `share` de la facade `View`. Típicamente, deberías colocar llamadas al método `share` dentro del método `boot` de un proveedor de servicios. Eres libre de añadirlos a la clase `AppProviders\AppServiceProvider` o generar un proveedor de servicios separado para alojarlos:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\View;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register()
        {
            //
        }

        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot()
        {
            View::share('key', 'value');
        }
    }

[]()

## Compositores de Vistas

Los compositores de vistas son callbacks o métodos de clase que se llaman cuando se renderiza una vista. Si tienes datos que quieres vincular a una vista cada vez que ésta se renderiza, un compositor de vistas puede ayudarte a organizar esa lógica en una única ubicación. Los compositores de vistas pueden ser particularmente útiles si la misma vista es devuelta por múltiples rutas o controladores dentro de tu aplicación y siempre necesita un dato en particular.

Normalmente, los compositores de vistas se registrarán dentro de uno de los proveedores de [servicios](/docs/%7B%7Bversion%7D%7D/providers) de tu aplicación. En este ejemplo, asumiremos que hemos creado un nuevo `AppProviders\ViewServiceProvider` para alojar esta lógica.

Utilizaremos el método `composer` de la facade `View` para registrar el compositor de vistas. Laravel no incluye un directorio por defecto para los compositores de vistas basados en clases, así que eres libre de organizarlos como quieras. Por ejemplo, puedes crear un directorio `app/View/Composers` para alojar todos los compositores de vistas de tu aplicación:

    <?php

    namespace App\Providers;

    use App\View\Composers\ProfileComposer;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\ServiceProvider;

    class ViewServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register()
        {
            //
        }

        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot()
        {
            // Using class based composers...
            View::composer('profile', ProfileComposer::class);

            // Using closure based composers...
            View::composer('dashboard', function ($view) {
                //
            });
        }
    }

> **Advertencia**  
> Recuerda, si creas un nuevo proveedor de servicios para contener tus registros de compositores de vistas, necesitarás añadir el proveedor de servicios a la array `proveedores` en el archivo de configuración `config/app.` php.

Ahora que hemos registrado el compositor, el método `compose` de la clase `App\View\Composers\ProfileComposer` será ejecutado cada vez que la vista de `perfil` sea renderizada. Veamos un ejemplo de la clase composer:

    <?php

    namespace App\View\Composers;

    use App\Repositories\UserRepository;
    use Illuminate\View\View;

    class ProfileComposer
    {
        /**
         * The user repository implementation.
         *
         * @var \App\Repositories\UserRepository
         */
        protected $users;

        /**
         * Create a new profile composer.
         *
         * @param  \App\Repositories\UserRepository  $users
         * @return void
         */
        public function __construct(UserRepository $users)
        {
            $this->users = $users;
        }

        /**
         * Bind data to the view.
         *
         * @param  \Illuminate\View\View  $view
         * @return void
         */
        public function compose(View $view)
        {
            $view->with('count', $this->users->count());
        }
    }

Como puedes ver, todos los compositores de vistas se resuelven a través del [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container), por lo que puedes escribir cualquier dependencia que necesites dentro del constructor de un compositor.

[]()

#### Adjuntar un compositor a múltiples vistas

Puede adjuntar un compositor de vistas a múltiples vistas a la vez pasando un array de vistas como primer argumento al método `composer`:

    use App\Views\Composers\MultiComposer;

    View::composer(
        ['profile', 'dashboard'],
        MultiComposer::class
    );

El método `composer` también acepta el carácter `*` como comodín, permitiéndole adjuntar un compositor a todas las vistas:

    View::composer('*', function ($view) {
        //
    });

[]()

### Creadores de vistas

Los "creadores" de vistas son muy similares a los compositores de vistas; sin embargo, se ejecutan inmediatamente después de instanciar la vista, en lugar de esperar a que la vista esté a punto de renderizarse. Para registrar un creador de vistas, utiliza el método `creator`:

    use App\View\Creators\ProfileCreator;
    use Illuminate\Support\Facades\View;

    View::creator('profile', ProfileCreator::class);

[]()

## Optimización de vistas

Por defecto, las vistas de plantilla Blade se compilan bajo demanda. Cuando se ejecuta una petición que renderiza una vista, Laravel determinará si existe una versión compilada de la vista. Si el fichero existe, Laravel determinará si la vista no compilada ha sido modificada más recientemente que la vista compilada. Si la vista compilada no existe, o la vista no compilada ha sido modificada, Laravel recompilará la vista.

Compilar las vistas durante la petición puede tener un pequeño impacto negativo en el rendimiento, por lo que Laravel proporciona el comando `view:cache` Artisan para precompilar todas las vistas utilizadas por tu aplicación. Para aumentar el rendimiento, es posible que desees ejecutar este comando como parte de tu proceso de despliegue:

```shell
php artisan view:cache
```

Puedes utilizar el comando `view:clear` para borrar la cache de vistas:

```shell
php artisan view:clear
```
