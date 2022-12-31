# Desarrollo de paquetes

- [Introducción](#introduction)
  - [Nota sobre facades](#a-note-on-facades)
- [Descubrimiento de paquetes](#package-discovery)
- [Proveedores de servicios](#service-providers)
- [Recursos](#resources)
  - [Configuración](#configuration)
  - [Migraciones](#migrations)
  - [Rutas](#routes)
  - [Traducciones](#translations)
  - [Vistas](#views)
  - [Ver componentes](#view-components)
  - ["Acerca de Artisan](#about-artisan-command)
- [Comandos](#commands)
- [Activos públicos](#public-assets)
- [Publicando Grupos de Archivos](#publishing-file-groups)

[]()

## Introducción

Los paquetes son la forma principal de añadir funcionalidad a Laravel. Los paquetes pueden ser cualquier cosa, desde una gran manera de trabajar con fechas como [Carbon](https://github.com/briannesbitt/Carbon) o un paquete que te permite asociar archivos con modelos Eloquent como Spatie's [Laravel Media Library](https://github.com/spatie/laravel-medialibrary).

Existen diferentes tipos de paquetes. Algunos paquetes son independientes, lo que significa que funcionan con cualquier framework PHP. Carbon y PHPUnit son ejemplos de paquetes independientes. Cualquiera de estos paquetes puede ser usado con Laravel requiriéndolos en tu archivo `composer.json`.

Por otro lado, otros paquetes están diseñados específicamente para su uso con Laravel. Estos paquetes pueden tener rutas, controladores, vistas, y la configuración destinada específicamente a mejorar una aplicación Laravel. Esta guía cubre principalmente el desarrollo de los paquetes que son específicos de Laravel.

[]()

### Nota sobre facades

Al escribir una aplicación Laravel, por lo general no importa si se utilizan contratos o facades, ya que ambos proporcionan esencialmente los mismos niveles de comprobabilidad. Sin embargo, al escribir paquetes, su paquete no suelen tener acceso a todos los ayudantes de pruebas de Laravel. Si desea poder escribir las tests su paquete como si el paquete estuviera instalado dentro de una aplicación Laravel típica, puede utilizar el paquete [Orchestral Testbench](https://github.com/orchestral/testbench).

[]()

## Descubrimiento de paquetes

En el archivo de configuración `config/app.php` de una aplicación Laravel, la opción `providers` define una lista de proveedores de servicios que deben ser cargados por Laravel. Cuando alguien instala tu paquete, normalmente querrás que tu proveedor de servicios esté incluido en esta lista. En lugar de pedir a los usuarios que añadan manualmente tu proveedor de servicios a la lista, puedes definir el proveedor en la sección `extra` del archivo `composer.json` de tu paquete. Además de los proveedores de servicios, también puede listar cualquier [facades](/docs/%7B%7Bversion%7D%7D/facades) que desee registrar:

```json
"extra": {
    "laravel": {
        "providers": [
            "Barryvdh\\Debugbar\\ServiceProvider"
        ],
        "aliases": {
            "Debugbar": "Barryvdh\\Debugbar\\Facade"
        }
    }
},
```

Una vez que su paquete ha sido configurado para el descubrimiento, Laravel registrará automáticamente sus proveedores de servicios y facades cuando se instala, creando una experiencia de instalación conveniente para los usuarios de su paquete.

[]()

### Exclusión de la detección de paquetes

Si eres el consumidor de un paquete y quieres desactivar el descubrimiento de un paquete, puedes listar el nombre del paquete en la sección `extra` del archivo `composer.json` de tu aplicación:

```json
"extra": {
    "laravel": {
        "dont-discover": [
            "barryvdh/laravel-debugbar"
        ]
    }
},
```

Puede desactivar la detección de paquetes para todos los paquetes utilizando el carácter `*` dentro de la directiva `dont-discover` de su aplicación:

```json
"extra": {
    "laravel": {
        "dont-discover": [
            "*"
        ]
    }
},
```

[]()

## Proveedores de servicios

Los proveedores de[servicios](/docs/%7B%7Bversion%7D%7D/providers) son el punto de conexión entre tu paquete y Laravel. Un proveedor de servicios es responsable de enlazar cosas en el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) de Laravel e informar a Laravel dónde cargar los recursos del paquete, tales como vistas, configuración y archivos de localización.

Un proveedor de servicios extiende la clase `Illuminate\Support\ServiceProvider` y contiene dos métodos: `register` y `boot`. La clase `ServiceProvider` base se encuentra en el paquete `illuminate/support` Composer, que debe añadir a las dependencias de su propio paquete. Para obtener más información sobre la estructura y la finalidad de los proveedores de servicios, consulta [su documentación](/docs/%7B%7Bversion%7D%7D/providers).

[]()

## Recursos

[]()

### Configuración

Normalmente, tendrá que publicar el archivo de configuración de su paquete en el directorio `config` de la aplicación. Esto permitirá a los usuarios de su paquete anular fácilmente sus opciones de configuración por defecto. Para permitir que sus archivos de configuración sean publicados, llame al método `publishes` desde el método `boot` de su proveedor de servicios:

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/courier.php' => config_path('courier.php'),
        ]);
    }

Ahora, cuando los usuarios de tu paquete ejecuten el comando `vendor:publish` de Laravel, tu archivo será copiado a la ubicación de publicación especificada. Una vez que tu configuración ha sido publicada, se puede acceder a sus valores como a cualquier otro archivo de configuración:

    $value = config('courier.option');

> **Advertencia**  
> No debes definir closures en tus ficheros de configuración. No pueden ser serializados correctamente cuando los usuarios ejecutan el comando `config:cache` Artisan.

[]()

#### Configuración de paquetes por defecto

También puede fusionar su propio archivo de configuración de paquetes con la copia publicada de la aplicación. Esto permitirá a sus usuarios definir sólo las opciones que realmente desean anular en la copia publicada del archivo de configuración. Para fusionar los valores del archivo de configuración, utilice el método `mergeConfigFrom` dentro del método `register` de su proveedor de servicios.

El método `mergeConfigFrom` acepta la ruta al archivo de configuración del paquete como primer argumento y el nombre de la copia del archivo de configuración de la aplicación como segundo argumento:

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/courier.php', 'courier'
        );
    }

> **Advertencia**  
> Este método sólo fusiona el primer nivel de la array configuración. Si tus usuarios definen parcialmente un array de configuración multidimensional, las opciones que falten no se fusionarán.

[]()

### Rutas

Si su paquete contiene rutas, puede cargarlas usando el método `loadRoutesFrom`. Este método determinará automáticamente si las rutas de la aplicación están almacenadas en caché y no cargará su archivo de rutas si las rutas ya han sido almacenadas en caché:

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

[]()

### Migraciones

Si tu paquete contiene [migraciones de base de datos](/docs/%7B%7Bversion%7D%7D/migrations), puedes usar el método `loadMigrationsFrom` para informar a Laravel cómo cargarlas. El método `loadMigrationsFrom` acepta como único argumento la ruta a las migraciones de tu paquete:

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

Una vez registradas las migraciones de su paquete, se ejecutarán automáticamente cuando se ejecute el comando `php artisan migrate`. No es necesario exportarlas al directorio `base de datos/migraciones` de la aplicación.

[]()

### Traducciones

Si tu paquete contiene [archivos de traducción](/docs/%7B%7Bversion%7D%7D/localization), puedes usar el método `loadTranslationsFrom` para informar a Laravel cómo cargarlos. Por ejemplo, si tu paquete se llama `courier`, deberías añadir lo siguiente al método `boot` de tu proveedor de servicios:

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'courier');
    }

Las traducciones de paquetes se referencian utilizando la convención sintáctica `package::file.line`. Por lo tanto, puede cargar la línea de `bienvenida` del paquete `courier` desde el archivo de `mensajes` de esta manera:

    echo trans('courier::messages.welcome');

[]()

#### Publicación de traducciones

Si desea publicar las traducciones de su paquete en el directorio `lang/vendor` de la aplicación, puede utilizar el método `publishes` del proveedor de servicios. El método `publishes` acepta una array de rutas de paquetes y sus ubicaciones de publicación deseadas. Por ejemplo, para publicar los archivos de traducción del paquete `courier`, puede hacer lo siguiente:

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'courier');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/courier'),
        ]);
    }

Ahora, cuando los usuarios de tu paquete ejecuten el comando `vendor:publish` Artisan de Laravel, las traducciones de tu paquete serán publicadas en la ubicación de publicación especificada.

[]()

### Vistas

Para registrar las [vistas](/docs/%7B%7Bversion%7D%7D/views) de tu paquete con Laravel, necesitas decirle a Laravel dónde están ubicadas las vistas. Puedes hacerlo utilizando el método `loadViewsFrom` del proveedor de servicios. El método `loadViewsFrom` acepta dos argumentos: la ruta a tus plantillas de vistas y el nombre de tu paquete. Por ejemplo, si el nombre de su paquete es `courier`, añadiría lo siguiente al método `boot` de su proveedor de servicios:

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'courier');
    }

Las vistas de los paquetes son referenciadas usando la convención sintáctica `package::view`. Por lo tanto, una vez que tu ruta de vista está registrada en un proveedor de servicios, puedes cargar la vista `del tablero` desde el paquete `courier` de esta manera:

    Route::get('/dashboard', function () {
        return view('courier::dashboard');
    });

[]()

#### Anulación de vistas de paquete

Cuando utilizas el método `loadViewsFrom`, Laravel en realidad registra dos ubicaciones para tus vistas: el directorio `resources/views/vendor` de la aplicación y el directorio que especifiques. Así, utilizando el paquete `courier` como ejemplo, Laravel comprobará primero si una versión personalizada de la vista ha sido colocada en el directorio `resources/views/vendor/courier` por el desarrollador. Luego, si la vista no ha sido personalizada, Laravel buscará en el directorio de vistas del paquete que especificaste en tu llamada a `loadViewsFrom`. Esto facilita a los usuarios del paquete personalizar / anular las vistas de tu paquete.

[]()

#### Publicando Vistas

Si quieres que tus vistas estén disponibles para su publicación en el directorio `resources/views/vendor` de la aplicación, puedes utilizar el método `publishes` del proveedor de servicios. El método `publishes` acepta un array de rutas de vistas del paquete y sus ubicaciones de publicación deseadas:

    /**
     * Bootstrap the package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'courier');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/courier'),
        ]);
    }

Ahora, cuando los usuarios de tu paquete ejecuten el comando `vendor:publish` Artisan de Laravel, las vistas de tu paquete serán copiadas a la ubicación de publicación especificada.

[]()

### Componentes de vista

Si estás construyendo un paquete que utiliza componentes Blade o colocando componentes en directorios no convencionales, necesitarás registrar manualmente tu clase de componente y su alias de etiqueta HTML para que Laravel sepa dónde encontrar el componente. Por lo general, debe registrar sus componentes en el método de `arranque` del proveedor de servicios de su paquete:

    use Illuminate\Support\Facades\Blade;
    use VendorPackage\View\Components\AlertComponent;

    /**
     * Bootstrap your package's services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('package-alert', AlertComponent::class);
    }

Una vez que su componente ha sido registrado, puede ser renderizado utilizando su alias de etiqueta:

```blade
<x-package-alert/>
```

[]()

#### Autoloading Package Components

Alternativamente, puede utilizar el método `componentNamespace` para autocargar clases de componentes por convención. Por ejemplo, un paquete `Nightshade` puede tener componentes `Calendar` y `ColorPicker` que residan dentro del espacio de nombres `Nightshade\Views\Components`:

    use Illuminate\Support\Facades\Blade;

    /**
     * Bootstrap your package's services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
    }

Esto permitirá el uso de componentes de paquete por su espacio de nombres de proveedor usando la sintaxis `package-name::`:

```blade
<x-nightshade::calendar />
<x-nightshade::color-picker />
```

Blade detectará automáticamente la clase vinculada a este componente escribiendo el nombre del componente en pascal. También se admiten subdirectorios utilizando la notación "punto".

[]()

#### Componentes anónimos

Si su paquete contiene componentes anónimos, deben colocarse dentro de un directorio de `componentes` del directorio "views" de su paquete (como se especifica en el [método`loadViewsFrom`](#views)). Luego, puede renderizarlos anteponiendo al nombre del componente el espacio de nombres de la vista del paquete:

```blade
<x-courier::alert />
```

[]()

### "Comando Artisan "Acerca de

El comando integrado de Laravel `about` Artisan proporciona una sinopsis del entorno y configuración de la aplicación. Los paquetes pueden añadir información adicional a la salida de este comando a través de la clase `AboutCommand`. Típicamente, esta información puede ser añadida desde el método de `arranque` de tu proveedor de servicios de paquetes:

    use Illuminate\Foundation\Console\AboutCommand;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        AboutCommand::add('My Package', fn () => ['Version' => '1.0.0']);
    }

[]()

## Comandos

Para registrar los comandos Artisan de tu paquete con Laravel, puedes utilizar el método `commands`. Este método espera un array de nombres de clases de comandos. Una vez que los comandos han sido registrados, puedes ejecutarlos usando el [Artisan CLI](/docs/%7B%7Bversion%7D%7D/artisan):

    use Courier\Console\Commands\InstallCommand;
    use Courier\Console\Commands\NetworkCommand;

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                NetworkCommand::class,
            ]);
        }
    }

[]()

## Activos públicos

Su paquete puede tener activos como JavaScript, CSS e imágenes. Para publicar estos activos en el directorio `público` de la aplicación, utilice el método `publishes` del proveedor de servicios. En este ejemplo, también añadiremos una etiqueta `pública` de grupo de activos, que puede utilizarse para publicar fácilmente grupos de activos relacionados:

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/courier'),
        ], 'public');
    }

Ahora, cuando los usuarios de su paquete ejecuten el comando `vendor:publish`, sus activos se copiarán en la ubicación de publicación especificada. Dado que los usuarios normalmente tendrán que sobrescribir los activos cada vez que se actualice el paquete, puede utilizar el indicador `--force`:

```shell
php artisan vendor:publish --tag=public --force
```

[]()

## Publicación de grupos de archivos

Es posible que desee publicar grupos de activos de paquetes y recursos por separado. Por ejemplo, puede que quieras permitir a tus usuarios publicar los archivos de configuración de tu paquete sin ser forzado a publicar los activos de tu paquete. Puede hacer esto "etiquetándolos" cuando llame al método `publishes` desde el proveedor de servicios de un paquete. Por ejemplo, usemos etiquetas para definir dos grupos de publicación para el paquete `courier``(courier-config` y `courier-migrations`) en el método `boot` del proveedor de servicios del paquete:

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/package.php' => config_path('package.php')
        ], 'courier-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'courier-migrations');
    }

Ahora sus usuarios pueden publicar estos grupos por separado haciendo referencia a su etiqueta al ejecutar el comando vendor: `publish`:

```shell
php artisan vendor:publish --tag=courier-config
```
