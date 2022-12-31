# Database: Generador de consultas

- [Introducción](#introduction)
- [Ejecución de consultas a bases de datos](#running-database-queries)
  - [Agrupación de resultados](#chunking-results)
  - [Transmisión perezosa de resultados](#streaming-results-lazily)
  - [Agregados](#aggregates)
- [Sentencias Select](#select-statements)
- [Expresiones en bruto](#raw-expressions)
- [Uniones](#joins)
- [Uniones](#unions)
- [Cláusulas Where básicas](#basic-where-clauses)
  - [Cláusulas Where](#where-clauses)
  - [Cláusulas Or Where](#or-where-clauses)
  - [Cláusulas Where Not](#where-not-clauses)
  - [Cláusulas Where JSON](#json-where-clauses)
  - [Cláusulas adicionales](#additional-where-clauses)
  - [Agrupación Lógica](#logical-grouping)
- [Cláusulas Where Avanzadas](#advanced-where-clauses)
  - [Cláusulas Where Exists](#where-exists-clauses)
  - [Cláusulas Where de Subconsulta](#subquery-where-clauses)
  - [Cláusulas de Referencia de Texto Completo](#full-text-where-clauses)
- [Ordenación, Agrupación, Límite y Desplazamiento](#ordering-grouping-limit-and-offset)
  - [Ordenación](#ordering)
  - [Agrupación](#grouping)
  - [Límite y Desplazamiento](#limit-and-offset)
- [Cláusulas condicionales](#conditional-clauses)
- [Sentencias de inserción](#insert-statements)
  - [Subinserciones](#upserts)
- [Sentencias Update](#update-statements)
  - [Actualización de Columnas JSON](#updating-json-columns)
  - [Incremento y Disminución](#increment-and-decrement)
- [Sentencias Delete](#delete-statements)
- [Bloqueo pesimista](#pessimistic-locking)
- [Depuración](#debugging)

[]()

## Introducción

El constructor de consultas a bases de datos de Laravel proporciona una interfaz cómoda y fluida para crear y ejecutar consultas a bases de datos. Se puede utilizar para realizar la mayoría de las operaciones de base de datos en su aplicación y funciona perfectamente con todos los sistemas de base de datos soportados por Laravel.

El constructor de consultas de Laravel utiliza la vinculación de parámetros PDO para proteger tu aplicación contra ataques de inyección SQL. No hay necesidad de limpiar o desinfectar las cadenas pasadas al constructor de consultas como enlaces de consulta.

> **Advertencia**  
> PDO no soporta la vinculación de nombres de columnas. Por lo tanto, nunca debe permitir que la entrada del usuario dicte los nombres de las columnas referenciadas por sus consultas, incluyendo las columnas "order by".

[]()

## Ejecución de consultas a la base de datos

[]()

#### Recuperación de todas las filas de una tabla

Puede utilizar el método `table` proporcionado por la facade `DB` para iniciar una consulta. El método `table` devuelve una instancia fluida del constructor de consultas para la tabla dada, permitiéndole encadenar más restricciones a la consulta y finalmente recuperar los resultados de la consulta utilizando el método `get`:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\DB;

    class UserController extends Controller
    {
        /**
         * Show a list of all of the application's users.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            $users = DB::table('users')->get();

            return view('user.index', ['users' => $users]);
        }
    }

El método `get` devuelve una instancia de `Illuminate\Support\Collection` que contiene los resultados de la consulta donde cada resultado es una instancia del objeto PHP `stdClass`. Puede acceder al valor de cada columna accediendo a la columna como una propiedad del objeto:

    use Illuminate\Support\Facades\DB;

    $users = DB::table('users')->get();

    foreach ($users as $user) {
        echo $user->name;
    }

> **Nota**  
> Las colecciones de Laravel proporcionan una variedad de métodos extremadamente potentes para mapear y reducir datos. Para más información sobre las colecciones de Laravel, consulta la [documentación de las colecciones](/docs/%7B%7Bversion%7D%7D/collections).

[]()

#### Recuperación de una única fila / columna de una tabla

Si sólo necesita recuperar una única fila de una tabla de la base de datos, puede utilizar el `primer` método de la facade `DB`. Este método devolverá un único objeto `stdClass`:

    $user = DB::table('users')->where('name', 'John')->first();

    return $user->email;

Si no necesita una fila entera, puede extraer un único valor de un registro utilizando el método `value`. Este método devolverá directamente el valor de la columna:

    $email = DB::table('users')->where('name', 'John')->value('email');

Para recuperar una única fila por su valor de columna `id`, utilice el método `find`:

    $user = DB::table('users')->find(3);

[]()

#### Recuperar una lista de valores de columna

Si desea recuperar una instancia `Illuminate\Support\Collection` que contiene los valores de una sola columna, puede utilizar el método `pluck`. En este ejemplo, recuperaremos una colección de títulos de usuario:

    use Illuminate\Support\Facades\DB;

    $titles = DB::table('users')->pluck('title');

    foreach ($titles as $title) {
        echo $title;
    }

Puede especificar la columna que la colección resultante debe utilizar como sus claves proporcionando un segundo argumento al método `pluck`:

    $titles = DB::table('users')->pluck('title', 'name');

    foreach ($titles as $name => $title) {
        echo $title;
    }

[]()

### Agrupación de resultados

Si necesita trabajar con miles de registros de la base de datos, considere utilizar el método `chunk` proporcionado por la facade `DB`. Este método recupera un pequeño trozo de resultados a la vez y alimenta cada trozo en un closure para su procesamiento. Por ejemplo, recuperemos toda la tabla de `usuarios` en trozos de 100 registros cada vez:

    use Illuminate\Support\Facades\DB;

    DB::table('users')->orderBy('id')->chunk(100, function ($users) {
        foreach ($users as $user) {
            //
        }
    });

Puede evitar que se procesen más trozos devolviendo `false` desde el closure:

    DB::table('users')->orderBy('id')->chunk(100, function ($users) {
        // Process the records...

        return false;
    });

Si está actualizando los registros de la base de datos mientras obtiene los resultados en trozos, los resultados de los trozos podrían cambiar de forma inesperada. Si planea actualizar los registros recuperados mientras hace el chunking, siempre es mejor utilizar el método `chunkById`. Este método paginará automáticamente los resultados basándose en la clave primaria del registro:

    DB::table('users')->where('active', false)
        ->chunkById(100, function ($users) {
            foreach ($users as $user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['active' => true]);
            }
        });

> **Advertencia**  
> Al actualizar o eliminar registros dentro de la devolución de llamada de chunk, cualquier cambio en la clave principal o en las claves externas podría afectar a la consulta de chunk. Esto podría provocar que los registros no se incluyeran en los resultados por partes.

[]()

### Transmisión Lenta de Resultados

El método `lazy` funciona de forma similar al [método `chunk`](#chunking-results) en el sentido de que ejecuta la consulta en trozos. Sin embargo, en lugar de pasar cada trozo a una llamada de retorno, el método lazy `(` ) devuelve una [`LazyCollection`](/docs/%7B%7Bversion%7D%7D/collections#lazy-collections), que permite interactuar con los resultados como un único flujo:

```php
use Illuminate\Support\Facades\DB;

DB::table('users')->orderBy('id')->lazy()->each(function ($user) {
    //
});
```

Una vez más, si planea actualizar los registros recuperados mientras itera sobre ellos, es mejor utilizar los métodos `lazyById` o `lazyByIdDesc`. Estos métodos paginarán automáticamente los resultados basándose en la clave primaria del registro:

```php
DB::table('users')->where('active', false)
    ->lazyById()->each(function ($user) {
        DB::table('users')
            ->where('id', $user->id)
            ->update(['active' => true]);
    });
```

> **Advertencia**  
> Al actualizar o eliminar registros mientras se itera sobre ellos, cualquier cambio en la clave primaria o en las claves externas podría afectar a la consulta por trozos. Esto podría provocar que no se incluyeran registros en los resultados.

[]()

### Agregados

El generador de consultas también proporciona una variedad de métodos para recuperar valores agregados como `count`, `max`, `min`, `avg` y `sum`. Puede llamar a cualquiera de estos métodos después de construir su consulta:

    use Illuminate\Support\Facades\DB;

    $users = DB::table('users')->count();

    $price = DB::table('orders')->max('price');

Por supuesto, puede combinar estos métodos con otras cláusulas para ajustar la forma en que se calcula el valor agregado:

    $price = DB::table('orders')
                    ->where('finalized', 1)
                    ->avg('price');

[]()

#### Determinación de la existencia de registros

En lugar de utilizar el método `count` para determinar si existe algún registro que coincida con las restricciones de la consulta, puede utilizar los métodos `exists` y `doesntExist`:

    if (DB::table('orders')->where('finalized', 1)->exists()) {
        // ...
    }

    if (DB::table('orders')->where('finalized', 1)->doesntExist()) {
        // ...
    }

[]()

## Sentencias Select

[]()

#### Especificación de una cláusula de selección

Es posible que no siempre desee seleccionar todas las columnas de una tabla de base de datos. Utilizando el método `select`, puede especificar una cláusula "select" personalizada para la consulta:

    use Illuminate\Support\Facades\DB;

    $users = DB::table('users')
                ->select('name', 'email as user_email')
                ->get();

El método `distinct` permite forzar a la consulta a devolver resultados distintos:

    $users = DB::table('users')->distinct()->get();

Si ya dispone de una instancia del generador de consultas y desea añadir una columna a su cláusula de selección existente, puede utilizar el método `addSelect`:

    $query = DB::table('users')->select('name');

    $users = $query->addSelect('age')->get();

[]()

## Expresiones en bruto

A veces puede ser necesario insertar una cadena arbitraria en una consulta. Para crear una expresión de cadena raw, puede utilizar el método `raw` proporcionado por la facade `DB`:

    $users = DB::table('users')
                 ->select(DB::raw('count(*) as user_count, status'))
                 ->where('status', '<>', 1)
                 ->groupBy('status')
                 ->get();

> **Advertencia**  
> Las expresiones raw serán inyectadas en la consulta como cadenas, por lo que debe ser extremadamente cuidadoso para evitar crear vulnerabilidades de inyección SQL.

[]()

### Métodos Raw

En lugar de utilizar el método `DB::raw`, también puedes utilizar los siguientes métodos para insertar una expresión raw en varias partes de tu consulta. **Recuerda que Laravel no puede garantizar que cualquier consulta que utilice expresiones raw esté protegida contra vulnerabilidades de inyección SQL.**

[]()

#### `selectRaw`

El método `selectRaw` puede utilizarse en lugar de `addSelect(DB::raw(/* ... */))`. Este método acepta una array opcional de enlaces como segundo argumento:

    $orders = DB::table('orders')
                    ->selectRaw('price * ? as price_with_tax', [1.0825])
                    ->get();

[]()

#### `whereRaw / orWhereRaw`

Los métodos `whereRaw` y `orWhereRaw` pueden utilizarse para inyectar una cláusula "where" sin procesar en la consulta. Estos métodos aceptan una array opcional de enlaces como segundo argumento:

    $orders = DB::table('orders')
                    ->whereRaw('price > IF(state = "TX", ?, 100)', [200])
                    ->get();

[]()

#### `havingRaw / orHavingRaw`

Los métodos `havingRaw` y `orHavingRaw` pueden utilizarse para proporcionar una cadena sin procesar como valor de la cláusula "having". Estos métodos aceptan una array opcional de enlaces como segundo argumento:

    $orders = DB::table('orders')
                    ->select('department', DB::raw('SUM(price) as total_sales'))
                    ->groupBy('department')
                    ->havingRaw('SUM(price) > ?', [2500])
                    ->get();

[]()

#### `orderByRaw`

El método `orderByRaw` puede utilizarse para proporcionar una cadena sin procesar como valor de la cláusula "order by":

    $orders = DB::table('orders')
                    ->orderByRaw('updated_at - created_at DESC')
                    ->get();

[]()

### `groupByRaw`

El método `groupByRaw` puede utilizarse para proporcionar una cadena sin procesar como valor de la cláusula `group by`:

    $orders = DB::table('orders')
                    ->select('city', 'state')
                    ->groupByRaw('city, state')
                    ->get();

[]()

## Uniones

[]()

#### Cláusula Inner Join

El generador de consultas también puede utilizarse para añadir cláusulas de unión a las consultas. Para realizar una "unión interna" básica, puede utilizar el método `join` en una instancia del generador de consultas. El primer argumento que se pasa al método `join` es el nombre de la tabla a la que se quiere unir, mientras que los argumentos restantes especifican las restricciones de columna para la unión. Puede incluso unir varias tablas en una sola consulta:

    use Illuminate\Support\Facades\DB;

    $users = DB::table('users')
                ->join('contacts', 'users.id', '=', 'contacts.user_id')
                ->join('orders', 'users.id', '=', 'orders.user_id')
                ->select('users.*', 'contacts.phone', 'orders.price')
                ->get();

[]()

#### Cláusula Left Join / Right Join

Si desea realizar una "left join" o "right join" en lugar de una "inner join", utilice los métodos `leftJoin` o `rightJoin`. Estos métodos tienen la misma firma que el método `join`:

    $users = DB::table('users')
                ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
                ->get();

    $users = DB::table('users')
                ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
                ->get();

[]()

#### Cláusula de unión cruzada

Puede utilizar el método `crossJoin` para realizar una "unión cruzada". Las uniones cruzadas generan un producto cartesiano entre la primera tabla y la tabla unida:

    $sizes = DB::table('sizes')
                ->crossJoin('colors')
                ->get();

[]()

#### Cláusulas de unión avanzadas

También puede especificar cláusulas de unión más avanzadas. Para empezar, pase un closure como segundo argumento al método `join`. El closure recibirá una instancia `Illuminate\Database\Query\JoinClause` que le permitirá especificar restricciones en la cláusula "join":

    DB::table('users')
            ->join('contacts', function ($join) {
                $join->on('users.id', '=', 'contacts.user_id')->orOn(/* ... */);
            })
            ->get();

Si desea utilizar una cláusula "where" en sus uniones, puede utilizar los métodos `where` y `orWhere` proporcionados por la instancia `JoinClause`. En lugar de comparar dos columnas, estos métodos compararán la columna con un valor:

    DB::table('users')
            ->join('contacts', function ($join) {
                $join->on('users.id', '=', 'contacts.user_id')
                     ->where('contacts.user_id', '>', 5);
            })
            ->get();

[]()

#### Subconsultas

Puede utilizar los métodos `joinSub`, `leftJoinSub` y `rightJoinSub` para unir una consulta a una subconsulta. Cada uno de estos métodos recibe tres argumentos: la subconsulta, su alias de tabla y un closure que define las columnas relacionadas. En este ejemplo, recuperaremos una colección de usuarios en la que cada registro de usuario contiene también la fecha y `hora de creación` de la última entrada del blog publicada por el usuario:

    $latestPosts = DB::table('posts')
                       ->select('user_id', DB::raw('MAX(created_at) as last_post_created_at'))
                       ->where('is_published', true)
                       ->groupBy('user_id');

    $users = DB::table('users')
            ->joinSub($latestPosts, 'latest_posts', function ($join) {
                $join->on('users.id', '=', 'latest_posts.user_id');
            })->get();

[]()

## Uniones

El generador de consultas también proporciona un método práctico para "unir" dos o más consultas. Por ejemplo, puede crear una consulta inicial y utilizar el método de `unión` para unirla con más consultas:

    use Illuminate\Support\Facades\DB;

    $first = DB::table('users')
                ->whereNull('first_name');

    $users = DB::table('users')
                ->whereNull('last_name')
                ->union($first)
                ->get();

Además del método de `unión`, el generador de consultas proporciona un método `unionAll`. No se eliminarán los resultados duplicados de las consultas combinadas mediante el método `unionAll`. El método `unionAll` tiene la misma firma que el método `union`.

[]()

## Cláusulas Where básicas

[]()

### Cláusulas Where

Puede utilizar el método `where` del generador de consultas para añadir cláusulas "where" a la consulta. La llamada más básica al método `where` requiere tres argumentos. El primer argumento es el nombre de la columna. El segundo argumento es un operador, que puede ser cualquiera de los soportados por la base de datos. El tercer argumento es el valor a comparar con el valor de la columna.

Por ejemplo, la siguiente consulta recupera los usuarios en los que el valor de la columna `votos` es igual a `100` y el valor de la columna `edad` es superior a `35`:

    $users = DB::table('users')
                    ->where('votes', '=', 100)
                    ->where('age', '>', 35)
                    ->get();

Por conveniencia, si quieres verificar que una columna es `=` a un valor dado, puedes pasar el valor como segundo argumento al método `where`. Laravel asumirá que quieres utilizar el operador `=`:

    $users = DB::table('users')->where('votes', 100)->get();

Como se mencionó anteriormente, puede utilizar cualquier operador que sea compatible con su sistema de base de datos:

    $users = DB::table('users')
                    ->where('votes', '>=', 100)
                    ->get();

    $users = DB::table('users')
                    ->where('votes', '<>', 100)
                    ->get();

    $users = DB::table('users')
                    ->where('name', 'like', 'T%')
                    ->get();

También puede pasar un array de condiciones a la función `where`. Cada elemento de la array debe ser una array que contenga los tres argumentos típicamente pasados al método `where`:

    $users = DB::table('users')->where([
        ['status', '=', '1'],
        ['subscribed', '<>', '1'],
    ])->get();

> **Advertencia**  
> PDO no soporta la vinculación de nombres de columnas. Por lo tanto, nunca debe permitir que la entrada del usuario dicte los nombres de las columnas referenciadas por sus consultas, incluyendo las columnas "order by".

[]()

### Cláusulas Or Where

Al encadenar llamadas al método `where` del constructor de consultas, las cláusulas "where" se unirán utilizando el operador `and`. Sin embargo, puede utilizar el método `orWhere` para unir una cláusula a la consulta utilizando el operador `or`. El método `orWhere` acepta los mismos argumentos que el método `where`:

    $users = DB::table('users')
                        ->where('votes', '>', 100)
                        ->orWhere('name', 'John')
                        ->get();

Si necesitas agrupar una condición "or" entre paréntesis, puedes pasar un closure como primer argumento al método `orWhere`:

    $users = DB::table('users')
                ->where('votes', '>', 100)
                ->orWhere(function($query) {
                    $query->where('name', 'Abigail')
                          ->where('votes', '>', 50);
                })
                ->get();

El ejemplo anterior producirá el siguiente SQL:

```sql
select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
```

> **Advertencia**  
> Debería agrupar siempre las llamadas a `orWhere` para evitar comportamientos inesperados cuando se aplican ámbitos globales.

[]()

### Cláusulas Where Not

Los métodos `whereNot` y `orWhereNot` pueden utilizarse para negar un grupo determinado de restricciones de consulta. Por ejemplo, la siguiente consulta excluye los productos que están en liquidación o que tienen un precio inferior a diez:

    $products = DB::table('products')
                    ->whereNot(function ($query) {
                        $query->where('clearance', true)
                              ->orWhere('price', '<', 10);
                    })
                    ->get();

[]()

### Cláusulas Where JSON

Laravel también soporta la consulta de tipos de columna JSON en bases de datos que proporcionen soporte para tipos de columna JSON. Actualmente, esto incluye MySQL 5.7+, PostgreSQL, SQL Server 2016, y SQLite 3.39.0 (con la [extensión JSON1](https://www.sqlite.org/json1.html)). Para consultar una columna JSON, utilice el operador `->:`

    $users = DB::table('users')
                    ->where('preferences->dining->meal', 'salad')
                    ->get();

Puede utilizar `whereJsonContains` para consultar matrices JSON. Esta función no es compatible con las versiones de bases de datos SQLite inferiores a la 3.38.0:

    $users = DB::table('users')
                    ->whereJsonContains('options->languages', 'en')
                    ->get();

Si su aplicación utiliza las bases de datos MySQL o PostgreSQL, puede pasar un array de valores al método `whereJsonContains`:

    $users = DB::table('users')
                    ->whereJsonContains('options->languages', ['en', 'de'])
                    ->get();

Puede utilizar el método `whereJsonLength` para consultar matrices JSON por su longitud:

    $users = DB::table('users')
                    ->whereJsonLength('options->languages', 0)
                    ->get();

    $users = DB::table('users')
                    ->whereJsonLength('options->languages', '>', 1)
                    ->get();

[]()

### Cláusulas adicionales

**whereBetween / orWhereBetween**

El método `whereBetween` verifica que el valor de una columna se encuentra entre dos valores:

    $users = DB::table('users')
               ->whereBetween('votes', [1, 100])
               ->get();

**whereNotBetween / orWhereNotBetween**

El método `whereNotBetween` verifica que el valor de una columna se encuentra fuera de dos valores:

    $users = DB::table('users')
                        ->whereNotBetween('votes', [1, 100])
                        ->get();

**whereBetweenColumns / whereNotBetweenColumns / orWhereBetweenColumns / orWhereNotBetweenColumns**

El método `whereBetweenColumns` comprueba que el valor de una columna se encuentra entre los dos valores de dos columnas de la misma fila de la tabla:

    $patients = DB::table('patients')
                           ->whereBetweenColumns('weight', ['minimum_allowed_weight', 'maximum_allowed_weight'])
                           ->get();

El método `whereNotBetweenColumns` verifica que el valor de una columna se encuentra fuera de los dos valores de dos columnas de la misma fila de la tabla:

    $patients = DB::table('patients')
                           ->whereNotBetweenColumns('weight', ['minimum_allowed_weight', 'maximum_allowed_weight'])
                           ->get();

**whereIn / whereNotIn / orWhereIn / orWhereNotIn**

El método `whereIn` verifica que el valor de una columna dada se encuentra dentro de la array dada:

    $users = DB::table('users')
                        ->whereIn('id', [1, 2, 3])
                        ->get();

El método `whereNotIn` verifica que el valor de la columna dada no está contenido en el array dado:

    $users = DB::table('users')
                        ->whereNotIn('id', [1, 2, 3])
                        ->get();

> **Advertencia**  
> Si va a añadir a la consulta una gran array de valores enteros, puede utilizar los métodos `whereIntegerInRaw` o `whereIntegerNotInRaw` para reducir considerablemente el uso de memoria.

**whereNull / whereNotNull / orWhereNull / orWhereNotNull**

El método `whereNull` verifica que el valor de la columna dada es `NULL`:

    $users = DB::table('users')
                    ->whereNull('updated_at')
                    ->get();

El método `whereNotNull` verifica que el valor de la columna no es `NULL`:

    $users = DB::table('users')
                    ->whereNotNull('updated_at')
                    ->get();

**whereDate / whereMonth / whereDay / whereYear / whereTime**

El método `whereDate` puede utilizarse para comparar el valor de una columna con una fecha:

    $users = DB::table('users')
                    ->whereDate('created_at', '2016-12-31')
                    ->get();

El método `whereMonth` permite comparar el valor de una columna con un mes determinado:

    $users = DB::table('users')
                    ->whereMonth('created_at', '12')
                    ->get();

El método `whereDay` permite comparar el valor de una columna con un día concreto del mes:

    $users = DB::table('users')
                    ->whereDay('created_at', '31')
                    ->get();

El método `whereYear` permite comparar el valor de una columna con un año determinado:

    $users = DB::table('users')
                    ->whereYear('created_at', '2016')
                    ->get();

El método `whereTime` permite comparar el valor de una columna con una hora determinada:

    $users = DB::table('users')
                    ->whereTime('created_at', '=', '11:20:45')
                    ->get();

**whereColumn / orWhereColumn**

El método `whereColumn` puede utilizarse para verificar que dos columnas son iguales:

    $users = DB::table('users')
                    ->whereColumn('first_name', 'last_name')
                    ->get();

También puede pasar un operador de comparación al método `whereColumn`:

    $users = DB::table('users')
                    ->whereColumn('updated_at', '>', 'created_at')
                    ->get();

También puede pasar un array de comparaciones de columnas al método `whereColumn`. Estas condiciones se unirán utilizando el operador `and`:

    $users = DB::table('users')
                    ->whereColumn([
                        ['first_name', '=', 'last_name'],
                        ['updated_at', '>', 'created_at'],
                    ])->get();

[]()

### Agrupación Lógica

A veces puede que necesite agrupar varias cláusulas "where" entre paréntesis para lograr la agrupación lógica deseada de su consulta. De hecho, por lo general siempre debe agrupar las llamadas al método `orWhere` entre paréntesis para evitar un comportamiento inesperado de la consulta. Para conseguir esto, puede pasar un closure al método `where`:

    $users = DB::table('users')
               ->where('name', '=', 'John')
               ->where(function ($query) {
                   $query->where('votes', '>', 100)
                         ->orWhere('title', '=', 'Admin');
               })
               ->get();

Como puede ver, al pasar un closure al método `where` se indica al generador de consultas que inicie un grupo de restricciones. El closure recibirá una instancia del generador de consultas que puede utilizar para establecer las restricciones que debe contener el grupo de paréntesis. El ejemplo anterior producirá el siguiente SQL:

```sql
select * from users where name = 'John' and (votes > 100 or title = 'Admin')
```

> **Advertencia**  
> Debería agrupar siempre las llamadas a `orWhere` para evitar comportamientos inesperados cuando se aplican ámbitos globales.

[]()

### Cláusulas Where Avanzadas

[]()

### Cláusulas Where Exists

El método `whereExists` permite escribir cláusulas SQL "where exists". El método `whereExists` acepta un closure que recibirá una instancia del constructor de consultas, permitiéndole definir la consulta que debe colocarse dentro de la cláusula "exists":

    $users = DB::table('users')
               ->whereExists(function ($query) {
                   $query->select(DB::raw(1))
                         ->from('orders')
                         ->whereColumn('orders.user_id', 'users.id');
               })
               ->get();

La consulta anterior producirá el siguiente SQL:

```sql
select * from users
where exists (
    select 1
    from orders
    where orders.user_id = users.id
)
```

[]()

### Cláusulas Where de subconsulta

A veces puede ser necesario construir una cláusula "where" que compare los resultados de una subconsulta con un valor dado. Para ello, puede pasar un closure y un valor al método `where`. Por ejemplo, la siguiente consulta recuperará todos los usuarios que tengan una "afiliación" reciente de un tipo determinado;

    use App\Models\User;

    $users = User::where(function ($query) {
        $query->select('type')
            ->from('membership')
            ->whereColumn('membership.user_id', 'users.id')
            ->orderByDesc('membership.start_date')
            ->limit(1);
    }, 'Pro')->get();

O puede que necesite construir una cláusula "where" que compare una columna con los resultados de una subconsulta. Para ello, puede pasar una columna, un operador y un closure al método `where`. Por ejemplo, la siguiente consulta recuperará todos los registros de ingresos cuyo importe sea inferior a la media;

    use App\Models\Income;

    $incomes = Income::where('amount', '<', function ($query) {
        $query->selectRaw('avg(i.amount)')->from('incomes as i');
    })->get();

[]()

### Cláusulas Where de texto completo

> **Advertencia**  
> Las cláusulas where de texto completo son soportadas actualmente por MySQL y PostgreSQL.

Los métodos `whereFullText` y `orWhereFullText` pueden utilizarse para añadir cláusulas "where" de texto completo a una consulta para columnas que tengan [índices de texto completo](/docs/%7B%7Bversion%7D%7D/migrations#available-index-types). Laravel transformará estos métodos en el SQL apropiado para el sistema de base de datos subyacente. Por ejemplo, se generará una cláusula `MATCH AGAINST` para aplicaciones que utilicen MySQL:

    $users = DB::table('users')
               ->whereFullText('bio', 'web developer')
               ->get();

[]()

## Ordenación, Agrupación, Límite y Desplazamiento

[]()

### Ordenación

[]()

#### El método `orderBy`

El método `orderBy` permite ordenar los resultados de la consulta por una columna determinada. El primer argumento aceptado por el método `orderBy` debe ser la columna por la que desea ordenar, mientras que el segundo argumento determina la dirección de la ordenación y puede ser `asc` o `desc`:

    $users = DB::table('users')
                    ->orderBy('name', 'desc')
                    ->get();

Para ordenar por varias columnas, basta con invocar `orderBy` tantas veces como sea necesario:

    $users = DB::table('users')
                    ->orderBy('name', 'desc')
                    ->orderBy('email', 'asc')
                    ->get();

[]()

#### Los métodos `más` reciente y más `antiguo`

Los métodos `latest` y `oldest` permiten ordenar fácilmente los resultados por fecha. Por defecto, el resultado se ordenará por la columna `created_at` de la tabla. O bien, puede pasar el nombre de la columna por la que desea ordenar:

    $user = DB::table('users')
                    ->latest()
                    ->first();

[]()

#### Ordenación aleatoria

El método `inRandomOrder` puede utilizarse para ordenar los resultados de la consulta de forma aleatoria. Por ejemplo, puede utilizar este método para obtener un usuario aleatorio:

    $randomUser = DB::table('users')
                    ->inRandomOrder()
                    ->first();

[]()

#### Eliminación de ordenaciones existentes

El método `reorder` elimina todas las cláusulas "order by" que se hayan aplicado previamente a la consulta:

    $query = DB::table('users')->orderBy('name');

    $unorderedUsers = $query->reorder()->get();

Puede pasar una columna y una dirección cuando llame al método `reorder` para eliminar todas las cláusulas "order by" existentes y aplicar un orden completamente nuevo a la consulta:

    $query = DB::table('users')->orderBy('name');

    $usersOrderedByEmail = $query->reorder('email', 'desc')->get();

[]()

### Agrupación

[]()

#### Los métodos `groupBy` y `having`

Como era de esperar, los métodos `groupBy` y `having` se pueden utilizar para agrupar los resultados de la consulta. La firma del método `having` es similar a la del método `where`:

    $users = DB::table('users')
                    ->groupBy('account_id')
                    ->having('account_id', '>', 100)
                    ->get();

Puede utilizar el método `havingBetween` para filtrar los resultados dentro de un rango determinado:

    $report = DB::table('orders')
                    ->selectRaw('count(id) as number_of_orders, customer_id')
                    ->groupBy('customer_id')
                    ->havingBetween('number_of_orders', [5, 15])
                    ->get();

Puede pasar varios argumentos al método `groupBy` para agrupar por varias columnas:

    $users = DB::table('users')
                    ->groupBy('first_name', 'status')
                    ->having('account_id', '>', 100)
                    ->get();

Para construir sentencias `having` más avanzadas, consulte el método [`havingRaw`](#raw-methods).

[]()

### Límite y Desplazamiento

[]()

#### Los métodos `skip` y `take`

Puede utilizar los métodos `skip` y `take` para limitar el número de resultados devueltos por la consulta o para omitir un número determinado de resultados en la consulta:

    $users = DB::table('users')->skip(10)->take(5)->get();

También puede utilizar los métodos `limit` y `offset`. Estos métodos son funcionalmente equivalentes a los métodos `take` y `skip`, respectivamente:

    $users = DB::table('users')
                    ->offset(10)
                    ->limit(5)
                    ->get();

[]()

## Cláusulas Condicionales

A veces puede que desee que ciertas cláusulas de consulta se apliquen a una consulta basada en otra condición. Por ejemplo, puede que sólo desee aplicar una sentencia `where` si un determinado valor de entrada está presente en la petición HTTP entrante. Para ello puede utilizar el método `when`:

    $role = $request->input('role');

    $users = DB::table('users')
                    ->when($role, function ($query, $role) {
                        $query->where('role_id', $role);
                    })
                    ->get();

El método `when` sólo ejecuta el closure dado cuando el primer argumento es `verdadero`. Si el primer argumento es `falso`, el closure no se ejecutará. Así, en el ejemplo anterior, el closure dado al método `when` sólo será invocado si el campo `role` está presente en la petición entrante y se evalúa como `verdadero`.

Puede pasar otro closure como tercer argumento al método `when`. Este closure sólo se ejecutará si el primer argumento se evalúa como `falso`. Para ilustrar cómo se puede utilizar esta característica, la utilizaremos para configurar el orden por defecto de una consulta:

    $sortByVotes = $request->input('sort_by_votes');

    $users = DB::table('users')
                    ->when($sortByVotes, function ($query, $sortByVotes) {
                        $query->orderBy('votes');
                    }, function ($query) {
                        $query->orderBy('name');
                    })
                    ->get();

[]()

## Sentencias Insert

El constructor de consultas también proporciona un método `insert` que puede utilizarse para insertar registros en la tabla de la base de datos. El método de `inserción` acepta una array de nombres y valores de columna:

    DB::table('users')->insert([
        'email' => 'kayla@example.com',
        'votes' => 0
    ]);

Puede insertar varios registros a la vez pasando un array de arrays. Cada array representa un registro que debe insertarse en la tabla:

    DB::table('users')->insert([
        ['email' => 'picard@example.com', 'votes' => 0],
        ['email' => 'janeway@example.com', 'votes' => 0],
    ]);

El método `insertOrIgnore` ignorará los errores al insertar registros en la base de datos. Cuando utilice este método, debe tener en cuenta que los errores de registros duplicados serán ignorados y que otros tipos de errores también pueden ser ignorados dependiendo del motor de la base de datos. Por ejemplo, `insertOrIgnore` [omitirá el modo estricto de MySQL](https://dev.mysql.com/doc/refman/en/sql-mode.html#ignore-effect-on-execution):

    DB::table('users')->insertOrIgnore([
        ['id' => 1, 'email' => 'sisko@example.com'],
        ['id' => 2, 'email' => 'archer@example.com'],
    ]);

El método `insertUsing` insertará nuevos registros en la tabla utilizando una subconsulta para determinar los datos que deben insertarse:

    DB::table('pruned_users')->insertUsing([
        'id', 'name', 'email', 'email_verified_at'
    ], DB::table('users')->select(
        'id', 'name', 'email', 'email_verified_at'
    )->where('updated_at', '<=', now()->subMonth()));

[]()

#### IDs auto-incrementados

Si la tabla tiene un ID auto-incrementable, utilice el método `insertGetId` para insertar un registro y luego recuperar el ID:

    $id = DB::table('users')->insertGetId(
        ['email' => 'john@example.com', 'votes' => 0]
    );

> **Advertencia**  
> Cuando se utiliza PostgreSQL el método `insertGetId` espera que la columna auto-incrementable se llame `id`. Si desea recuperar el ID de una "secuencia" diferente, puede pasar el nombre de la columna como segundo parámetro al método `insertGetId`.

[]()

### Subidas

El método `upsert` insertará registros que no existen y actualizará los registros que ya existen con los nuevos valores que usted especifique. El primer argumento del método consiste en los valores a insertar o actualizar, mientras que el segundo argumento enumera la(s) columna(s) que identifican unívocamente los registros dentro de la tabla asociada. El tercer y último argumento del método es un array de columnas que deben actualizarse si ya existe un registro coincidente en la base de datos:

    DB::table('flights')->upsert(
        [
            ['departure' => 'Oakland', 'destination' => 'San Diego', 'price' => 99],
            ['departure' => 'Chicago', 'destination' => 'New York', 'price' => 150]
        ],
        ['departure', 'destination'],
        ['price']
    );

En el ejemplo anterior, Laravel intentará insertar dos registros. Si ya existe un registro con los mismos valores de columna de `salida` y `destino`, Laravel actualizará la columna de `precio` de ese registro.

> **Advertencia**  
> Todas las bases de datos excepto SQL Server requieren que las columnas del segundo argumento del método `upsert` tengan un índice "primario" o "único". Además, el controlador de base de datos MySQL ignora el segundo argumento del método `upsert` y utiliza siempre los índices "primario" y "único" de la tabla para detectar los registros existentes.

[]()

## Sentencias de actualización

Además de insertar registros en la base de datos, el constructor de consultas también puede actualizar registros existentes utilizando el método `update`. El método `update`, al igual que el método `insert`, acepta un array de pares columna y valor indicando las columnas a actualizar. El método `update` devuelve el número de filas afectadas. Puede restringir la consulta de `actualización` mediante cláusulas `where`:

    $affected = DB::table('users')
                  ->where('id', 1)
                  ->update(['votes' => 1]);

[]()

#### Actualizar o insertar

A veces es posible que desee actualizar un registro existente en la base de datos o crearlo si no existe ningún registro coincidente. En este caso, se puede utilizar el método `updateOrInsert`. El método `updateOrInsert` acepta dos argumentos: una array de condiciones para encontrar el registro y una array de pares de columnas y valores que indican las columnas que deben actualizarse.

El método `updateOrInsert` intentará localizar un registro coincidente en la base de datos utilizando los pares de columna y valor del primer argumento. Si el registro existe, se actualizará con los valores del segundo argumento. Si no se encuentra el registro, se insertará uno nuevo con los atributos combinados de ambos argumentos:

    DB::table('users')
        ->updateOrInsert(
            ['email' => 'john@example.com', 'name' => 'John'],
            ['votes' => '2']
        );

[]()

### Actualización de Columnas JSON

Al actualizar una columna JSON, debe utilizar la sintaxis `->` para actualizar la clave apropiada en el objeto JSON. Esta operación está soportada en MySQL 5.7+ y PostgreSQL 9.5+:

    $affected = DB::table('users')
                  ->where('id', 1)
                  ->update(['options->enabled' => true]);

[]()

### Incremento y Disminución

El constructor de consultas también proporciona métodos convenientes para incrementar o decrementar el valor de una columna dada. Ambos métodos aceptan al menos un argumento: la columna a modificar. Se puede proporcionar un segundo argumento para especificar la cantidad en la que se debe incrementar o decrementar la columna:

    DB::table('users')->increment('votes');

    DB::table('users')->increment('votes', 5);

    DB::table('users')->decrement('votes');

    DB::table('users')->decrement('votes', 5);

También puede especificar columnas adicionales para actualizar durante la operación:

    DB::table('users')->increment('votes', 1, ['name' => 'John']);

[]()

## Sentencias Delete

El método `delete` del constructor de consultas puede utilizarse para eliminar registros de la tabla. El método `delete` devuelve el número de filas afectadas. Puede restringir las sentencias de `borrado` añadiendo cláusulas "where" antes de llamar al método `delete`:

    $deleted = DB::table('users')->delete();

    $deleted = DB::table('users')->where('votes', '>', 100)->delete();

Si desea truncar una tabla completa, lo que eliminará todos los registros de la tabla y restablecerá el ID autoincrementado a cero, puede utilizar el método `truncar`:

    DB::table('users')->truncate();

[]()

#### Truncado de tablas y PostgreSQL

Al truncar una base de datos PostgreSQL, se aplicará el comportamiento `CASCADE`. Esto significa que todos los registros relacionados con claves foráneas en otras tablas serán borrados también.

[]()

## Bloqueo Pesimista

El generador de consultas también incluye algunas funciones que le ayudarán a conseguir un "bloqueo pesimista" al ejecutar sus sentencias `select`. Para ejecutar una sentencia con un "bloqueo compartido", puede llamar al método `sharedLock`. Un bloqueo compartido impide que se modifiquen las filas seleccionadas hasta que se confirme la transacción:

    DB::table('users')
            ->where('votes', '>', 100)
            ->sharedLock()
            ->get();

Alternativamente, puede utilizar el método `lockForUpdate`. Un bloqueo "for update" impide que se modifiquen los registros seleccionados o que se seleccionen con otro bloqueo compartido:

    DB::table('users')
            ->where('votes', '>', 100)
            ->lockForUpdate()
            ->get();

[]()

## Depuración

Puede utilizar los métodos `dd` y `dump` mientras construye una consulta para volcar los enlaces de consulta y SQL actuales. El método `dd` mostrará la información de depuración y luego detendrá la ejecución de la consulta. El método `dump` mostrará la información de depuración pero permitirá que la petición continúe ejecutándose:

    DB::table('users')->where('votes', '>', 100)->dd();

    DB::table('users')->where('votes', '>', 100)->dump();
