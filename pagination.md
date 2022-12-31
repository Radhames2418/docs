# Base de datos: Paginación

- [Introducción](#introduction)
- [Uso básico](#basic-usage)
  - [Paginación de resultados del Generador de consultas](#paginating-query-builder-results)
  - [Paginación de resultados de Eloquent](#paginating-eloquent-results)
  - [Paginación por Cursor](#cursor-pagination)
  - [Creación manual de un paginador](#manually-creating-a-paginator)
  - [Personalización de las URL de paginación](#customizing-pagination-urls)
- [Visualización de resultados de paginación](#displaying-pagination-results)
  - [Ajuste de la ventana de enlace de paginación](#adjusting-the-pagination-link-window)
  - [Conversión de resultados a JSON](#converting-results-to-json)
- [Personalización de la vista de paginación](#customizing-the-pagination-view)
  - [Uso de Bootstrap](#using-bootstrap)
- [Métodos de Instancia Paginator y LengthAwarePaginator](#paginator-instance-methods)
- [Métodos de instancia del paginador de cursor](#cursor-paginator-instance-methods)

[]()

## Introducción

En otros frameworks, la paginación puede ser muy dolorosa. Esperamos que el enfoque de Laravel a la paginación sea un soplo de aire fresco. El paginador de Laravel está integrado con el [constructor de consultas](/docs/%7B%7Bversion%7D%7D/queries) y el [ORM Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent) y proporciona una paginación cómoda y fácil de usar de los registros de la base de datos con cero configuración.

Por defecto, el HTML generado por el paginador es compatible con el [framework CSS Tailwind](https://tailwindcss.com/); sin embargo, también está disponible el soporte de paginación Bootstrap.

[]()

#### Tailwind JIT

Si estás utilizando las vistas de paginación Tailwind por defecto de Laravel y el motor Tailwind JIT, debes asegurarte de que la clave de `contenido` del fichero `tailwind.config.js` de tu aplicación hace referencia a las vistas de paginación de Laravel para que sus clases Tailwind no sean purgadas:

```js
content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
],
```

[]()

## Uso Básico

[]()

### Paginación de Resultados de Query Builder

Hay varias formas de paginar elementos. La más sencilla es utilizar el método `paginar` en el [constructor de consultas](/docs/%7B%7Bversion%7D%7D/queries) o en una [consulta Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent). El método `paginar` se encarga automáticamente de establecer el "límite" y el "desplazamiento" de la consulta basándose en la página actual que está viendo el usuario. Por defecto, la página actual es detectada por el valor del argumento `page` query string en la petición HTTP. Este valor es detectado automáticamente por Laravel, y también se inserta automáticamente en los enlaces generados por el paginador.

En este ejemplo, el único argumento que se pasa al método `paginate` es el número de elementos que queremos que se muestren "por página". En este caso, vamos a especificar que nos gustaría mostrar `15` elementos por página:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\DB;

    class UserController extends Controller
    {
        /**
         * Show all application users.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            return view('user.index', [
                'users' => DB::table('users')->paginate(15)
            ]);
        }
    }

[]()

#### Paginación simple

El método `paginate` cuenta el número total de registros coincidentes con la consulta antes de recuperar los registros de la base de datos. Esto se hace para que el paginador sepa cuántas páginas de registros hay en total. Sin embargo, si no tiene previsto mostrar el número total de páginas en la interfaz de usuario de su aplicación, la consulta de recuento de registros es innecesaria.

Por lo tanto, si sólo necesita mostrar enlaces simples "Siguiente" y "Anterior" en la interfaz de usuario de su aplicación, puede utilizar el método `simplePaginate` para realizar una consulta única y eficiente:

    $users = DB::table('users')->simplePaginate(15);

[]()

### Paginación de Resultados de Eloquent

También puede paginar consultas [Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent). En este ejemplo, paginaremos el modelo `App\Models\User` e indicaremos que queremos mostrar 15 registros por página. Como puede ver, la sintaxis es casi idéntica a la paginación de los resultados del constructor de consultas:

    use App\Models\User;

    $users = User::paginate(15);

Por supuesto, puede llamar al método `paginate` después de establecer otras restricciones en la consulta, como las cláusulas `where`:

    $users = User::where('votes', '>', 100)->paginate(15);

También puede utilizar el método `simplePaginate` cuando pagine modelos Eloquent:

    $users = User::where('votes', '>', 100)->simplePaginate(15);

Del mismo modo, puede utilizar el método `cursorPaginate` para paginar por cursor modelos Eloquent:

    $users = User::where('votes', '>', 100)->cursorPaginate(15);

[]()

#### Múltiples Paginadores por Página

A veces puede que necesites renderizar dos paginadores separados en una única pantalla que es renderizada por tu aplicación. Sin embargo, si ambas instancias del paginador utilizan el parámetro `page` query string para almacenar la página actual, los dos paginadores entrarán en conflicto. Para resolver este conflicto, puede pasar el nombre del parámetro de cadena de consulta que desea utilizar para almacenar la página actual del paginador a través del tercer argumento proporcionado a los métodos `paginate`, `simplePaginate` y `cursorPaginate`:

    use App\Models\User;

    $users = User::where('votes', '>', 100)->paginate(
        $perPage = 15, $columns = ['*'], $pageName = 'users'
    );

[]()

### Paginación por Cursor

Mientras que `paginate` y `simplePaginate` crean consultas utilizando la cláusula "offset" de SQL, la paginación por cursor funciona construyendo cláusulas "where" que comparan los valores de las columnas ordenadas contenidas en la consulta, proporcionando el rendimiento de base de datos más eficiente disponible entre todos los métodos de paginación de Laravel. Este método de paginación es particularmente adecuado para grandes conjuntos de datos e interfaces de usuario de desplazamiento "infinito".

A diferencia de la paginación basada en offset, que incluye un número de página en la cadena de consulta de las URL generadas por el paginador, la paginación basada en cursor coloca una cadena de "cursor" en la cadena de consulta. El cursor es una cadena codificada que contiene la ubicación en la que la siguiente consulta paginada debe empezar a paginar y la dirección en la que debe paginar:

```nothing
http://localhost/users?cursor=eyJpZCI6MTUsIl9wb2ludHNUb05leHRJdGVtcyI6dHJ1ZX0
```

Puede crear una instancia de paginador basada en el cursor mediante el método `cursorPaginate` que ofrece el constructor de consultas. Este método devuelve una instancia de `Illuminate\Pagination\CursorPaginator`:

    $users = DB::table('users')->orderBy('id')->cursorPaginate(15);

Una vez que haya recuperado una instancia de paginador de cursor, puede [mostrar los resultados de la paginación](#displaying-pagination-results) como lo haría normalmente al utilizar los métodos `paginate` y `simplePaginate`. Para más información sobre los métodos de instancia ofrecidos por el paginador de cursor, consulte [la documentación de métodos de instancia del paginador](#cursor-paginator-instance-methods) de cursor.

> **Advertencia**  
> Su consulta debe contener una cláusula "order by" para poder aprovechar la paginación por cursor.

[]()

#### Paginación por cursor vs. Paginación por desplazamiento

Para ilustrar las diferencias entre la paginación por desplazamiento y la paginación por cursor, examinemos algunas consultas SQL de ejemplo. Las dos consultas siguientes mostrarán la "segunda página" de resultados de una tabla de `usuarios` ordenados por `id:`

```sql
# Offset Pagination...
select * from users order by id asc limit 15 offset 15;

# Cursor Pagination...
select * from users where id > 15 order by id asc limit 15;
```

La consulta de paginación por cursor ofrece las siguientes ventajas sobre la paginación por desplazamiento:

- Para conjuntos de datos grandes, la paginación por cursor ofrecerá un mejor rendimiento si las columnas "order by" están indexadas. Esto se debe a que la cláusula "offset" explora todos los datos previamente encontrados.
- Para conjuntos de datos con escrituras frecuentes, la paginación desplazada puede omitir registros o mostrar duplicados si se han añadido o eliminado resultados recientemente de la página que el usuario está viendo en ese momento.

Sin embargo, la paginación por cursor tiene las siguientes limitaciones:

- Al igual que `simplePaginate`, la paginación por cursor sólo puede utilizarse para mostrar enlaces "Siguiente" y "Anterior" y no permite generar enlaces con números de página.
- Requiere que la ordenación se base en al menos una columna única o una combinación de columnas que sean únicas. No se admiten columnas con valores `nulos`.
- Las expresiones de consulta en las cláusulas "order by" sólo se admiten si se les asigna un alias y se añaden también a la cláusula "select".
- No se admiten expresiones de consulta con parámetros.

[]()

### Creación manual de un paginador

A veces puede que desee crear una instancia de paginación manualmente, pasándole una array de elementos que ya tenga en memoria. Puede hacerlo creando una instancia `Illuminate\Pagination\Paginator`, `Illuminate\Pagination\LengthAwarePaginator` o `Illuminate\Pagination\CursorPaginator`, dependiendo de sus necesidades.

Las clases `Paginator` y `CursorPaginator` no necesitan conocer el número total de elementos en el conjunto de resultados; sin embargo, debido a esto, estas clases no tienen métodos para recuperar el índice de la última página. El `LengthAwarePaginator` acepta casi los mismos argumentos que el `Paginator`; sin embargo, requiere un recuento del número total de elementos del conjunto de resultados.

En otras palabras, el `Paginator` corresponde al método `simplePaginate` del constructor de consultas, el `CursorPaginator` corresponde al método `cursorPaginate`, y el `LengthAwarePaginator` corresponde al método `paginate`.

> **Advertencia**  
> Al crear manualmente una instancia de paginador, debe "cortar" manualmente la array de resultados que pasa al paginador. Si no está seguro de cómo hacerlo, consulte la función [array-slice.php">array_slice](<https://secure.php.net/manual/en/function.\<glossary variable=>) de PHP.

[]()

### Personalización de las URL de paginación

Por defecto, los enlaces generados por el paginador coincidirán con el URI de la petición actual. Sin embargo, el método `withPath` del paginador permite personalizar el URI utilizado por el paginador al generar los enlaces. Por ejemplo, si desea que el paginador genere enlaces como `http://example.com/admin/users?page=N,` debe pasar `/admin/users` al método `withPath`:

    use App\Models\User;

    Route::get('/users', function () {
        $users = User::paginate(15);

        $users->withPath('/admin/users');

        //
    });

[]()

#### Añadir valores a la cadena de consulta

Puede añadir valores a la cadena de consulta de los enlaces de paginación utilizando el método `appends`. Por ejemplo, para añadir `sort=votes` a cada enlace de paginación, debe realizar la siguiente llamada a `appends`:

    use App\Models\User;

    Route::get('/users', function () {
        $users = User::paginate(15);

        $users->appends(['sort' => 'votes']);

        //
    });

Puede utilizar el método `withQueryString` si desea añadir todos los valores de la cadena de consulta de la solicitud actual a los enlaces de paginación:

    $users = User::paginate(15)->withQueryString();

[]()

#### Adición de fragmentos Hash

Si necesita añadir un "fragmento hash" a las URLs generadas por el paginador, puede utilizar el método `fragment`. Por ejemplo, para añadir `#users` al final de cada enlace de paginación, debería invocar el método `fragment` así:

    $users = User::paginate(15)->fragment('users');

[]()

## Visualización de resultados de paginación

Al llamar al método `paginate`, recibirá una instancia de `Illuminate\Pagination\LengthAwarePaginator`, mientras que al llamar al método `simplePaginate` devuelve una instancia de `Illuminate\Pagination\Paginator`. Y, por último, llamar al método `cursorPaginate` devuelve una instancia de `Illuminate\Pagination\CursorPaginator`.

Estos objetos proporcionan varios métodos que describen el conjunto de resultados. Además de estos métodos de ayuda, las instancias de paginador son iteradores y se pueden recorrer en bucle como una array. Por lo tanto, una vez que haya recuperado los resultados, puede mostrarlos y mostrar los enlaces de la página utilizando [Blade](/docs/%7B%7Bversion%7D%7D/blade):

```blade
<div class="container">
    @foreach ($users as $user)
        {{ $user->name }}
    @endforeach
</div>

{{ $users->links() }}
```

El método `links` mostrará los enlaces al resto de páginas del conjunto de resultados. Cada uno de estos enlaces ya contendrá la variable de cadena de consulta de `página` adecuada. Recuerda que el HTML generado por el método `links` es compatible con el [framework CSS Tailwind](https://tailwindcss.com).

[]()

### Ajuste de la ventana de enlace de paginación

Cuando el paginador muestra enlaces de paginación, se muestra el número de la página actual así como enlaces para las tres páginas anteriores y posteriores a la página actual. Mediante el método `onEachSide`, puede controlar cuántos enlaces adicionales se muestran a cada lado de la página actual dentro de la ventana deslizante central de enlaces generada por el paginador:

```blade
{{ $users->onEachSide(5)->links() }}
```

[]()

### Conversión de resultados a JSON

Las clases paginadoras de Laravel implementan el contrato `Illuminate\Contracts\Support\Jsonable` Interface y exponen el método `toJson`, por lo que es muy fácil convertir tus resultados de paginación a JSON. También puede convertir una instancia de paginador a JSON devolviéndola desde una ruta o acción de controlador:

    use App\Models\User;

    Route::get('/users', function () {
        return User::paginate();
    });

El JSON del paginador incluirá metainformación como `total`, `página_actual`, `última_página`, etc. Los registros de resultados están disponibles a través de la clave de `datos` en la array JSON. Este es un ejemplo del JSON creado al devolver una instancia del paginador desde una ruta:

    {
       "total": 50,
       "per_page": 15,
       "current_page": 1,
       "last_page": 4,
       "first_page_url": "http://laravel.app?page=1",
       "last_page_url": "http://laravel.app?page=4",
       "next_page_url": "http://laravel.app?page=2",
       "prev_page_url": null,
       "path": "http://laravel.app",
       "from": 1,
       "to": 15,
       "data":[
            {
                // Record...
            },
            {
                // Record...
            }
       ]
    }

[]()

## Personalización de la vista de paginación

Por defecto, las vistas renderizadas para mostrar los enlaces de paginación son compatibles con el framework [CSS de Tailwind](https://tailwindcss.com). Sin embargo, si no utilizas Tailwind, puedes definir tus propias vistas para mostrar estos enlaces. Cuando llame al método `links` en una instancia de paginador, puede pasar el nombre de la vista como primer argumento del método:

```blade
{{ $paginator->links('view.name') }}

<!-- Passing additional data to the view... -->
{{ $paginator->links('view.name', ['foo' => 'bar']) }}
```

Sin embargo, la forma más sencilla de personalizar las vistas de paginación es exportándolas a su directorio `resources/views/vendor` mediante el comando `vendor:publish`:

```shell
php artisan vendor:publish --tag=laravel-pagination
```

Este comando colocará las vistas en el directorio `resources/views/vendor/pagination` de tu aplicación. El archivo `tailwind.blade.` php dentro de este directorio corresponde a la vista de paginación predeterminada. Puede editar este archivo para modificar el HTML de paginación.

Si desea designar un archivo diferente como vista de paginación por defecto, puede invocar los métodos `defaultView` y `defaultSimpleView` del paginador dentro del método `boot` de su clase `App\Providers\AppServiceProvider`:

    <?php

    namespace App\Providers;

    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot()
        {
            Paginator::defaultView('view-name');

            Paginator::defaultSimpleView('view-name');
        }
    }

[]()

### Uso de Bootstrap

Laravel incluye vistas de paginación construidas usando [Bootstrap CSS](https://getbootstrap.com/). Para utilizar estas vistas en lugar de las vistas por defecto de Tailwind, puedes llamar a los métodos `useBootstrapFour` o `useBootstrapFive` del paginador dentro del método `boot` de tu clase `App\Providers\AppServiceProvider`:

    use Illuminate\Pagination\Paginator;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
    }

[]()

## Métodos de paginación / LengthAwarePaginator

Cada instancia de paginador proporciona información adicional de paginación a través de los siguientes métodos:

|Método                                 |Descripción                                                                                                                     |
|---------------------------------------|--------------------------------------------------------------------------------------------------------------------------------|
|`$paginator->count()`                  |Obtener el número de elementos de la página actual.                                                                             |
|`$paginator->currentPage()`            |Obtener el número de página actual.                                                                                             |
|`$paginator->firstItem()`              |Obtener el número de resultado del primer elemento de los resultados.                                                           |
|`$paginator->getOptions()`             |Obtener las opciones del paginador.                                                                                             |
|`$paginator->getUrlRange($start, $end)`|Crear un rango de URLs de paginación.                                                                                           |
|`$paginator->hasPages()`               |Determinar si hay suficientes elementos para dividir en varias páginas.                                                         |
|`$paginator->hasMorePages()`           |Determinar si hay más elementos en el almacén de datos.                                                                         |
|`$paginator->items()`                  |Obtener los elementos de la página actual.                                                                                      |
|`$paginator->lastItem()`               |Obtener el número de resultado del último elemento de los resultados.                                                           |
|`$paginator->lastPage()`               |Obtener el número de página de la última página disponible. (No disponible si se utiliza `simplePaginate`).                     |
|`$paginator->nextPageUrl()`            |Obtener la URL de la página siguiente.                                                                                          |
|`$paginator->onFirstPage()`            |Determina si el paginador está en la primera página.                                                                            |
|`$paginator->perPage()`                |El número de elementos a mostrar por página.                                                                                    |
|`$paginator->previousPageUrl()`        |Obtener la URL de la página anterior.                                                                                           |
|`$paginator->total()`                  |Determinar el número total de elementos coincidentes en el almacén de datos. (No disponible cuando se utiliza `simplePaginate`).|
|`$paginator->url($page)`               |Obtener la URL de un número de página determinado.                                                                              |
|`$paginator->getPageName()`            |Obtener la variable de cadena de consulta utilizada para almacenar la página.                                                   |
|`$paginator->setPageName($name)`       |Establecer la variable de cadena de consulta utilizada para almacenar la página.                                                |

[]()

## Métodos de Instancia del Paginador de Cursor

Cada instancia de paginador de cursor proporciona información adicional de paginación a través de los siguientes métodos:

|Método                         |Descripción                                                                     |
|-------------------------------|--------------------------------------------------------------------------------|
|`$paginator->count()`          |Obtener el número de elementos de la página actual.                             |
|`$paginator->cursor()`         |Obtener la instancia actual del cursor.                                         |
|`$paginator->getOptions()`     |Obtener las opciones del paginador.                                             |
|`$paginator->hasPages()`       |Determine si hay suficientes elementos para dividir en varias páginas.          |
|`$paginator->hasMorePages()`   |Determine si hay más elementos en el almacén de datos.                          |
|`$paginator->getCursorName()`  |Obtener la variable de cadena de consulta utilizada para almacenar el cursor.   |
|`$paginator->items()`          |Obtener los elementos de la página actual.                                      |
|`$paginator->nextCursor()`     |Obtener la instancia del cursor para el siguiente conjunto de elementos.        |
|`$paginator->nextPageUrl()`    |Obtener la URL de la página siguiente.                                          |
|`$paginator->onFirstPage()`    |Determina si el paginador está en la primera página.                            |
|`$paginator->onLastPage()`     |Determinar si el paginador está en la última página.                            |
|`$paginator->perPage()`        |El número de elementos a mostrar por página.                                    |
|`$paginator->previousCursor()` |Obtener la instancia del cursor para el conjunto de elementos anterior.         |
|`$paginator->previousPageUrl()`|Obtener la URL de la página anterior.                                           |
|`$paginator->setCursorName()`  |Establecer la variable de cadena de consulta utilizada para almacenar el cursor.|
|`$paginator->url($cursor)`     |Obtener la URL para una instancia de cursor dada.                               |
