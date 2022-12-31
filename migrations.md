# Base de datos: Migraciones

- [Introducción](#introduction)
- [Generar migraciones](#generating-migrations)
  - [Aplastar migraciones](#squashing-migrations)
- [Estructura de las migraciones](#migration-structure)
- [Ejecución de migraciones](#running-migrations)
  - [Anulación de migraciones](#rolling-back-migrations)
- [Tablas](#tables)
  - [Creación de tablas](#creating-tables)
  - [Actualización de tablas](#updating-tables)
  - [Renombrar / Eliminar Tablas](#renaming-and-dropping-tables)
- [Columnas](#columns)
  - [Creación de columnas](#creating-columns)
  - [Tipos de Columnas Disponibles](#available-column-types)
  - [Modificadores de Columnas](#column-modifiers)
  - [Modificación de Columnas](#modifying-columns)
  - [Renombrar Columnas](#renaming-columns)
  - [Eliminación de columnas](#dropping-columns)
- [Índices](#indexes)
  - [Creación de índices](#creating-indexes)
  - [Cambio de nombre de índices](#renaming-indexes)
  - [Eliminación de índices](#dropping-indexes)
  - [Restricciones de clave foránea](#foreign-key-constraints)
- [Eventos](#events)

[]()

## Introducción

Las migraciones son como el control de versiones de su base de datos, ya que permiten a su equipo definir y compartir la definición del esquema de la base de datos de la aplicación. Si alguna vez ha tenido que decirle a un compañero de equipo que añada manualmente una columna a su esquema de base de datos local después de introducir los cambios desde el control de versiones, se habrá enfrentado al problema que resuelven las migraciones de bases de datos.

La [facade](/docs/%7B%7Bversion%7D%7D/facades) Laravel `Schema` proporciona soporte agnóstico de bases de datos para crear y manipular tablas en todos los sistemas de bases de datos soportados por Laravel. Normalmente, las migraciones utilizarán esta facade para crear y modificar tablas y columnas de bases de datos.

[]()

## Generación de migraciones

Puedes utilizar el [comando](/docs/%7B%7Bversion%7D%7D/artisan) `make:migration` [Artisan](/docs/%7B%7Bversion%7D%7D/artisan) para generar una migración de base de datos. La nueva migración será colocada en tu directorio `database/migrations`. Cada nombre de archivo de migración contiene una marca de tiempo que permite a Laravel determinar el orden de las migraciones:

```shell
php artisan make:migration create_flights_table
```

Laravel utilizará el nombre de la migración para intentar adivinar el nombre de la tabla y si la migración creará o no una nueva tabla. Si Laravel es capaz de determinar el nombre de la tabla a partir del nombre de la migración, Laravel pre-llenará el fichero de migración generado con la tabla especificada. De lo contrario, puede simplemente especificar la tabla en el fichero de migración manualmente.

Si desea especificar una ruta personalizada para la migración generada, puede utilizar la opción `--path` al ejecutar el comando `make:migration`. La ruta indicada debe ser relativa a la ruta base de su aplicación.

> **Nota**  
> stubs migración pueden personalizarse utilizando [stub-customization">la publicación destub](</docs/%7B%7Bversion%7D%7D/artisan#\<glossary variable=>).

[]()

### Eliminación de migraciones

A medida que construyes tu aplicación, puedes acumular más y más migraciones a lo largo del tiempo. Esto puede hacer que su `base de datos/directorio de migraciones` se llene de cientos de migraciones. Si lo desea, puede "aplastar" sus migraciones en un único archivo SQL. Para empezar, ejecute el comando `schema:dump`:

```shell
php artisan schema:dump

# Dump the current database schema and prune all existing migrations...
php artisan schema:dump --prune
```

Cuando ejecutes este comando, Laravel escribirá un archivo "schema" en el directorio `base de datos/schema` de tu aplicación. El nombre del archivo de esquema se corresponderá con la conexión a la base de datos. Ahora, cuando intentes migrar tu base de datos y no se hayan ejecutado otras migraciones, Laravel ejecutará primero las sentencias SQL del fichero de esquema de la conexión de base de datos que estés utilizando. Tras ejecutar las sentencias del fichero de esquema, Laravel ejecutará las migraciones restantes que no formaban parte del volcado de esquema.

Si las tests su aplicación utilizan una conexión de base de datos diferente a la que utiliza normalmente durante el desarrollo local, debe asegurarse de haber volcado un archivo de esquema utilizando esa conexión de base de datos para que sus tests puedan construir su base de datos. Es posible que desee hacer esto después de volcar la conexión de base de datos que suele utilizar durante el desarrollo local:

```shell
php artisan schema:dump
php artisan schema:dump --database=testing --prune
```

Deberías enviar tu archivo de esquema de base de datos al control de código fuente para que otros nuevos desarrolladores de tu equipo puedan crear rápidamente la estructura de base de datos inicial de tu aplicación.

> **Advertencia**  
> El volcado de migración sólo está disponible para las bases de datos MySQL, PostgreSQL y SQLite y utiliza el cliente de línea de comandos de la base de datos. Los volcados de esquema no pueden restaurarse en bases de datos SQLite en memoria.

[]()

## Estructura de las migraciones

Una clase de migración contiene dos métodos: `ascendente` y `descendente`. El método `ascendente` se utiliza para añadir nuevas tablas, columnas o índices a la base de datos, mientras que el método `descendente` debe invertir las operaciones realizadas por el método `ascendente`.

Dentro de estos dos métodos, puedes utilizar el constructor de esquemas de Laravel para crear y modificar tablas de forma expresiva. Para conocer todos los métodos disponibles en el constructor de `esquemas`, [consulta su documentación](#creating-tables). Por ejemplo, la siguiente migración crea una tabla de `vuelos`:

    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('flights', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('airline');
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::drop('flights');
        }
    };

[]()

#### Configuración de la conexión de migración

Si su migración va a interactuar con una conexión de base de datos distinta a la conexión de base de datos por defecto de su aplicación, deberá establecer la propiedad `$connection` de su migración:

    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'pgsql';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    }

[]()

## Ejecución de migraciones

Para ejecutar todas sus migraciones pendientes, ejecute el comando `migrate` Artisan:

```shell
php artisan migrate
```

Si desea ver qué migraciones se han ejecutado hasta el momento, puede utilizar el comando migrate `:status` Artisan:

```shell
php artisan migrate:status
```

Si desea ver las sentencias SQL que serán ejecutadas por las migraciones sin ejecutarlas realmente, puede proporcionar el indicador `--pretend` al comando `migrate`:

```shell
php artisan migrate --pretend
```

#### Aislando la ejecución de la migración

Si está desplegando su aplicación en varios servidores y ejecutando migraciones como parte del proceso de despliegue, es probable que no desee que dos servidores intenten migrar la base de datos al mismo tiempo. Para evitarlo, puede utilizar la opción `aislada` al invocar el comando `migrate`.

Cuando se proporciona la opción `isolated`, Laravel adquirirá un bloqueo atómico utilizando el controlador de cache de tu aplicación antes de intentar ejecutar tus migraciones. Todos los demás intentos de ejecutar el comando `migrate` mientras se mantiene ese bloqueo no se ejecutarán; sin embargo, el comando saldrá con un código de estado de salida correcto:

```shell
php artisan migrate --isolated
```

> **AdvertenciaPara**utilizar esta característica, su aplicación debe estar usando el controlador de cache `memcached`, `redis`, `dynamodb`, `base de datos`, `archivo` o `array` como controlador de cache predeterminado de su aplicación. Además, todos los servidores deben comunicarse con el mismo servidor central de cache.

[]()

#### Forzar la ejecución de migraciones en producción

Algunas operaciones de migración son destructivas, lo que significa que pueden provocar la pérdida de datos. Para evitar que ejecute estos comandos en su base de datos de producción, se le pedirá confirmación antes de ejecutar los comandos. Para forzar la ejecución de los comandos sin que se le pida confirmación, utilice el indicador `--force`:

```shell
php artisan migrate --force
```

[]()

### Deshacer Migraciones

Para revertir la última operación de migración, puede utilizar el comando `rollback` Artisan. Este comando retrocede el último "lote" de migraciones, que puede incluir varios archivos de migración:

```shell
php artisan migrate:rollback
```

Puede revertir un número limitado de migraciones proporcionando la opción `step` al comando `rollback`. Por ejemplo, el siguiente comando revertirá las últimas cinco migraciones:

```shell
php artisan migrate:rollback --step=5
```

El comando `migrate:` reset revertirá todas las migraciones de su aplicación:

```shell
php artisan migrate:reset
```

[]()

#### Retroceder y migrar con un solo comando

El comando migrate `:refresh` revertirá todas las migraciones y, a continuación, ejecutará el comando `migrate`. Este comando vuelve a crear toda la base de datos:

```shell
php artisan migrate:refresh

# Refresh the database and run all database seeds...
php artisan migrate:refresh --seed
```

Puede hacer retroceder y volver a migrar un número limitado de migraciones proporcionando la opción `step` al comando `refresh`. Por ejemplo, el siguiente comando revertirá y volverá a migrar las últimas cinco migraciones:

```shell
php artisan migrate:refresh --step=5
```

[]()

#### Eliminar todas las tablas y migrar

El comando `migrate`:fresh eliminará todas las tablas de la base de datos y, a continuación, ejecutará el comando `migrate`:

```shell
php artisan migrate:fresh

php artisan migrate:fresh --seed
```

> **Advertencia**  
> El comando `migrate`:fresh eliminará todas las tablas de la base de datos independientemente de su prefijo. Este comando debe utilizarse con precaución cuando se desarrolle en una base de datos compartida con otras aplicaciones.

[]()

## Tablas

[]()

### Creación de tablas

Para crear una nueva tabla de base de datos, utilice el método `create` en la facade `Schema`. El método `create` acepta dos argumentos: el primero es el nombre de la tabla, mientras que el segundo es un closure que recibe un objeto `Blueprint` que puede ser utilizado para definir la nueva tabla:

    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->timestamps();
    });

Al crear la tabla, puede utilizar cualquiera de los [métodos de columna](#creating-columns) del constructor de esquemas para definir las columnas de la tabla.

[]()

#### Comprobación de Existencia de Tabla / Columna

Puede comprobar la existencia de una tabla o columna utilizando los métodos `hasTable` y `hasColumn`:

    if (Schema::hasTable('users')) {
        // The "users" table exists...
    }

    if (Schema::hasColumn('users', 'email')) {
        // The "users" table exists and has an "email" column...
    }

[]()

#### Conexión a la base de datos y opciones de tabla

Si desea realizar una operación de esquema en una conexión de base de datos que no es la conexión por defecto de su aplicación, utilice el método `connection`:

    Schema::connection('sqlite')->create('users', function (Blueprint $table) {
        $table->id();
    });

Además, se pueden utilizar otras propiedades y métodos para definir otros aspectos de la creación de la tabla. La propiedad `engine` puede usarse para especificar el motor de almacenamiento de la tabla cuando se usa MySQL:

    Schema::create('users', function (Blueprint $table) {
        $table->engine = 'InnoDB';

        // ...
    });

Las propiedades `charset` y `collation` pueden usarse para especificar el conjunto de caracteres y collation para la tabla creada cuando se usa MySQL:

    Schema::create('users', function (Blueprint $table) {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_unicode_ci';

        // ...
    });

El método `temporal` puede utilizarse para indicar que la tabla debe ser "temporal". Las tablas temporales sólo son visibles para la sesión de base de datos de la conexión actual y se eliminan automáticamente cuando se cierra la conexión:

    Schema::create('calculations', function (Blueprint $table) {
        $table->temporary();

        // ...
    });

Si desea añadir un "comentario" a una tabla de base de datos, puede invocar el método `comment` en la instancia de la tabla. Actualmente, los comentarios de tablas sólo están soportados por MySQL y Postgres:

    Schema::create('calculations', function (Blueprint $table) {
        $table->comment('Business calculations');

        // ...
    });

[]()

### Actualización de tablas

El método `table` de la facade `Schema` puede utilizarse para actualizar tablas existentes. Al igual que el método `create`, el método `table` acepta dos argumentos: el nombre de la tabla y un closure que recibe una instancia de `Blueprint` que se puede utilizar para añadir columnas o índices a la tabla:

    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    Schema::table('users', function (Blueprint $table) {
        $table->integer('votes');
    });

[]()

### Renombrar / Eliminar Tablas

Para renombrar una tabla de base de datos existente, utilice el método `renombrar`:

    use Illuminate\Support\Facades\Schema;

    Schema::rename($from, $to);

Para eliminar una tabla existente, puede utilizar los métodos `drop` o `dropIfExists`:

    Schema::drop('users');

    Schema::dropIfExists('users');

[]()

#### Cambio de nombre de tablas con claves externas

Antes de renombrar una tabla, debes verificar que cualquier restricción de clave foránea en la tabla tenga un nombre explícito en tus archivos de migración en lugar de dejar que Laravel asigne un nombre basado en convenciones. De lo contrario, el nombre de la restricción de clave foránea hará referencia al nombre antiguo de la tabla.

[]()

## Columnas

[]()

### Creación de Columnas

El método `table` de la facade `Schema` puede utilizarse para actualizar tablas existentes. Al igual que el método `create`, el método `table` acepta dos argumentos: el nombre de la tabla y un closure que recibe una instancia `Illuminate\Database\Schema\Blueprint` que puede utilizar para añadir columnas a la tabla:

    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    Schema::table('users', function (Blueprint $table) {
        $table->integer('votes');
    });

[]()

### Tipos de Columnas Disponibles

El schema builder blueprint ofrece una variedad de métodos que corresponden a los diferentes tipos de columnas que puedes añadir a las tablas de tu base de datos. Cada uno de los métodos disponibles se enumeran en la tabla siguiente:

<style>
    .collection-method-list &gt; p {
        columnas: 10.8em 3; -moz-columns: 10.8em 3; -webkit-columns: 10.8em 3;
    }

    .collection-method-list a {
        display: block;
        overflow: oculto;
        text-overflow: ellipsis;
        espacio en blanco: nowrap;
    }

    .collection-method code {
        font-size: 14px;
    }

    .collection-method:not(.first-collection-method) {
        margin-top: 50px;
    }
</style>

<div class="collection-method-list" markdown="1"/>

[bigIncrementsbigIntegerbinarybooleanchardateTimeTzdateTimedatedecimaldoubleenumfloatforeignIdforeignIdForforeignUlidforeignUuidgeometryCollectiongeometryidincrementsintegeripAddressjsonjsonblineStringlongTextmacAddressmediumIncrementsmediumIntegermediumTextmorphsmultiLineStringmultiPointmultiPolygonnullableMorphsnullableTimestampsnullableUlidMorphsnullableUuidMorphspointpolygonrememberTokensetsmallIncrementssmallIntegersoftDeletesTzsoftDeletesstringtexttimeTztimetimestampTztimestamptimestampsTztimestampstinyIncrementstinyIntegertinyTextunsignedBigIntegerunsignedDecimalunsignedIntegerunsignedMediumIntegerunsignedSmallIntegerunsignedTinyIntegerulidMorphsuuidMorphsuliduuidyear](#column-method-year)

[object Object]

[]()

#### `bigIncrements()` {.método-colección .método-primera-colección}

El método `bigIncrements` crea una columna equivalente `UNSIGNED BIGINT` (clave primaria) autoincrementable:

    $table->bigIncrements('id');

[]()

#### `bigInteger()` {.método-colección}

El método `bigInteger` crea una columna equivalente `BIGINT`:

    $table->bigInteger('votes');

[]()

#### binary(`)` {.método-colección}

El método `binary` crea una columna equivalente `BLOB`:

    $table->binary('photo');

[]()

#### `boolean()` {.método-colección}

El método `boolean` crea una columna equivalente a `BOOLEAN`:

    $table->boolean('confirmed');

[]()

#### char(`)` {.método-colección}

El método `char` crea una columna equivalente a `CHAR` con una longitud dada:

    $table->char('name', 100);

[]()

#### `dateTimeTz()` {.método-colección}

El método `dateTimeTz` crea una columna equivalente `DATETIME` (con zona horaria) con una precisión opcional (total de dígitos):

    $table->dateTimeTz('created_at', $precision = 0);

[]()

#### `dateTime()` {.collection-method}

El método `dateTime` crea una columna equivalente a `DATETIME` con una precisión opcional (total de dígitos):

    $table->dateTime('created_at', $precision = 0);

[]()

#### date(`)` {.método-colección}

El método `date` crea una columna equivalente a `DATE`:

    $table->date('created_at');

[]()

#### `decimal()` {.método-colección}

El método `decimal` crea una columna equivalente a `DECIMAL` con la precisión (dígitos totales) y escala (dígitos decimales) dadas:

    $table->decimal('amount', $precision = 8, $scale = 2);

[]()

#### `double()` {.método-colección}

El método `double` crea una columna equivalente a `DOUBLE` con la precisión (dígitos totales) y escala (dígitos decimales) dadas:

    $table->double('amount', 8, 2);

[]()

#### `enum()` {.método-colección}

El método `enum` crea una columna equivalente a `ENUM` con los valores válidos dados:

    $table->enum('difficulty', ['easy', 'hard']);

[]()

#### `float()` {.método-colección}

El método `float` crea una columna equivalente a `FLOAT` con la precisión (dígitos totales) y escala (dígitos decimales) dadas:

    $table->float('amount', 8, 2);

[]()

#### `foreignId()` {.método-colección}

El método `foreignId` crea una columna equivalente `UNSIGNED BIGINT`:

    $table->foreignId('user_id');

[]()

#### `foreignIdFor()` {.método-colección}

El método `foreignIdFor` añade una columna equivalente `{column}_id UNSIGNED BIGINT` para una clase de modelo dada:

    $table->foreignIdFor(User::class);

[]()

#### `foreignUlid()` {.método-colección}

El método `foreignUlid` crea una columna equivalente `ULID`:

    $table->foreignUlid('user_id');

[]()

#### foreignUuid(`)` {.método-colección}

El método `foreignUuid` crea una columna equivalente a `UUID`:

    $table->foreignUuid('user_id');

[]()

#### `geometryCollection()` {.método-colección}

El método `geometryCollection` crea una columna equivalente a `GEOMETRYCOLLECTION`:

    $table->geometryCollection('positions');

[]()

#### geometry(`)` {.método-colección}

El método `geometry` crea una columna equivalente a `GEOMETRY`:

    $table->geometry('positions');

[]()

#### id(`)` {.método-colección}

El método `id` es un alias del método `bigIncrements`. Por defecto, el método creará una columna `id`; sin embargo, puede pasar un nombre de columna si desea asignar un nombre diferente a la columna:

    $table->id();

[]()

#### incrementos`()` {.método-colección}

El método `increments` crea una columna equivalente `UNSIGNED INTEGER` autoincrementable como clave primaria:

    $table->increments('id');

[]()

#### `integer()` {.collection-method}

El método `integer` crea una columna equivalente a `INTEGER`:

    $table->integer('votes');

[]()

#### `ipAddress()` {.método-colección}

El método `ipAddress` crea una columna equivalente `VARCHAR`:

    $table->ipAddress('visitor');

[]()

#### `json()` {.método-colección}

El método `json` crea una columna equivalente `JSON`:

    $table->json('options');

[]()

#### `jsonb()` {.método-colección}

El método `jsonb` crea una columna equivalente `JSONB`:

    $table->jsonb('options');

[]()

#### `lineString()` {.método-colección}

El método `lineString` crea una columna equivalente a `LINESTRING`:

    $table->lineString('positions');

[]()

#### `longText()` {.método-colección}

El método `longText` crea una columna equivalente a `LONGTEXT`:

    $table->longText('description');

[]()

#### `macAddress()` {.método-colección}

El método `macAddress` crea una columna destinada a contener una dirección MAC. Algunos sistemas de bases de datos, como PostgreSQL, tienen un tipo de columna dedicado para este tipo de datos. Otros sistemas de bases de datos utilizarán una columna equivalente a una cadena:

    $table->macAddress('device');

[]()

#### `mediumIncrements()` {.collection-method}

El método `mediumIncrements` crea una columna equivalente `UNSIGNED MEDIUMINT` auto-incrementada como clave primaria:

    $table->mediumIncrements('id');

[]()

#### `mediumInteger()` {.método-colección}

El método `mediumInteger` crea una columna equivalente a `MEDIUMINT`:

    $table->mediumInteger('votes');

[]()

#### `mediumText()` {.método-colección}

El método `mediumText` crea una columna equivalente a `MEDIUMTEXT`:

    $table->mediumText('description');

[]()

#### morphs(`)` {.método-colección}

El método `morphs` es un método conveniente que añade una columna equivalente `{column}_id` `UNSIGNED BIGINT` y una columna equivalente `{column}_type` `VARCHAR`.

Este método se utiliza para definir las columnas necesarias para una [relación Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent-relationships) polimórfica. En el siguiente ejemplo, se crearían las columnas `taggable_id` y `taggable_type`:

    $table->morphs('taggable');

[]()

#### `multiLineString()` {.método-colección}

El método `multiLineString` crea una columna equivalente `MULTILINETRING`:

    $table->multiLineString('positions');

[]()

#### `multiPoint()` {.método-colección}

El método `multiPoint` crea una columna equivalente a `MULTIPOINT`:

    $table->multiPoint('positions');

[]()

#### `multiPolygon()` {.método-colección}

El método `multiPolygon` crea una columna equivalente `MULTIPOLYGON`:

    $table->multiPolygon('positions');

[]()

#### `nullableTimestamps()` {.método-colección}

El método `nullableTimestamps` es un alias del método [timestamps](#column-method-timestamps):

    $table->nullableTimestamps(0);

[]()

#### `nullableMorphs` () {.método-colección}

El método es similar al método [morphs](#column-method-morphs); sin embargo, las columnas que se creen serán "nullable":

    $table->nullableMorphs('taggable');

[]()

#### `nullableUlidMorphs` () {.método-colección}

El método es similar al método [ulidMorphs](#column-method-ulidMorphs); sin embargo, las columnas que se creen serán "nullable":

    $table->nullableUlidMorphs('taggable');

[]()

#### `nullableUuidMorphs` () {.método-colección}

El método es similar al método [uuidMorphs](#column-method-uuidMorphs); sin embargo, las columnas que se creen serán "nullable":

    $table->nullableUuidMorphs('taggable');

[]()

#### point(`)` {.método-colección}

El método `point` crea una columna equivalente a `POINT`:

    $table->point('position');

[]()

#### `polygon()` {.método-colección}

El método `polygon` crea una columna equivalente a `POLYGON`:

    $table->polygon('position');

[]()

#### `rememberToken()` {.método-colección}

El método `rememberToken` crea una columna nulable, equivalente a `VARCHAR(100)`, destinada a almacenar el [token de autenticación](/docs/%7B%7Bversion%7D%7D/authentication#remembering-users)"remember me" actual:

    $table->rememberToken();

[]()

#### set(`)` {.método-colección}

El método `set` crea una columna equivalente a `SET` con la lista dada de valores válidos:

    $table->set('flavors', ['strawberry', 'vanilla']);

[]()

#### `smallIncrements()` {.método-colección}

El método `smallIncrements` crea una columna equivalente `UNSIGNED SMALLINT` autoincrementada como clave primaria:

    $table->smallIncrements('id');

[]()

#### `smallInteger()` {.método-colección}

El método `smallInteger` crea una columna equivalente `SMALLINT`:

    $table->smallInteger('votes');

[]()

#### `softDeletesTz()` {.método-colección}

El método `softDeletesTz` añade una columna nulable equivalente a `deleted_at` `TIMESTAMP` (con zona horaria) con una precisión opcional (dígitos totales). Esta columna está pensada para almacenar la marca de tiempo `deleted_at` necesaria para la funcionalidad "soft delete" de Eloquent:

    $table->softDeletesTz($column = 'deleted_at', $precision = 0);

[]()

#### `softDeletes()` {.método-colección}

El método `softDeletes` añade una columna nulable equivalente a `deleted_at` `TIMESTAMP` con una precisión opcional (dígitos totales). Esta columna está pensada para almacenar la marca de tiempo `deleted_at` necesaria para la funcionalidad "soft delete" de Eloquent:

    $table->softDeletes($column = 'deleted_at', $precision = 0);

[]()

#### `string()` {.método-colección}

El método `string` crea una columna equivalente `VARCHAR` de la longitud dada:

    $table->string('name', 100);

[]()

#### text(`)` {.método-colección}

El método `text` crea una columna equivalente a `TEXT`:

    $table->text('description');

[]()

#### `timeTz()` {.método-colección}

El método `timeTz` crea una columna equivalente a `TIME` (con zona horaria) con una precisión opcional (total de dígitos):

    $table->timeTz('sunrise', $precision = 0);

[]()

#### time(`)` {.método-colección}

El método `time` crea una columna equivalente a `TIME` con una precisión opcional (total de dígitos):

    $table->time('sunrise', $precision = 0);

[]()

#### `timestampTz()` {.método-colección}

El método `timestampTz` crea una columna equivalente a `TIMESTAMP` (con zona horaria) con una precisión opcional (total de dígitos):

    $table->timestampTz('added_at', $precision = 0);

[]()

#### `timestamp()` {.método-colección}

El método `timestamp` crea una columna equivalente a `TIMESTAMP` con una precisión opcional (total de dígitos):

    $table->timestamp('added_at', $precision = 0);

[]()

#### `timestampsTz()` {.método-colección}

El método `timestampsTz` crea las columnas `created_at` y `updated_at` equivalentes a `TIMESTAMP` (con zona horaria) con una precisión opcional (total de dígitos):

    $table->timestampsTz($precision = 0);

[]()

#### timestamps`()` {.método-colección}

El método `timestamps` crea columnas equivalentes a `created_at` y `updated_at` `TIMESTAMP` con una precisión opcional (total de dígitos):

    $table->timestamps($precision = 0);

[]()

#### `tinyIncrements()` {.método-colección}

El método `tinyIncrements` crea una columna equivalente `UNSIGNED TINYINT` autoincrementada como clave primaria:

    $table->tinyIncrements('id');

[]()

#### `tinyInteger()` {.método-colección}

El método `tinyInteger` crea una columna equivalente `TINYINT`:

    $table->tinyInteger('votes');

[]()

#### `tinyText()` {.método-colección}

El método `tinyText` crea una columna equivalente `TINYTEXT`:

    $table->tinyText('notes');

[]()

#### `unsignedBigInteger()` {.método-colección}

El método `unsignedBigInteger` crea una columna equivalente a `UNSIGNED BIGINT`:

    $table->unsignedBigInteger('votes');

[]()

#### `unsignedDecimal()` {.método-colección}

El método `unsignedDecimal` crea una columna equivalente `DECIMAL` SIN SIGNO con una precisión (dígitos totales) y una escala (dígitos decimales) opcionales:

    $table->unsignedDecimal('amount', $precision = 8, $scale = 2);

[]()

#### `unsignedInteger()` {.método-colección}

El método `unsignedInteger` crea una columna equivalente a UNSIGNED `INTEGER`:

    $table->unsignedInteger('votes');

[]()

#### `unsignedMediumInteger()` {.método-colección}

El método `unsignedMediumInteger` crea una columna equivalente UNSIGNED `MEDIUMINT`:

    $table->unsignedMediumInteger('votes');

[]()

#### `unsignedSmallInteger()` {.método-colección}

El método `unsignedSmallInteger` crea una columna equivalente UNSIGNED `SMALLINT`:

    $table->unsignedSmallInteger('votes');

[]()

#### `unsignedTinyInteger()` {.método-colección}

El método `unsignedTinyInteger` crea una columna equivalente `UNSIGNED TINYINT`:

    $table->unsignedTinyInteger('votes');

[]()

#### `ulidMorphs()` {.método-colección}

El método `ulidMorphs` es un método conveniente que añade una columna equivalente `{column}_id` `CHAR(26)` y una columna equivalente `{column}_type` `VARCHAR`.

Este método debe utilizarse al definir las columnas necesarias para una [relación](/docs/%7B%7Bversion%7D%7D/eloquent-relationships) polimórfica de [Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent-relationships) que utilice identificadores ULID. En el siguiente ejemplo, se crearían las columnas `taggable_id` y `taggable_type`:

    $table->ulidMorphs('taggable');

[]()

#### `uuidMorphs()` {.método-colección}

El método `uuidMorphs` es un método conveniente que añade una columna equivalente a `{column}_id` `CHAR(` 36) y una columna equivalente a `{column}_type` `VARCHAR`.

Este método está pensado para utilizarse cuando se definen las columnas necesarias para una relación [Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent-relationships) polimórfica que utiliza identificadores UUID. En el siguiente ejemplo, se crearían las columnas `taggable_id` y `taggable_type`:

    $table->uuidMorphs('taggable');

[]()

#### ulid`()` {.método-colección}

El método `ulid` crea una columna equivalente a `ULID`:

    $table->ulid('id');

[]()

#### uuid(`)` {.método-colección}

El método `uuid` crea una columna equivalente `UUID`:

    $table->uuid('id');

[]()

#### year(`)` {.método-colección}

El método `year` crea una columna equivalente a `YEAR`:

    $table->year('birth_year');

[]()

### Modificadores de columnas

Además de los tipos de columna enumerados anteriormente, existen varios "modificadores" de columna que puede utilizar al añadir una columna a una tabla de base de datos. Por ejemplo, para hacer que la columna sea "anulable", puede utilizar el método `nullable`:

    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    Schema::table('users', function (Blueprint $table) {
        $table->string('email')->nullable();
    });

La siguiente tabla contiene todos los modificadores de columna disponibles. Esta lista no incluye los modificadores [de índice](#creating-indexes):

|Modificador                        |Descripción                                                                                                   |
|-----------------------------------|--------------------------------------------------------------------------------------------------------------|
|`->after('column')`                |Colocar la columna "después" de otra columna (MySQL).                                                         |
|`->autoIncrement()`                |Establecer columnas INTEGER como autoincrementables (clave primaria).                                         |
|`->charset('utf8mb4')`             |Especifique un conjunto de caracteres para la columna (MySQL).                                                |
|`->collation('utf8mb4_unicode_ci')`|Especificar una intercalación para la columna (MySQL/PostgreSQL/SQL Server).                                  |
|`->comment('my comment')`          |Añadir un comentario a una columna (MySQL/PostgreSQL).                                                        |
|`->default($value)`                |Especificar un valor "por defecto" para la columna.                                                           |
|`->first()`                        |Colocar la columna "primero" en la tabla (MySQL).                                                             |
|`->from($integer)`                 |Establecer el valor inicial de un campo autoincrementable (MySQL / PostgreSQL).                               |
|`->invisible()`                    |Hacer la columna "invisible" para `SELECT *` consultas (MySQL).                                               |
|`->nullable($value = true)`        |Permitir la inserción de valores NULL en la columna.                                                          |
|`->storedAs($expression)`          |Crear una columna generada almacenada (MySQL / PostgreSQL).                                                   |
|`->unsigned()`                     |Configurar columnas INTEGER como UNSIGNED (MySQL).                                                            |
|`->useCurrent()`                   |Establecer columnas TIMESTAMP para usar CURRENT_TIMESTAMP como valor por defecto.                             |
|`->useCurrentOnUpdate()`           |Configurar las columnas TIMESTAMP para que utilicen CURRENT_TIMESTAMP cuando se actualiza un registro.        |
|`->virtualAs($expression)`         |Crear una columna virtual generada (MySQL).                                                                   |
|`->generatedAs($expression)`       |Crear una columna de identidad con las opciones de secuencia especificadas (PostgreSQL).                      |
|`->always()`                       |Define la precedencia de los valores de secuencia sobre la entrada para una columna de identidad (PostgreSQL).|
|`->isGeometry()`                   |Establece el tipo de columna espacial a `geometry` - el tipo por defecto es `geography` (PostgreSQL).         |

[]()

#### Expresiones por defecto

El modificador por `defecto` acepta un valor o una instancia `Illuminate\Database\Query\Expression`. El uso de una instancia de `Expression` evitará que Laravel envuelva el valor entre comillas y le permitirá utilizar funciones específicas de la base de datos. Una situación en la que esto es particularmente útil es cuando se necesita asignar valores por defecto a las columnas JSON:

    <?php

    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Query\Expression;
    use Illuminate\Database\Migrations\Migration;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('flights', function (Blueprint $table) {
                $table->id();
                $table->json('movies')->default(new Expression('(JSON_ARRAY())'));
                $table->timestamps();
            });
        }
    };

> **Advertencia**  
> La compatibilidad con expresiones por defecto depende del controlador de la base de datos, de la versión de la base de datos y del tipo de campo. Consulte la documentación de su base de datos. Además, no es posible combinar expresiones por `defecto` sin procesar (utilizando `DB::raw`) con cambios de columna a través del método `change`.

[]()

#### Orden de columnas

Cuando se utiliza la base de datos MySQL, se puede utilizar el método `after` para añadir columnas después de una columna existente en el esquema:

    $table->after('password', function ($table) {
        $table->string('address_line1');
        $table->string('address_line2');
        $table->string('city');
    });

[]()

### Modificación de columnas

[]()

#### Requisitos previos

Antes de modificar una columna, debe instalar el paquete `doctrine/dbal` mediante el gestor de paquetes Composer. La biblioteca DBAL de Doctrine se utiliza para determinar el estado actual de la columna y crear las consultas SQL necesarias para realizar los cambios solicitados en la columna:

    composer require doctrine/dbal

Si planea modificar columnas creadas utilizando el método `de marca de tiempo`, también debe agregar la siguiente configuración al archivo de configuración `config/database.php` de su aplicación:

```php
use Illuminate\Database\DBAL\TimestampType;

'dbal' => [
    'types' => [
        'timestamp' => TimestampType::class,
    ],
],
```

> **Advertencia**  
> Si su aplicación utiliza Microsoft SQL Server, asegúrese de instalar `doctrine/dbal:^3.0`.

[]()

#### Actualización de atributos de columna

El método de `modificación` le permite modificar el tipo y los atributos de las columnas existentes. Por ejemplo, puede que desee aumentar el tamaño de una columna `de cadena`. Para ver el método `change` en acción, aumentemos el tamaño de la columna `name` de 25 a 50. Para lograrlo, simplemente definimos el nuevo estado de la columna y luego llamamos al método `change`:

    Schema::table('users', function (Blueprint $table) {
        $table->string('name', 50)->change();
    });

También podemos modificar una columna para que sea anulable:

    Schema::table('users', function (Blueprint $table) {
        $table->string('name', 50)->nullable()->change();
    });

> **Advertencia**  
> Los siguientes tipos de columna pueden ser modificados: `bigInteger`, `binary`, `boolean`, `char`, `date`, `dateTime`, `dateTimeTz`, `decimal`, `double`, `integer`, `json`, `longText`, `mediumText`, `smallInteger`, `string`, `text`, `time`, `tinyText`, `unsignedBigInteger`, `unsignedInteger`, `unsignedSmallInteger`, y `uuid`. Para modificar un tipo de columna `timestamp` se [debe registrar un tipo Doctrine](#prerequisites).

[]()

### Renombrar Columnas

Para renombrar una columna, puedes utilizar el método `renameColumn` proporcionado por el constructor de esquemas:

    Schema::table('users', function (Blueprint $table) {
        $table->renameColumn('from', 'to');
    });

[]()

#### Renombrar columnas en bases de datos heredadas

Si está ejecutando una instalación de base de datos anterior a una de las siguientes versiones, debe asegurarse de que ha instalado la biblioteca `doctrine/dbal` a través del gestor de paquetes Composer antes de renombrar una columna:

<div class="content-list" markdown="1"/>

- MySQL < `8.0.3`
- MariaDB < 10 `.5.`2
- SQLite < 3 `.25.`0

[object Object]

[]()

### Eliminación de columnas

Para eliminar una columna, puede utilizar el método `dropColumn` en el constructor de esquemas:

    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('votes');
    });

Puede eliminar varias columnas de una tabla pasando una array de nombres de columnas al método `dropColumn`:

    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['votes', 'avatar', 'location']);
    });

[]()

#### Eliminación de columnas en bases de datos heredadas

Si utiliza una versión de SQLite anterior a la `3.35.`0, deberá instalar el paquete `doctrine/dbal` a través del gestor de paquetes Composer antes de poder utilizar el método `dropColumn`. No es posible eliminar o modificar varias columnas en una misma migración utilizando este paquete.

[]()

#### Alias de comandos disponibles

Laravel proporciona varios métodos prácticos para eliminar tipos comunes de columnas. Cada uno de estos métodos se describe en la siguiente tabla:

|Comando                           |Descripción                                          |
|----------------------------------|-----------------------------------------------------|
|`$table->dropMorphs('morphable');`|Soltar la `morphable_id` y `morphable_type` columnas.|
|`$table->dropRememberToken();`    |Drop the `remember_token` columna.                   |
|`$table->dropSoftDeletes();`      |Drop the `deleted_at` columna.                       |
|`$table->dropSoftDeletesTz();`    |Alias de `dropSoftDeletes()` método.                 |
|`$table->dropTimestamps();`       |Eliminar `created_at` y `updated_at` columnas.       |
|`$table->dropTimestampsTz();`     |Alias de `dropTimestamps()` método.                  |

[]()

## Índices

[]()

### Creación de índices

El constructor de esquemas de Laravel soporta varios tipos de índices. El siguiente ejemplo crea una nueva columna `email` y especifica que sus valores deben ser únicos. Para crear el índice, podemos encadenar el método `unique` en la definición de la columna:

    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    Schema::table('users', function (Blueprint $table) {
        $table->string('email')->unique();
    });

Alternativamente, puede crear el índice después de definir la columna. Para ello, debes llamar al método `unique` en el plano del constructor de esquemas. Este método acepta el nombre de la columna que debe recibir un índice único:

    $table->unique('email');

Incluso puedes pasar un array de columnas a un método de índice para crear un índice compuesto:

    $table->index(['account_id', 'created_at']);

Al crear un índice, Laravel generará automáticamente un nombre de índice basado en la tabla, los nombres de las columnas y el tipo de índice, pero puedes pasar un segundo argumento al método para especificar tú mismo el nombre del índice:

    $table->unique('email', 'unique_email');

[]()

#### Tipos de índice disponibles

La clase schema builder blueprint de Laravel proporciona métodos para crear cada tipo de índice soportado por Laravel. Cada método de índice acepta un segundo argumento opcional para especificar el nombre del índice. Si se omite, el nombre se derivará de los nombres de la tabla y columna(s) utilizadas para el índice, así como del tipo de índice. Cada uno de los métodos de índice disponibles se describe en la tabla siguiente:

|Comando                                         |Descripción                                                            |
|------------------------------------------------|-----------------------------------------------------------------------|
|`$table->primary('id');`                        |Añade una clave primaria.                                              |
|`$table->primary(['id', 'parent_id']);`         |Añade claves compuestas.                                               |
|`$table->unique('email');`                      |Añade un índice único.                                                 |
|`$table->index('state');`                       |Añade un índice.                                                       |
|`$table->fullText('body');`                     |Añade un índice de texto completo (MySQL/PostgreSQL).                  |
|`$table->fullText('body')->language('english');`|Añade un índice de texto completo del idioma especificado (PostgreSQL).|
|`$table->spatialIndex('location');`             |Añade un índice espacial (excepto SQLite).                             |

[]()

#### Longitudes de índice y MySQL / MariaDB

Por defecto, Laravel utiliza el conjunto de caracteres `utf8mb4`. Si estás ejecutando una versión de MySQL anterior a la 5.7.7 o MariaDB anterior a la 10.2.2, puede que necesites configurar manualmente la longitud de cadena por defecto generada por las migraciones para que MySQL cree índices para ellas. Puede configurar la longitud de cadena por defecto llamando al método `Schema::defaultStringLength` dentro del método `boot` de su clase `AppProviders\AppServiceProvider`:

    use Illuminate\Support\Facades\Schema;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

Alternativamente, puede activar la opción `innodb_large_prefix` para su base de datos. Consulte la documentación de su base de datos para obtener instrucciones sobre cómo activar correctamente esta opción.

[]()

### Cambio de nombre de índices

Para renombrar un índice, puede utilizar el método `renameIndex` proporcionado por el schema builder blueprint. Este método acepta el nombre actual del índice como primer argumento y el nombre deseado como segundo argumento:

    $table->renameIndex('from', 'to')

> **Advertencia**  
> Si tu aplicación utiliza una base de datos SQLite, debes instalar el paquete `doctrine/dbal` a través del gestor de paquetes Composer antes de poder utilizar el método `renameIndex`.

[]()

### Eliminación de índices

Para eliminar un índice, debes especificar el nombre del índice. Por defecto, Laravel asigna automáticamente un nombre de índice basado en el nombre de la tabla, el nombre de la columna indexada y el tipo de índice. He aquí algunos ejemplos:

|Comando                                                 |Descripción                                                    |
|--------------------------------------------------------|---------------------------------------------------------------|
|`$table->dropPrimary('users_id_primary');`              |Elimine una clave primaria de la tabla "users".                |
|`$table->dropUnique('users_email_unique');`             |Elimine un índice único de la tabla "users".                   |
|`$table->dropIndex('geo_state_index');`                 |Elimine un índice básico de la tabla "geo".                    |
|`$table->dropFullText('posts_body_fulltext');`          |Eliminar un índice de texto completo de la tabla "posts".      |
|`$table->dropSpatialIndex('geo_location_spatialindex');`|Eliminar un índice espacial de la tabla "geo" (excepto SQLite).|

Si pasas un array de columnas a un método que suelta índices, el nombre del índice convencional se generará basándose en el nombre de la tabla, las columnas y el tipo de índice:

    Schema::table('geo', function (Blueprint $table) {
        $table->dropIndex(['state']); // Drops index 'geo_state_index'
    });

[]()

### Restricciones de clave foránea

Laravel también proporciona soporte para crear restricciones de clave foránea, que se utilizan para forzar la integridad referencial a nivel de base de datos. Por ejemplo, definamos una columna `user_id` en la tabla `posts` que haga referencia a la columna `id` de una tabla `users`:

    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    Schema::table('posts', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id');

        $table->foreign('user_id')->references('id')->on('users');
    });

Dado que esta sintaxis es bastante verbosa, Laravel proporciona métodos adicionales, más tersos, que utilizan convenciones para proporcionar una mejor experiencia al desarrollador. Cuando se utiliza el método `foreignId` para crear su columna, el ejemplo anterior puede ser reescrito así:

    Schema::table('posts', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained();
    });

El método `foreignId` crea una columna equivalente `UNSIGNED BIGINT`, mientras que el método `constrained` utilizará convenciones para determinar la tabla y el nombre de la columna a la que se hace referencia. Si el nombre de tu tabla no coincide con las convenciones de Laravel, puedes especificar el nombre de la tabla pasándolo como argumento al método `constrained`:

    Schema::table('posts', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained('users');
    });

También puedes especificar la acción deseada para las propiedades "on delete" y "on update" de la restricción:

    $table->foreignId('user_id')
          ->constrained()
          ->onUpdate('cascade')
          ->onDelete('cascade');

También se proporciona una sintaxis alternativa y expresiva para estas acciones:

|Método                       |Descripción                                                        |
|-----------------------------|-------------------------------------------------------------------|
|`$table->cascadeOnUpdate();` |Las actualizaciones deben realizarse en cascada.                   |
|`$table->restrictOnUpdate();`|Las actualizaciones deben restringirse.                            |
|`$table->cascadeOnDelete();` |Los borrados deben realizarse en cascada.                          |
|`$table->restrictOnDelete();`|Los borrados deben ser restringidos.                               |
|`$table->nullOnDelete();`    |Los borrados deben establecer el valor de la clave foránea en null.|

Cualquier [modificador de columna](#column-modifiers) adicional debe invocarse antes del método de `restricción`:

    $table->foreignId('user_id')
          ->nullable()
          ->constrained();

[]()

#### Eliminación de claves externas

Para eliminar una clave externa, puede utilizar el método `dropForeign`, pasando el nombre de la restricción de clave externa que se va a eliminar como argumento. Las restricciones de clave externa utilizan la misma convención de nomenclatura que los índices. En otras palabras, el nombre de la restricción de clave foránea se basa en el nombre de la tabla y las columnas de la restricción, seguido de un sufijo "\_foreign":

    $table->dropForeign('posts_user_id_foreign');

Alternativamente, puede pasar un array que contenga el nombre de la columna que contiene la clave foránea al método `dropForeign`. El array se convertirá en un nombre de restricción de clave foránea utilizando las convenciones de Laravel:

    $table->dropForeign(['user_id']);

[]()

#### Activación de Restricciones de Clave Foránea

Puede activar o desactivar las restricciones de clave foránea en sus migraciones utilizando los siguientes métodos:

    Schema::enableForeignKeyConstraints();

    Schema::disableForeignKeyConstraints();

> **Advertencia**  
> SQLite desactiva las restricciones de clave externa por defecto. Cuando utilice SQLite, asegúrese de [habilitar el soporte de](/docs/%7B%7Bversion%7D%7D/database#configuration) claves externas en la configuración de su base de datos antes de intentar crearlas en sus migraciones. Además, SQLite sólo soporta claves foráneas en el momento de la creación de la tabla y [no cuando las tablas son alteradas](https://www.sqlite.org/omitted.html).

[]()

## Eventos

Para mayor comodidad, cada operación de migración enviará un [evento](/docs/%7B%7Bversion%7D%7D/events). Todos los eventos siguientes extienden la clase base `Illuminate\Database\Events\MigrationEvent`:

|Clase                                         |Descripción                                                    |
|----------------------------------------------|---------------------------------------------------------------|
|`Illuminate\Database\Events\MigrationsStarted`|Un lote de migraciones está a punto de ejecutarse.             |
|`Illuminate\Database\Events\MigrationsEnded`  |Un lote de migraciones ha terminado de ejecutarse.             |
|`Illuminate\Database\Events\MigrationStarted` |Una única migración está a punto de ejecutarse.                |
|`Illuminate\Database\Events\MigrationEnded`   |Ha finalizado la ejecución de una única migración.             |
|`Illuminate\Database\Events\SchemaDumped`     |Se ha completado un volcado de esquema de base de datos.       |
|`Illuminate\Database\Events\SchemaLoaded`     |Se ha cargado un volcado de esquema de base de datos existente.|
