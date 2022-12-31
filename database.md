# Base de datos: Introducción

- [Introducción](#introduction)
  - [Configuración](#configuration)
  - [Conexiones de lectura y escritura](#read-and-write-connections)
- [Ejecución de consultas SQL](#running-queries)
  - [Uso de múltiples conexiones a bases de datos](#using-multiple-database-connections)
  - [Escucha de Eventos de Consulta](#listening-for-query-events)
  - [Monitorización del Tiempo de Consulta Acumulado](#monitoring-cumulative-query-time)
- [Transacciones de Base de Datos](#database-transactions)
- [Conexión a la CLI de la Base de Datos](#connecting-to-the-database-cli)
- [Inspección de Bases de Datos](#inspecting-your-databases)
- [Monitorización de Bases de Datos](#monitoring-your-databases)

[]()

## Introducción

Casi todas las aplicaciones web modernas interactúan con una base de datos. Laravel hace que la interacción con bases de datos sea extremadamente simple a través de una variedad de bases de datos soportadas usando SQL sin procesar, un [constructor de consultas fluido](/docs/%7B%7Bversion%7D%7D/queries), y el [ORM Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent). Actualmente, Laravel proporciona soporte de primera parte para cinco bases de datos:

<div class="content-list" markdown="1"/>

- MariaDB 10.3+[( policy versiones](https://mariadb.org/about/#maintenance-policy))
- MySQL 5.7+ (Política[ policy](https://en.wikipedia.org/wiki/MySQL#Release_history)[ policy](https://en.wikipedia.org/wiki/MySQL#Release_history)[versiones](https://en.wikipedia.org/wiki/MySQL#Release_history))
- PostgreSQL 10.0+ (política[ policy](https://www.postgresql.org/support/versioning/)[ policy](https://www.postgresql.org/support/versioning/)[versiones](https://www.postgresql.org/support/versioning/))
- SQLite 3.8.8+
- SQL Server 2017+[(](https://docs.microsoft.com/en-us/lifecycle/products/?products=sql-server)política[ policy](https://docs.microsoft.com/en-us/lifecycle/products/?products=sql-server)[ policy](https://docs.microsoft.com/en-us/lifecycle/products/?products=sql-server) versión)

[object Object]

[]()

### Configuración

La configuración de los servicios de base de datos de Laravel se encuentra en el fichero de configuración `config/database.php` de tu aplicación. En este archivo, puedes definir todas tus conexiones de base de datos, así como especificar qué conexión se debe utilizar por defecto. La mayoría de las opciones de configuración dentro de este archivo son controladas por los valores de las variables de entorno de tu aplicación. En este archivo se proporcionan ejemplos para la mayoría de los sistemas de bases de datos soportados por Laravel.

De forma predeterminada, la [configuración del entorno](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration) de muestra de Laravel está lista para usar con [Laravel Sail](/docs/%7B%7Bversion%7D%7D/sail), que es una configuración de Docker para desarrollar aplicaciones Laravel en su máquina local. Sin embargo, eres libre de modificar la configuración de la base de datos según sea necesario para tu base de datos local.

[]()

#### Configuración de SQLite

Las bases de datos SQLite están contenidas dentro de un único fichero en tu sistema de ficheros. Puedes crear una nueva base de datos SQLite usando el comando `touch` en tu terminal: `touch database/database.sqlite`. Una vez creada la base de datos, puede configurar fácilmente sus variables de entorno para que apunten a esta base de datos colocando la ruta absoluta a la base de datos en la variable de entorno `DB_DATABASE`:

```ini
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

Para habilitar las restricciones de clave externa para las conexiones SQLite, debe establecer la variable de entorno `DB_FOREIGN_KEYS` en `true`:

```ini
DB_FOREIGN_KEYS=true
```

[]()

#### Configuración de Microsoft SQL Server

Para usar una base de datos Microsoft SQL Server, debe asegurarse de tener instaladas las extensiones de PHP `sqlsrv` y `pdo_sqlsrv`, así como cualquier dependencia que puedan requerir, como el controlador ODBC de Microsoft SQL.

[]()

#### Configuración usando URLs

Típicamente, las conexiones a bases de datos son configuradas usando múltiples valores de configuración tales como `host`, `base de datos`, `nombre de usuario`, `contraseña`, etc. Cada uno de estos valores de configuración tiene su correspondiente variable de entorno. Esto significa que al configurar la información de conexión a la base de datos en un servidor de producción, es necesario gestionar varias variables de entorno.

Algunos proveedores de bases de datos gestionadas como AWS y Heroku proporcionan una única "URL" de base de datos que contiene toda la información de conexión para la base de datos en una sola cadena. Un ejemplo de URL de base de datos puede parecerse a lo siguiente:

```html
mysql://root:password@127.0.0.1/forge?charset=UTF-8
```

Estas URLs suelen seguir una convención de esquema estándar:

```html
driver://username:password@host:port/database?options
```

Por conveniencia, Laravel soporta estas URLs como una alternativa a la configuración de tu base de datos con múltiples opciones de configuración. Si la opción de configuración `url` (o la correspondiente variable de entorno `DATABASE_URL` ) está presente, se utilizará para extraer la información de conexión y credenciales de la base de datos.

[]()

### Conexiones de lectura y escritura

A veces es posible que desees utilizar una conexión de base de datos para las sentencias SELECT, y otra para las sentencias INSERT, UPDATE y DELETE. Laravel hace que esto sea muy fácil, y siempre se utilizarán las conexiones adecuadas, ya sea que estés utilizando consultas sin formato, el constructor de consultas, o el ORM Eloquent.

Para ver cómo se deben configurar las conexiones de lectura / escritura, veamos este ejemplo:

    'mysql' => [
        'read' => [
            'host' => [
                '192.168.1.1',
                '196.168.1.2',
            ],
        ],
        'write' => [
            'host' => [
                '196.168.1.3',
            ],
        ],
        'sticky' => true,
        'driver' => 'mysql',
        'database' => 'database',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],

Observa que se han añadido tres claves al array array configuración: `read`, `write` y `sticky`. Las claves de `lectura` y `escritura` tienen valores de array que contienen una única clave: `host`. El resto de las opciones de base de datos para las conexiones de `lectura` y `escritura` se fusionarán desde el array array configuración principal `de mysql`.

Sólo necesitas colocar elementos en las matrices de `lectura` y `escritura` si deseas anular los valores de la array principal `de mysql`. Así, en este caso, `192.168.1.1` se utilizará como host para la conexión de "lectura", mientras que `192.168.1.3` se utilizará para la conexión de "escritura". Las credenciales de la base de datos, el prefijo, el juego de caracteres y el resto de opciones del array principal de `mysql` se compartirán en ambas conexiones. Cuando existan múltiples valores en el array de configuración del `host`, se elegirá aleatoriamente un host de base de datos para cada petición.

[]()

#### La opción `sticky`

La opción `sticky` es un valor *opcional* que puede usarse para permitir la lectura inmediata de registros que han sido escritos en la base de datos durante el ciclo de petición actual. Si la opción `"` sticky" está activada y se ha realizado una operación de "escritura" en la base de datos durante el ciclo de solicitud actual, cualquier otra operación de "lectura" utilizará la conexión de "escritura". Esto asegura que cualquier dato escrito durante el ciclo de petición pueda ser inmediatamente leído de nuevo desde la base de datos durante esa misma petición. Depende de usted decidir si este es el comportamiento deseado para su aplicación.

[]()

## Ejecución de consultas SQL

Una vez que haya configurado su conexión a la base de datos, puede ejecutar consultas utilizando la facade `DB`. La facade `DB` proporciona métodos para cada tipo de consulta: `select`, `update`, `insert`, `delete` y `statement`.

[]()

#### Ejecución de una consulta Select

Para ejecutar una consulta SELECT básica, puede utilizar el método `select` de la facade `BD`:

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
            $users = DB::select('select * from users where active = ?', [1]);

            return view('user.index', ['users' => $users]);
        }
    }

El primer argumento que se pasa al método `select` es la consulta SQL, mientras que el segundo argumento es cualquier parámetro vinculado a la consulta. Típicamente, estos son los valores de las restricciones de la cláusula `where`. La vinculación de parámetros proporciona protección contra la inyección SQL.

El método `select` siempre devuelve una `array` de resultados. Cada resultado dentro del array será un objeto PHP `stdClass` representando un registro de la base de datos:

    use Illuminate\Support\Facades\DB;

    $users = DB::select('select * from users');

    foreach ($users as $user) {
        echo $user->name;
    }

[]()

#### Selección de Valores Escalares

Algunas veces su consulta a la base de datos puede resultar en un único valor escalar. En lugar de tener que recuperar el resultado escalar de la consulta desde un objeto registro, Laravel le permite recuperar este valor directamente utilizando el método `scalar`:

    $burgers = DB::scalar(
        "select count(case when food = 'burger' then 1 end) as burgers from menu"
    );

[]()

#### Uso de Named Bindings

En lugar de utilizar `?` para representar los parámetros, puedes ejecutar una consulta utilizando named bindings:

    $results = DB::select('select * from users where id = :id', ['id' => 1]);

[]()

#### Ejecutar una sentencia de inserción

Para ejecutar una sentencia `insert`, puede utilizar el método `insert` de la facade `DB`. Al igual que `select`, este método acepta la consulta SQL como primer argumento y los parámetros como segundo argumento:

    use Illuminate\Support\Facades\DB;

    DB::insert('insert into users (id, name) values (?, ?)', [1, 'Marc']);

[]()

#### Ejecución de una Sentencia Update

El método `update` debe utilizarse para actualizar registros existentes en la base de datos. El método devuelve el número de filas afectadas por la sentencia:

    use Illuminate\Support\Facades\DB;

    $affected = DB::update(
        'update users set votes = 100 where name = ?',
        ['Anita']
    );

[]()

#### Ejecución de una sentencia Delete

El método `delete` se utiliza para eliminar registros de la base de datos. Al igual que con `update`, el método devuelve el número de filas afectadas:

    use Illuminate\Support\Facades\DB;

    $deleted = DB::delete('delete from users');

[]()

#### Ejecución de una sentencia general

Algunas sentencias de base de datos no devuelven ningún valor. Para este tipo de operaciones, puede utilizar el método `statement` de la facade `DB`:

    DB::statement('drop table users');

[]()

#### Ejecución de una Sentencia No Preparada

En ocasiones, es posible que desee ejecutar una sentencia SQL sin vincular ningún valor. Para ello puede utilizar el método `unprepared` de facade fachada `DB`:

    DB::unprepared('update users set votes = 100 where name = "Dries"');

> **Advertencia**  
> Dado que las sentencias no preparadas no vinculan parámetros, pueden ser vulnerables a inyecciones SQL. Nunca debe permitir valores controlados por el usuario dentro de una sentencia unprepared.

[]()

#### Confirmaciones implícitas

Cuando utilice las `sentencias` de la facade `DB` y los métodos `no preparados` dentro de las transacciones debe tener cuidado de evitar sentencias que causen [commits implícitos](https://dev.mysql.com/doc/refman/8.0/en/implicit-commit.html). Estas sentencias harán que el motor de la base de datos confirme indirectamente toda la transacción, dejando a Laravel sin conocimiento del nivel de transacción de la base de datos. Un ejemplo de este tipo de sentencia es la creación de una tabla de base de datos:

    DB::unprepared('create table a (col varchar(1) null)');

Por favor, consulte el manual de MySQL para obtener [una lista de todas las declaraciones](https://dev.mysql.com/doc/refman/8.0/en/implicit-commit.html) que desencadenan commits implícitos.

[]()

### Uso de Múltiples Conexiones a Bases de Datos

Si su aplicación define múltiples conexiones en su archivo de configuración `config/database.` php, puede acceder a cada conexión a través del método de `conexión` proporcionado por la facade `DB`. El nombre de conexión pasado al método de `conexión` debe corresponder a una de las conexiones listadas en su archivo de configuración `config/database.` php o configurada en tiempo de ejecución usando el ayudante `config`:

    use Illuminate\Support\Facades\DB;

    $users = DB::connection('sqlite')->select(/* ... */);

Puede acceder a la instancia PDO subyacente de una conexión usando el método `getPdo` en una instancia de conexión:

    $pdo = DB::connection()->getPdo();

[]()

### Escucha de Eventos de Consulta

Si desea especificar un closure que sea invocado para cada consulta SQL ejecutada por su aplicación, puede utilizar el método `listen` de la facade `DB`. Este método puede ser útil para el registro de consultas o depuración. Puedes registrar tu cierre closure escucha de consultas en el método `boot` de un [proveedor de servicios](/docs/%7B%7Bversion%7D%7D/providers):

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\ServiceProvider;

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
            DB::listen(function ($query) {
                // $query->sql;
                // $query->bindings;
                // $query->time;
            });
        }
    }

[]()

### Monitorización del Tiempo de Consulta Acumulado

Un cuello de botella común en el rendimiento de las aplicaciones web modernas es la cantidad de tiempo que pasan consultando bases de datos. Afortunadamente, Laravel puede invocar un closure o callback de tu elección cuando pasa demasiado tiempo consultando la base de datos durante una sola petición. Para empezar, proporciona un umbral de tiempo de consulta (en milisegundos) y un closure al método `whenQueryingForLongerThan`. Puede invocar este método en el método de `arranque` de un [proveedor de servicios](/docs/%7B%7Bversion%7D%7D/providers):

    <?php

    namespace App\Providers;

    use Illuminate\Database\Connection;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\ServiceProvider;
    use Illuminate\Database\Events\QueryExecuted;

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
            DB::whenQueryingForLongerThan(500, function (Connection $connection, QueryExecuted $event) {
                // Notify development team...
            });
        }
    }

[]()

## Transacciones de Base de Datos

Puede usar el método `transaction` proporcionado por la facade `DB` para ejecutar un conjunto de operaciones dentro de una transacción de base de datos. Si se lanza una excepción dentro del cierre closure la transacción, la transacción se revertirá automáticamente y se volverá a lanzar la excepción. Si el closure se ejecuta con éxito, la transacción se confirmará automáticamente. No es necesario preocuparse de revertir o confirmar manualmente mientras se utiliza el método de `transacción`:

    use Illuminate\Support\Facades\DB;

    DB::transaction(function () {
        DB::update('update users set votes = 1');

        DB::delete('delete from posts');
    });

[]()

#### Manejo de Bloqueos

El método de `transacción` acepta un segundo argumento opcional que define el número de veces que una transacción debe ser reintentada cuando se produce un bloqueo. Una vez agotados estos intentos, se lanzará una excepción:

    use Illuminate\Support\Facades\DB;

    DB::transaction(function () {
        DB::update('update users set votes = 1');

        DB::delete('delete from posts');
    }, 5);

[]()

#### Uso manual de transacciones

Si desea iniciar una transacción manualmente y tener un control completo sobre las reversiones y confirmaciones, puede utilizar el método `beginTransaction` proporcionado por la facade `DB`:

    use Illuminate\Support\Facades\DB;

    DB::beginTransaction();

Puede revertir la transacción mediante el método `rollBack`:

    DB::rollBack();

Por último, puede confirmar una transacción mediante el método `commit`:

    DB::commit();

> **Nota**  
> Los métodos de transacción de la facade `DB` controlan las transacciones tanto para el [constructor de consultas](/docs/%7B%7Bversion%7D%7D/queries) como para [Eloquent ORM](/docs/%7B%7Bversion%7D%7D/eloquent).

[]()

## Conexión a la CLI de la Base de Datos

Si desea conectarse a la CLI de su base de datos, puede utilizar el comando `db` Artisan:

```shell
php artisan db
```

Si es necesario, puede especificar un nombre de conexión de base de datos para conectarse a una conexión de base de datos que no sea la conexión por defecto:

```shell
php artisan db mysql
```

[]()

## Inspección de Bases de Datos

Usando los comandos db `:show` y `db:table` Artisan, puedes obtener información valiosa sobre tu base de datos y sus tablas asociadas. Para ver una visión general de tu base de datos, incluyendo su tamaño, tipo, número de conexiones abiertas y un resumen de sus tablas, puedes utilizar el comando db `:` show:

```shell
php artisan db:show
```

Puede especificar qué conexión de base de datos debe inspeccionarse proporcionando el nombre de la conexión de base de datos al comando mediante la opción `--database`:

```shell
php artisan db:show --database=pgsql
```

Si desea incluir el recuento de filas de las tablas y los detalles de las vistas de la base de datos en la salida del comando, puede proporcionar las opciones `--counts` y `--views`, respectivamente. En bases de datos de gran tamaño, la recuperación de los recuentos de filas y los detalles de las vistas puede resultar lenta:

```shell
php artisan db:show --counts --views
```

[]()

#### Vista general de la tabla

Si desea obtener una visión general de una tabla individual dentro de su base de datos, puede ejecutar el comando `db:table` Artisan. Este comando proporciona una visión general de una tabla de la base de datos, incluyendo sus columnas, tipos, atributos, claves e índices:

```shell
php artisan db:table users
```

[]()

## Monitorización de Bases de Datos

Usando el comando `db:monitor` Artisan, puedes indicar a Laravel que envíe un evento `Illuminate\Database\Events\DatabaseBusy` si tu base de datos está gestionando más de un número especificado de conexiones abiertas.

Para empezar, debes programar el comando `db:monitor` para que [se ejecute cada minuto](/docs/%7B%7Bversion%7D%7D/scheduling). El comando acepta los nombres de las configuraciones de conexión a la base de datos que desea monitorizar, así como el número máximo de conexiones abiertas que debe tolerar antes de enviar un evento:

```shell
php artisan db:monitor --databases=mysql,pgsql --max=100
```

Programar este comando por sí solo no es suficiente para activar una notificación alertando del número de conexiones abiertas. Cuando el comando encuentra una base de datos que tiene un recuento de conexiones abiertas que excede su umbral, se enviará un evento `DatabaseBusy`. Usted debe escuchar este evento dentro del `EventServiceProvider` de su aplicación para enviarle una notificación a usted o a su equipo de desarrollo:

```php
use App\Notifications\DatabaseApproachingMaxConnections;
use Illuminate\Database\Events\DatabaseBusy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

/**
 * Register any other events for your application.
 *
 * @return void
 */
public function boot()
{
    Event::listen(function (DatabaseBusy $event) {
        Notification::route('mail', 'dev@example.com')
                ->notify(new DatabaseApproachingMaxConnections(
                    $event->connectionName,
                    $event->connections
                ));
    });
}
```
