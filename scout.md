# Laravel Scout

- [Introducción](#introduction)
- [Instalación](#installation)
  - [Requisitos previos del controlador](#driver-prerequisites)
  - [Puesta en cola](#queueing)
- [Configuración](#configuration)
  - [Configuración de índices de modelos](#configuring-model-indexes)
  - [Configuración de datos buscables](#configuring-searchable-data)
  - [Configuración del ID de modelo](#configuring-the-model-id)
  - [Configuración de motores de búsqueda por modelo](#configuring-search-engines-per-model)
  - [Identificación de usuarios](#identifying-users)
- [Base de datos / Motores de colección](#database-and-collection-engines)
  - [Motor de base de datos](#database-engine)
  - [Motor de recogida](#collection-engine)
- [Indexación](#indexing)
  - [Importación por lotes](#batch-import)
  - [Añadir registros](#adding-records)
  - [Actualización de registros](#updating-records)
  - [Eliminación de registros](#removing-records)
  - [Pausa de indexación](#pausing-indexing)
  - [Búsqueda condicional de instancias del modelo](#conditionally-searchable-model-instances)
- [Búsqueda](#searching)
  - [Cláusulas Where](#where-clauses)
  - [Paginación](#pagination)
  - [Borrado suave](#soft-deleting)
  - [Personalización de motores de búsqueda](#customizing-engine-searches)
- [Motores personalizados](#custom-engines)
- [Macros del constructor](#builder-macros)

[]()

## Introducción

[Laravel Scout](https://github.com/laravel/scout) proporciona una solución sencilla basada en controladores para añadir búsquedas de texto completo a tus [modelos E](/docs/%7B%7Bversion%7D%7D/eloquent)loquent. Usando observadores de modelos, Scout mantendrá automáticamente tus índices de búsqueda sincronizados con tus registros de Eloquent.

Actualmente, Scout se suministra con controladores [Algolia](https://www.algolia.com/), [MeiliSearch](https://www.meilisearch.com) y MySQL / PostgreSQL`(base de datos`). Además, Scout incluye un controlador de "colección" que está diseñado para el uso de desarrollo local y no requiere dependencias externas o servicios de terceros. Además, escribir controladores personalizados es sencillo y eres libre de extender Scout con tus propias implementaciones de búsqueda.

[]()

## Instalación

En primer lugar, instale Scout a través del gestor de paquetes Composer:

```shell
composer require laravel/scout
```

Después de instalar Scout, debes publicar el archivo de configuración de Scout utilizando el comando `vendor:publish` Artisan. Este comando publicará el archivo de configuración `scout.` php en el directorio `config` de tu aplicación:

```shell
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

Por último, añada el rasgo `Laravel\Scout\Searchable` al modelo que desea hacer buscable. Este rasgo registrará un observador de modelo que mantendrá automáticamente el modelo sincronizado con tu controlador de búsqueda:

    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Laravel\Scout\Searchable;

    class Post extends Model
    {
        use Searchable;
    }

[]()

### Requisitos previos del controlador

[]()

#### Algolia

Cuando utilices el controlador Algolia, debes configurar tus credenciales Algolia `id` y `secret` en tu archivo de configuración `config/scout.` php. Una vez configuradas tus credenciales, también deberás instalar el SDK PHP de Algolia a través del gestor de paquetes Composer:

```shell
composer require algolia/algoliasearch-client-php
```

[]()

#### MeiliSearch

[MeiliSearch](https://www.meilisearch.com) es un motor de búsqueda muy rápido y de código abierto. Si no estás seguro de cómo instalar MeiliSearch en tu máquina local, puedes utilizar [Laravel Sail](/docs/%7B%7Bversion%7D%7D/sail#meilisearch), el entorno de desarrollo Docker soportado oficialmente por Laravel.

Si utiliza el controlador MeiliSearch, deberá instalar el SDK PHP de MeiliSearch a través del gestor de paquetes Composer:

```shell
composer require meilisearch/meilisearch-php http-interop/http-factory-guzzle
```

A continuación, configure la variable de entorno `SCOUT_DRIVER`, así como su `host` MeiliSearch y las credenciales `clave` dentro del archivo `.env` de su aplicación:

```ini
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterKey
```

Para más información sobre MeiliSearch, consulte la [documentación de MeiliSearch](https://docs.meilisearch.com/learn/getting_started/quick_start.html).

Además, debe asegurarse de instalar una versión de `meilisearch/meilisearch-php` que sea compatible con su versión binaria de MeiliSearch revisando [la documentación de MeiliSearch relativa a la compatibilidad binaria](https://github.com/meilisearch/meilisearch-php#-compatibility-with-meilisearch).

> **Advertencia**  
> Al actualizar Scout en una aplicación que utiliza MeiliSearch, debería [revisar](https://github.com/meilisearch/MeiliSearch/releases) siempre [cualquier cambio adicional de ruptura](https://github.com/meilisearch/MeiliSearch/releases) en el propio servicio MeiliSearch.

[]()

### Puesta en cola

Aunque no es estrictamente necesario para utilizar Scout, deberías considerar seriamente configurar un controlador [de](/docs/%7B%7Bversion%7D%7D/queues) cola antes de utilizar la biblioteca. Ejecutar un trabajador de cola permitirá a Scout poner en cola todas las operaciones que sincronizan la información de tu modelo con tus índices de búsqueda, proporcionando tiempos de respuesta mucho mejores para la interfaz web de tu aplicación.

Una vez que haya configurado un controlador de cola, establezca el valor de la opción de `cola` en su archivo de configuración `config/scout.` php en `true`:

    'queue' => true,

Incluso cuando la opción de `cola` está establecida en `false`, es importante recordar que algunos controladores Scout como Algolia y Meilisearch siempre indexan los registros de forma asíncrona. Es decir, aunque la operación de indexación se haya completado dentro de tu aplicación Laravel, es posible que el propio motor de búsqueda no refleje los registros nuevos y actualizados inmediatamente.

Para especificar la conexión y la cola que utilizan tus trabajos Scout, puedes definir la opción de configuración de `la` cola como un array:

    'queue' => [
        'connection' => 'redis',
        'queue' => 'scout'
    ],

[]()

## Configuración

[]()

### Configuración de índices de modelos

Cada modelo de Eloquent se sincroniza con un "índice" de búsqueda determinado, que contiene todos los registros de búsqueda de ese modelo. En otras palabras, puedes pensar en cada índice como en una tabla MySQL. Por defecto, cada modelo se persistirá en un índice que coincida con el nombre típico de la "tabla" del modelo. Típicamente, esta es la forma plural del nombre del modelo; sin embargo, eres libre de personalizar el índice del modelo anulando el método `searchableAs` en el modelo:

    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Laravel\Scout\Searchable;

    class Post extends Model
    {
        use Searchable;

        /**
         * Get the name of the index associated with the model.
         *
         * @return string
         */
        public function searchableAs()
        {
            return 'posts_index';
        }
    }

[]()

### Configuración de datos de búsqueda

Por defecto, todo el formulario `toArray` de un modelo dado se persistirá en su índice de búsqueda. Si desea personalizar los datos que se sincronizan con el índice de búsqueda, puede anular el método `toSearchableArray` en el modelo:

    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Laravel\Scout\Searchable;

    class Post extends Model
    {
        use Searchable;

        /**
         * Get the indexable data array for the model.
         *
         * @return array
         */
        public function toSearchableArray()
        {
            $array = $this->toArray();

            // Customize the data array...

            return $array;
        }
    }

Algunos motores de búsqueda como MeiliSearch sólo realizarán operaciones de filtrado`(>`, `<`, etc.) en datos del tipo correcto. Por lo tanto, al utilizar estos motores de búsqueda y personalizar los datos que se pueden buscar, debe asegurarse de que los valores numéricos se convierten al tipo correcto:

    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
        ];
    }

[]()

#### Configuración de datos filtrables y ajustes de índice (MeiliSearch)

A diferencia de otros controladores de Scout, MeiliSearch requiere que definas previamente la configuración de búsqueda del índice, como los atributos filtrables, los atributos ordenables y [otros campos de configuración compatibles](https://docs.meilisearch.com/reference/api/settings.html).

Los atributos filtrables son los atributos sobre los que planeas filtrar al invocar el método `where` de Scout, mientras que los atributos ordenables son los atributos por los que planeas ordenar al invocar el método `orderBy` de Scout. Para definir la configuración del índice, ajuste la parte `index-settings` de la entrada de configuración `meilisearch` en el archivo de configuración `scout` de su aplicación:

```php
use App\Models\User;
use App\Models\Flight;

'meilisearch' => [
    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'key' => env('MEILISEARCH_KEY', null),
    'index-settings' => [
        User::class => [
            'filterableAttributes'=> ['id', 'name', 'email'],
            'sortableAttributes' => ['created_at'],
            // Other settings fields...
        ],
        Flight::class => [
            'filterableAttributes'=> ['id', 'destination'],
            'sortableAttributes' => ['updated_at'],
        ],
    ],
],
```

Si el modelo subyacente a un índice dado es de borrado suave y está incluido en la array `index-settings`, Scout incluirá automáticamente soporte para filtrar en modelos de borrado suave en ese índice. Si no tienes otros atributos filtrables u ordenables que definir para un índice de modelo de borrado suave, puedes simplemente añadir una entrada vacía a la array `index-settings` para ese modelo:

```php
'index-settings' => [
    Flight::class => []
],
```

Después de configurar los ajustes de índice de tu aplicación, debes invocar el comando `scout:sync-index-settings` Artisan. Este comando informará a MeiliSearch de la configuración actual de los índices. Para mayor comodidad, puede que desee hacer que este comando forme parte de su proceso de despliegue:

```shell
php artisan scout:sync-index-settings
```

[]()

### Configuración del ID del modelo

Por defecto, Scout utilizará la clave primaria del modelo como ID / clave única del modelo que se almacena en el índice de búsqueda. Si necesita personalizar este comportamiento, puede anular los métodos `getScoutKey` y `getScoutKeyName` en el modelo:

    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Laravel\Scout\Searchable;

    class User extends Model
    {
        use Searchable;

        /**
         * Get the value used to index the model.
         *
         * @return mixed
         */
        public function getScoutKey()
        {
            return $this->email;
        }

        /**
         * Get the key name used to index the model.
         *
         * @return mixed
         */
        public function getScoutKeyName()
        {
            return 'email';
        }
    }

[]()

### Configuración de motores de búsqueda por modelo

Al buscar, Scout utilizará normalmente el motor de búsqueda predeterminado especificado en el archivo de configuración de `scout` de su aplicación. Sin embargo, el motor de búsqueda para un modelo en particular se puede cambiar anulando el método `searchableUsing` en el modelo:

    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Laravel\Scout\EngineManager;
    use Laravel\Scout\Searchable;

    class User extends Model
    {
        use Searchable;

        /**
         * Get the engine used to index the model.
         *
         * @return \Laravel\Scout\Engines\Engine
         */
        public function searchableUsing()
        {
            return app(EngineManager::class)->engine('meilisearch');
        }
    }

[]()

### Identificación de usuarios

Scout también permite autoidentificar a los usuarios cuando se utiliza [Algolia](https://algolia.com). Asociar el usuario autenticado con las operaciones de búsqueda puede ser útil al ver sus análisis de búsqueda dentro del tablero de Algolia. Puedes habilitar la identificación de usuarios definiendo una variable de entorno `SCOUT_IDENTIFY` como `true` en el archivo `.env` de tu aplicación:

```ini
SCOUT_IDENTIFY=true
```

Al habilitar esta función, también se pasarán a Algolia la dirección IP de la solicitud y el identificador principal del usuario autenticado, de modo que estos datos se asocien a cualquier solicitud de búsqueda que realice el usuario.

[]()

## Base de datos / Motores de colecciones

[]()

### Motor de base de datos

> **Advertencia**  
> El motor de base de datos soporta actualmente MySQL y PostgreSQL.

Si su aplicación interactúa con bases de datos pequeñas o medianas o tiene una carga de trabajo ligera, puede que le resulte más conveniente empezar con el motor de "base de datos" de Scout. El motor de base de datos utilizará cláusulas "where like" e índices de texto completo al filtrar los resultados de tu base de datos existente para determinar los resultados de búsqueda aplicables a tu consulta.

Para utilizar el motor de base de datos, puede simplemente establecer el valor de la variable de entorno `SCOUT_DRIVER` en `base de datos`, o especificar el controlador de `base de datos` directamente en el archivo de configuración de `scout` de su aplicación:

```ini
SCOUT_DRIVER=database
```

Una vez que haya especificado el motor de base de datos como su controlador preferido, deberá [configurar sus datos de búsqueda](#configuring-searchable-data). A continuación, puede empezar a [ejecutar consultas de búsqueda](#searching) en sus modelos. La indexación del motor de búsqueda, como la necesaria para sembrar los índices Algolia o MeiliSearch, no es necesaria cuando se utiliza el motor de base de datos.

#### Personalización de las estrategias de búsqueda en bases de datos

Por defecto, el motor de base de datos ejecutará una consulta "where like" contra cada atributo del modelo que hayas [configurado como buscable](#configuring-searchable-data). Sin embargo, en algunas situaciones, esto puede dar lugar a un rendimiento deficiente. Por lo tanto, la estrategia de búsqueda del motor de base de datos puede configurarse para que algunas columnas especificadas utilicen consultas de búsqueda de texto completo o sólo utilicen restricciones "where like" para buscar en los prefijos de las cadenas`(ejemplo%`) en lugar de buscar en toda la cadena`(%ejemplo%`).

Para definir este comportamiento, puede asignar atributos PHP al método `toSearchableArray` de su modelo. Cualquier columna a la que no se le asigne un comportamiento de estrategia de búsqueda adicional continuará utilizando la estrategia por defecto "where like":

```php
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;

/**
 * Get the indexable data array for the model.
 *
 * @return array
 */
#[SearchUsingPrefix(['id', 'email'])]
#[SearchUsingFullText(['bio'])]
public function toSearchableArray()
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'bio' => $this->bio,
    ];
}
```

> **Advertencia**  
> Antes de especificar que una columna debe utilizar restricciones de consulta de texto completo, asegúrese de que a la columna se le ha asignado un [índice de texto completo](/docs/%7B%7Bversion%7D%7D/migrations#available-index-types).

[]()

### Motor de recogida

Aunque es libre de utilizar los motores de búsqueda Algolia o MeiliSearch durante el desarrollo local, puede que le resulte más cómodo empezar con el motor "collection". El motor de recopilación utilizará cláusulas "where" y filtrado de recopilación en los resultados de su base de datos existente para determinar los resultados de búsqueda aplicables a su consulta. Al utilizar este motor, no es necesario "indexar" sus modelos de búsqueda, ya que simplemente se recuperarán de su base de datos local.

Para utilizar el motor de recopilación, sólo tiene que establecer el valor de la variable de entorno `SCOUT_DRIVER` en `recopilación` o especificar el controlador de `recopilación` directamente en el archivo de configuración de `explorador` de su aplicación:

```ini
SCOUT_DRIVER=collection
```

Una vez que haya especificado el motor de recogida como su motor preferido, puede empezar a [ejecutar consultas de](#searching) búsqueda contra sus modelos. La indexación del motor de búsqueda, como la necesaria para sembrar los índices de Algolia o MeiliSearch, no es necesaria cuando se utiliza el motor de recopilación.

#### Diferencias con el motor de base de datos

A primera vista, los motores "base de datos" y "colecciones" son bastante similares. Ambos interactúan directamente con su base de datos para recuperar resultados de búsqueda. Sin embargo, el motor de colecciones no utiliza índices de texto completo ni cláusulas `LIKE` para encontrar registros coincidentes. En su lugar, extrae todos los registros posibles y utiliza el ayudante `Str::is` de Laravel para determinar si la cadena de búsqueda existe dentro de los valores de los atributos del modelo.

El motor de colección es el motor de búsqueda más portable ya que funciona en todas las bases de datos relacionales soportadas por Laravel (incluyendo SQLite y SQL Server); sin embargo, es menos eficiente que el motor de base de datos de Scout.

[]()

## Indexación

[]()

### Importación por lotes

Si estás instalando Scout en un proyecto existente, puede que ya tengas registros de bases de datos que necesites importar a tus índices. Scout proporciona un comando `scout:import` Artisan que puede utilizar para importar todos sus registros existentes en sus índices de búsqueda:

```shell
php artisan scout:import "App\Models\Post"
```

El comando `flush` puede ser usado para remover todos los registros de un modelo de sus índices de búsqueda:

```shell
php artisan scout:flush "App\Models\Post"
```

[]()

#### Modificación de la consulta de importación

Si desea modificar la consulta que se utiliza para recuperar todos sus modelos para la importación por lotes, puede definir un método `makeAllSearchableUsing` en su modelo. Este es un buen lugar para añadir cualquier carga de relación ansiosa que pueda ser necesaria antes de importar sus modelos:

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with('author');
    }

[]()

### Añadir registros

Una vez que hayas añadido el rasgo `Laravel\Scout\Searchable` a un modelo, todo lo que necesitas hacer es `guardar` o `crear` una instancia del modelo y se añadirá automáticamente a tu índice de búsqueda. Si ha configurado Scout para [utilizar colas](#queueing) esta operación se realizará en segundo plano por su trabajador de cola:

    use App\Models\Order;

    $order = new Order;

    // ...

    $order->save();

[]()

#### Añadir registros mediante consulta

Si quieres añadir una colección de modelos a tu índice de búsqueda a través de una consulta Eloquent, puedes encadenar el método `searchable` en la consulta Eloquent. El método `searchable` [troceará los resultados](/docs/%7B%7Bversion%7D%7D/eloquent#chunking-results) de la consulta y añadirá los registros a tu índice de búsqueda. De nuevo, si has configurado Scout para usar colas, todos los trozos serán importados en segundo plano por tus trabajadores de cola:

    use App\Models\Order;

    Order::where('price', '>', 100)->searchable();

También puede llamar al método `searchable` en una instancia de relación Eloquent:

    $user->orders()->searchable();

O, si ya tienes una colección de modelos Eloquent en memoria, puedes llamar al método `searchable` en la instancia de la colección para añadir las instancias del modelo a su índice correspondiente:

    $orders->searchable();

> **Nota**  
> El método de `búsqueda` puede considerarse una operación de "upsert". En otras palabras, si el registro del modelo ya está en tu índice, se actualizará. Si no existe en el índice de búsqueda, se añadirá al índice.

[]()

### Actualización de registros

Para actualizar un modelo buscable, sólo tienes que actualizar las propiedades de la instancia del modelo y `guardar` el modelo en tu base de datos. Scout persistirá automáticamente los cambios en su índice de búsqueda:

    use App\Models\Order;

    $order = Order::find(1);

    // Update the order...

    $order->save();

También puedes invocar el método `searchable` en una instancia de consulta Eloquent para actualizar una colección de modelos. Si los modelos no existen en tu índice de búsqueda, se crearán:

    Order::where('price', '>', 100)->searchable();

Si desea actualizar los registros del índice de búsqueda de todos los modelos de una relación, puede invocar el método `searchable` en la instancia de la relación:

    $user->orders()->searchable();

O, si ya tiene una colección de modelos Eloquent en memoria, puede invocar el método `searchable` sobre la instancia de la colección para actualizar las instancias de los modelos en su índice correspondiente:

    $orders->searchable();

[]()

### Eliminación de registros

Para eliminar un registro del índice, basta con `borrar` el modelo de la base de datos. Esto puede hacerse incluso si está utilizando modelos de [borrado suave](/docs/%7B%7Bversion%7D%7D/eloquent#soft-deleting):

    use App\Models\Order;

    $order = Order::find(1);

    $order->delete();

Si no desea recuperar el modelo antes de eliminar el registro, puede utilizar el método `unsearchable` en una instancia de consulta de Eloquent:

    Order::where('price', '>', 100)->unsearchable();

Si desea eliminar los registros del índice de búsqueda de todos los modelos de una relación, puede invocar `unsearchable` en la instancia de relación:

    $user->orders()->unsearchable();

O, si ya tienes una colección de modelos Eloquent en memoria, puedes invocar el método `unsearchable` sobre la instancia de colección para eliminar las instancias de modelo de su índice correspondiente:

    $orders->unsearchable();

[]()

### Pausa de indexación

A veces puede que necesites realizar un lote de operaciones Eloquent en un modelo sin sincronizar los datos del modelo con tu índice de búsqueda. Puedes hacerlo utilizando el método `withoutSyncingToSearch`. Este método acepta un único closure que se ejecutará inmediatamente. Cualquier operación del modelo que ocurra dentro del closure no se sincronizará con el índice del modelo:

    use App\Models\Order;

    Order::withoutSyncingToSearch(function () {
        // Perform model actions...
    });

[]()

### Búsqueda condicional de instancias del modelo

A veces es necesario que un modelo sólo sea consultable en determinadas condiciones. Por ejemplo, imagine que tiene un modelo `App\Models\Post` que puede estar en uno de dos estados: "borrador" y "publicado". Es posible que sólo desee permitir que las entradas "publicadas" se puedan buscar. Para ello, puede definir un método `shouldBeSearchable` en su modelo:

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->isPublished();
    }

El método `shouldBeSearchable` sólo se aplica cuando se manipulan modelos a través de los métodos `guardar` y `crear`, consultas o relaciones. La búsqueda directa de modelos o colecciones mediante el método `searchable` anulará el resultado del método `shouldBeSearchable`.

> **Advertencia**  
> El método `shouldBeSearchable` no es aplicable cuando se utiliza el motor de "base de datos" de Scout, ya que todos los datos buscables se almacenan siempre en la base de datos. Para lograr un comportamiento similar cuando se utiliza el motor de base de datos, debe utilizar [cláusulas where](#where-clauses) en su lugar.

[]()

## Búsqueda en

Puede empezar a buscar en un modelo utilizando el método `search`. El método search acepta una única cadena que se utilizará para buscar en los modelos. A continuación, debe encadenar el método `get` en la consulta de búsqueda para recuperar los modelos Eloquent que coincidan con la consulta de búsqueda dada:

    use App\Models\Order;

    $orders = Order::search('Star Trek')->get();

Dado que las búsquedas Scout devuelven una colección de modelos Eloquent, puedes incluso devolver los resultados directamente desde una ruta o controlador y se convertirán automáticamente a JSON:

    use App\Models\Order;
    use Illuminate\Http\Request;

    Route::get('/search', function (Request $request) {
        return Order::search($request->search)->get();
    });

Si desea obtener los resultados de la búsqueda sin procesar antes de que se conviertan en modelos Eloquent, puede utilizar el método `sin procesar`:

    $orders = Order::search('Star Trek')->raw();

[]()

#### Índices personalizados

Las consultas de búsqueda se realizarán normalmente en el índice especificado por el método [`searchableAs`](#configuring-model-indexes) del modelo. Sin embargo, puede utilizar el método `within` para especificar un índice personalizado en el que buscar:

    $orders = Order::search('Star Trek')
        ->within('tv_shows_popularity_desc')
        ->get();

[]()

### Cláusulas Where

Scout permite añadir cláusulas "where" simples a las consultas de búsqueda. Actualmente, estas cláusulas sólo admiten comprobaciones básicas de igualdad numérica y son útiles principalmente para consultas de búsqueda de ámbito por un ID de propietario:

    use App\Models\Order;

    $orders = Order::search('Star Trek')->where('user_id', 1)->get();

Puede utilizar el método `whereIn` para limitar los resultados a un conjunto determinado de valores:

    $orders = Order::search('Star Trek')->whereIn(
        'status', ['paid', 'open']
    )->get();

Dado que un índice de búsqueda no es una base de datos relacional, actualmente no se admiten cláusulas "where" más avanzadas.

> **AdvertenciaSi**su aplicación utiliza MeiliSearch, debe configurar los [atributos filtrables](#configuring-filterable-data-for-meilisearch) de su aplicación antes de utilizar las cláusulas "where" de Scout.

[]()

### Paginación

Además de recuperar una colección de modelos, puedes paginar los resultados de la búsqueda utilizando el método `paginate`. Este método devolverá una instancia `Illuminate\Pagination\LengthAwarePaginator` como si hubiera [paginado una consulta Eloquent tradicional](/docs/%7B%7Bversion%7D%7D/pagination):

    use App\Models\Order;

    $orders = Order::search('Star Trek')->paginate();

Puede especificar cuántos modelos recuperar por página pasando la cantidad como primer argumento al método `paginate`:

    $orders = Order::search('Star Trek')->paginate(15);

Una vez que haya recuperado los resultados, puede mostrarlos y mostrar los enlaces de página utilizando [Blade](/docs/%7B%7Bversion%7D%7D/blade) como si hubiera paginado una consulta Eloquent tradicional:

```html
<div class="container">
    @foreach ($orders as $order)
        {{ $order->price }}
    @endforeach
</div>

{{ $orders->links() }}
```

Por supuesto, si desea recuperar los resultados de la paginación como JSON, puede devolver la instancia del paginador directamente desde una ruta o controlador:

    use App\Models\Order;
    use Illuminate\Http\Request;

    Route::get('/orders', function (Request $request) {
        return Order::search($request->input('query'))->paginate(15);
    });

> **Advertencia**  
> Dado que los motores de búsqueda no conocen las definiciones de ámbito global de su modelo Eloquent, no debe utilizar ámbitos globales en aplicaciones que utilicen la paginación Scout. O bien, debe volver a crear las restricciones del ámbito global al buscar a través de Scout.

[]()

### Borrado suave

Si sus modelos indexados [se bor](/docs/%7B%7Bversion%7D%7D/eloquent#soft-deleting) ran suavemente y necesita buscar sus modelos borrados suavemente, establezca la opción `soft_delete` del archivo de configuración `config/scout.` php en `true`:

    'soft_delete' => true,

Cuando esta opción de configuración es `verdadera`, Scout no eliminará los modelos borrados del índice de búsqueda. En su lugar, establecerá un atributo oculto `__soft_deleted` en el registro indexado. A continuación, puede utilizar los métodos `withTrashed` o `onlyTrashed` para recuperar los registros borrados en caliente durante la búsqueda:

    use App\Models\Order;

    // Include trashed records when retrieving results...
    $orders = Order::search('Star Trek')->withTrashed()->get();

    // Only include trashed records when retrieving results...
    $orders = Order::search('Star Trek')->onlyTrashed()->get();

> **Nota**  
> Cuando un modelo borrado por software se borra permanentemente usando `forceDelete`, Scout lo eliminará del índice de búsqueda automáticamente.

[]()

### Personalización de motores de búsqueda

Si necesita realizar una personalización avanzada del comportamiento de búsqueda de un motor, puede pasar un closure como segundo argumento al método de `búsqueda`. Por ejemplo, podrías utilizar esta llamada de retorno para añadir datos de geolocalización a tus opciones de búsqueda antes de que la consulta de búsqueda se pase a Algolia:

    use Algolia\AlgoliaSearch\SearchIndex;
    use App\Models\Order;

    Order::search(
        'Star Trek',
        function (SearchIndex $algolia, string $query, array $options) {
            $options['body']['query']['bool']['filter']['geo_distance'] = [
                'distance' => '1000km',
                'location' => ['lat' => 36, 'lon' => 111],
            ];

            return $algolia->search($query, $options);
        }
    )->get();

[]()

#### Personalización de la consulta de resultados de Eloquent

Después de que Scout recupere una lista de modelos Eloquent coincidentes desde el motor de búsqueda de su aplicación, se utiliza Eloquent para recuperar todos los modelos coincidentes por sus claves primarias. Puede personalizar esta consulta invocando el método `query`. El método `query` acepta un closure que recibirá la instancia del constructor de consultas de Eloquent como argumento:

```php
use App\Models\Order;

$orders = Order::search('Star Trek')
    ->query(fn ($query) => $query->with('invoices'))
    ->get();
```

Dado que esta llamada de retorno se invoca después de que los modelos relevantes ya han sido recuperados del motor de búsqueda de tu aplicación, el método de `consulta` no debería utilizarse para "filtrar" resultados. En su lugar, debes utilizar las [cláusulas where](#where-clauses) de Scout.

[]()

## Motores personalizados

[]()

#### Escribiendo el motor

Si uno de los motores de búsqueda Scout incorporados no se ajusta a tus necesidades, puedes escribir tu propio motor personalizado y registrarlo con Scout. Su motor debe extender la clase abstracta `Laravel\Scout\Engines\Engine`. Esta clase abstracta contiene ocho métodos que tu motor personalizado debe implementar:

    use Laravel\Scout\Builder;

    abstract public function update($models);
    abstract public function delete($models);
    abstract public function search(Builder $builder);
    abstract public function paginate(Builder $builder, $perPage, $page);
    abstract public function mapIds($results);
    abstract public function map(Builder $builder, $results, $model);
    abstract public function getTotalCount($results);
    abstract public function flush($model);

Puede resultarte útil revisar las implementaciones de estos métodos en la clase `Laravel\Scout\Engines\AlgoliaEngine`. Esta clase le proporcionará un buen punto de partida para aprender a implementar cada uno de estos métodos en su propio motor.

[]()

#### Registro del motor

Una vez que hayas escrito tu motor personalizado, puedes registrarlo con Scout usando el método `extend` del gestor de motores de Scout. El gestor de motores de Scout puede resolverse desde el contenedor de servicios de Laravel. Debes llamar al método `extend` desde el método `boot` de tu clase `App\Providers\AppServiceProvider` o cualquier otro proveedor de servicios utilizado por tu aplicación:

    use App\ScoutExtensions\MySqlSearchEngine
    use Laravel\Scout\EngineManager;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        resolve(EngineManager::class)->extend('mysql', function () {
            return new MySqlSearchEngine;
        });
    }

Una vez que su motor ha sido registrado, puede especificarlo como su `controlador` Scout por defecto en el archivo de configuración `config/scout.php` de su aplicación:

    'driver' => 'mysql',

[]()

## Macros del constructor

Si desea definir un método constructor de búsqueda Scout personalizado, puede utilizar el método `macro` en la clase `Laravel\Scout\Builder`. Normalmente, las "macros" deben definirse dentro del método de `arranque` [de](/docs/%7B%7Bversion%7D%7D/providers) un proveedor de servicios:

    use Illuminate\Support\Facades\Response;
    use Illuminate\Support\ServiceProvider;
    use Laravel\Scout\Builder;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('count', function () {
            return $this->engine()->getTotalCount(
                $this->engine()->search($this)
            );
        });
    }

La función `macro` acepta un nombre de macro como primer argumento y un closure como segundo argumento. El closure de la macro se ejecutará cuando se llame al nombre de la macro desde una implementación `Laravel\Scout\Builder`:

    use App\Models\Order;

    Order::search('Star Trek')->count();
