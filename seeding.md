# Database: Sembradoras

- [Introducción](#introduction)
- [Escribir sembradoras](#writing-seeders)
  - [Uso de fábricas de modelos](#using-model-factories)
  - [Llamada a sembradoras adicionales](#calling-additional-seeders)
  - [Silenciamiento de eventos de modelo](#muting-model-events)
- [Ejecución de sembradoras](#running-seeders)

[]()

## Introducción

Laravel incluye la capacidad de sembrar tu base de datos con datos usando clases semilla. Todas las clases semilla se almacenan en el directorio `database/seeders`. Por defecto, se define una clase `DatabaseSeeder`. Desde esta clase, puede utilizar el método `call` para ejecutar otras clases semilla, permitiéndole controlar el orden de siembra.

> **Nota**  
> [La protección de asignación masiva](/docs/%7B%7Bversion%7D%7D/eloquent#mass-assignment) se desactiva automáticamente durante la siembra de la base de datos.

[]()

## Escritura de Sembradoras

Para generar un sembrador, ejecute el [comando](/docs/%7B%7Bversion%7D%7D/artisan) `make:seeder` [de Artisan](/docs/%7B%7Bversion%7D%7D/artisan). Todos los sembradores generados por el framework se colocarán en el directorio `database/seeders`:

```shell
php artisan make:seeder UserSeeder
```

Por defecto, una clase seeder sólo contiene un método: `run`. Este método es llamado cuando se ejecuta el [comando](/docs/%7B%7Bversion%7D%7D/artisan) `db:seed` de Artisan. Dentro del método `run`, puedes insertar datos en tu base de datos como desees. Puedes usar el [constructor de consultas](/docs/%7B%7Bversion%7D%7D/queries) para insertar datos manualmente o puedes usar [las fábricas de modelos de Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent-factories).

Como ejemplo, modifiquemos la clase `DatabaseSeeder` por defecto y añadamos una sentencia de inserción de base de datos al método de `ejecución`:

    <?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Str;

    class DatabaseSeeder extends Seeder
    {
        /**
         * Run the database seeders.
         *
         * @return void
         */
        public function run()
        {
            DB::table('users')->insert([
                'name' => Str::random(10),
                'email' => Str::random(10).'@gmail.com',
                'password' => Hash::make('password'),
            ]);
        }
    }

> **Nota**  
> Puedes escribir cualquier dependencia que necesites dentro de la firma del método de `ejecución`. Se resolverán automáticamente a través del [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) de Laravel.

[]()

### Uso de fábricas de modelos

Por supuesto, especificar manualmente los atributos para cada semilla de modelo es engorroso. En su lugar, puede utilizar [las fá](/docs/%7B%7Bversion%7D%7D/eloquent-factories) bricas de modelos para generar convenientemente grandes cantidades de registros de base de datos. En primer lugar, revise la [documentación](/docs/%7B%7Bversion%7D%7D/eloquent-factories) de las fábricas de modelos para aprender a definir sus fábricas.

Por ejemplo, vamos a crear 50 usuarios que cada uno tiene un post relacionado:

    use App\Models\User;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
                ->count(50)
                ->hasPosts(1)
                ->create();
    }

[]()

### Llamada a sembradoras adicionales

Dentro de la clase `DatabaseSeeder`, puede utilizar el método `call` para ejecutar clases semilla adicionales. Utilizar el método `call` le permite dividir su sembrado de base de datos en múltiples archivos para que ninguna clase sembradora sea demasiado grande. El método `call` acepta un array de clases sembradoras que deben ejecutarse:

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
        ]);
    }

[]()

### Silenciando Eventos de Modelo

Mientras se ejecutan las semillas, es posible que desee evitar que los modelos envíen eventos. Puede conseguirlo utilizando el rasgo `WithoutModelEvents`. Cuando se utiliza, el rasgo `WithoutModelEvents` garantiza que no se envíen eventos de modelo, incluso si se ejecutan clases sembradoras adicionales a través del método de `llamada`:

    <?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;

    class DatabaseSeeder extends Seeder
    {
        use WithoutModelEvents;

        /**
         * Run the database seeders.
         *
         * @return void
         */
        public function run()
        {
            $this->call([
                UserSeeder::class,
            ]);
        }
    }

[]()

## Ejecución de sembradoras

Puede ejecutar el comando Artisan `db`:seed para sembrar su base de datos. Por defecto, el comando db: `seed` ejecuta la clase `Database\Seeders\DatabaseSeeder`, que a su vez puede invocar otras clases seed. Sin embargo, puede utilizar la opción `--class` para especificar una clase específica de sembradora a ejecutar individualmente:

```shell
php artisan db:seed

php artisan db:seed --class=UserSeeder
```

También puede sembrar su base de datos utilizando el comando `migrate:fresh` en combinación con la opción `--seed`, que eliminará todas las tablas y volverá a ejecutar todas sus migraciones. Este comando es útil para reconstruir completamente la base de datos. La opción `--seeder` se puede utilizar para especificar una sembradora concreta que ejecutar:

```shell
php artisan migrate:fresh --seed

php artisan migrate:fresh --seed --seeder=UserSeeder 
```

[]()

#### Forzar la ejecución de sembradoras en producción

Algunas operaciones de sembrado pueden provocar la alteración o pérdida de datos. Para evitar que ejecute comandos de siembra contra su base de datos de producción, se le pedirá confirmación antes de ejecutar los sembradores en el entorno de `producción`. Para forzar la ejecución de los sembradores sin preguntar, utilice el indicador `--force`:

```shell
php artisan db:seed --force
```
