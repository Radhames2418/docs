# Pruebas de bases de datos

- [Introducción](#introduction)
  - [Restablecer la base de datos después de cada test](#resetting-the-database-after-each-test)
- [Fábricas de modelos](#model-factories)
- [Ejecución de sembradoras](#running-seeders)
- [Aserciones disponibles](#available-assertions)

[]()

## Introducción

Laravel proporciona una variedad de herramientas útiles y aserciones para hacer más fácil la test de sus aplicaciones basadas en bases de datos. Además, las fábricas de modelos y los sembradores de Laravel facilitan la creación de registros de base de datos test utilizando los modelos y relaciones Eloquent de tu aplicación. Vamos a discutir todas estas características de gran alcance en la siguiente documentación.

[]()

### Restablecimiento de la base de datos después de cada test

Antes de seguir adelante, vamos a discutir cómo restablecer la base de datos después de cada una de las tests para que los datos de una test anterior no interfiera con las tests posteriores. El rasgo `Illuminate\Foundation\Testing\RefreshDatabase` incluido en Laravel se encargará de esto por usted. Basta con utilizar el rasgo en su clase de test:

    <?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        use RefreshDatabase;

        /**
         * A basic functional test example.
         *
         * @return void
         */
        public function test_basic_example()
        {
            $response = $this->get('/');

            // ...
        }
    }

El rasgo `Illuminate\Foundation\Testing\RefreshDatabase` no migra su base de datos si su esquema está actualizado. En su lugar, sólo ejecutará la test dentro de una transacción de base de datos. Por lo tanto, cualquier registro añadido a la base de datos por casos de test que no utilicen este rasgo puede seguir existiendo en la base de datos.

Si desea restablecer totalmente la base de datos mediante migraciones, puede utilizar el rasgo `Illuminate\Foundation\Testing\DatabaseMigrations` en su lugar. Sin embargo, el rasgo `DatabaseMigrations` es significativamente más lento que el rasgo `RefreshDatabase`.

[]()

## Fábricas de modelos

Al realizar pruebas, es posible que necesite insertar algunos registros en la base de datos antes de ejecutar la test. En lugar de especificar manualmente el valor de cada columna cuando creas estos datos de test, Laravel te permite definir un conjunto de atributos por defecto para cada uno de tus [modelos Eloquent](/docs/%7B%7Bversion%7D%7D/eloquent) utilizando [fábricas de modelos](/docs/%7B%7Bversion%7D%7D/eloquent-factories).

Para saber más sobre cómo crear y utilizar fábricas de modelos para crear modelos, consulta la [documentación](/docs/%7B%7Bversion%7D%7D/eloquent-factories) completa sobre [fábricas](/docs/%7B%7Bversion%7D%7D/eloquent-factories) de modelos. Una vez que haya definido una fábrica de modelos, puede utilizar la fábrica dentro de su test para crear modelos:

    use App\Models\User;

    public function test_models_can_be_instantiated()
    {
        $user = User::factory()->create();

        // ...
    }

[]()

## Ejecución de sembradoras

Si desea utilizar [sembradoras de base de datos](/docs/%7B%7Bversion%7D%7D/seeding) para poblar su base de datos durante una test características, puede invocar el método `seed`. Por defecto, el método `seed` ejecutará el `DatabaseSeeder`, que debería ejecutar todos sus otros seeders. Alternativamente, puede pasar un nombre de clase de sembrador específico al método `seed`:

    <?php

    namespace Tests\Feature;

    use Database\Seeders\OrderStatusSeeder;
    use Database\Seeders\TransactionStatusSeeder;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Tests\TestCase;

    class ExampleTest extends TestCase
    {
        use RefreshDatabase;

        /**
         * Test creating a new order.
         *
         * @return void
         */
        public function test_orders_can_be_created()
        {
            // Run the DatabaseSeeder...
            $this->seed();

            // Run a specific seeder...
            $this->seed(OrderStatusSeeder::class);

            // ...

            // Run an array of specific seeders...
            $this->seed([
                OrderStatusSeeder::class,
                TransactionStatusSeeder::class,
                // ...
            ]);
        }
    }

Alternativamente, puedes instruir a Laravel para que siembre automáticamente la base de datos antes de cada test que utilice el rasgo `RefreshDatabase`. Para ello, defina una propiedad `$seed` en su clase base de test:

    <?php

    namespace Tests;

    use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

    abstract class TestCase extends BaseTestCase
    {
        use CreatesApplication;

        /**
         * Indicates whether the default seeder should run before each test.
         *
         * @var bool
         */
        protected $seed = true;
    }

Cuando la propiedad `$seed` es `true`, la test ejecutará la clase `Database\Seeders\DatabaseSeeder` antes de cada test que utilice el rasgo `RefreshDatabase`. Sin embargo, puedes especificar un seeder específico que deba ejecutarse definiendo una propiedad `$seeder` en tu clase de test:

    use Database\Seeders\OrderStatusSeeder;

    /**
     * Run a specific seeder before each test.
     *
     * @var string
     */
    protected $seeder = OrderStatusSeeder::class;

[]()

## Aserciones disponibles

Laravel proporciona varias aserciones de base de datos para tus pruebas tests rasgo [PHPUnit](https://phpunit.de/). Discutiremos cada una de estas aserciones a continuación.

[]()

#### assertDatabaseCount

Comprueba que una tabla de la base de datos contiene el número de registros dado:

    $this->assertDatabaseCount('users', 5);

[]()

#### assertDatabaseHas

Comprueba que una tabla de la base de datos contiene registros que coinciden con las restricciones de consulta clave/valor dadas:

    $this->assertDatabaseHas('users', [
        'email' => 'sally@example.com',
    ]);

[]()

#### assertDatabaseMissing

Comprobar que una tabla de la base de datos no contiene registros que coincidan con las restricciones de consulta de clave/valor dadas:

    $this->assertDatabaseMissing('users', [
        'email' => 'sally@example.com',
    ]);

[]()

#### assertSoftDeleted

El método `assertSoftDeleted` se puede utilizar para afirmar que un determinado modelo de Eloquent ha sido "borrado suavemente":

    $this->assertSoftDeleted($user);

[]()

#### assertNotSoftDeleted

El método `assertNotSoftDeleted` puede usarse para afirmar que un modelo Eloquent dado no ha sido "borrado suavemente":

    $this->assertNotSoftDeleted($user);

[]()

#### assertModelExists

Compruebe que un modelo determinado existe en la base de datos:

    use App\Models\User;

    $user = User::factory()->create();

    $this->assertModelExists($user);

[]()

#### assertModelMissing

Compruebe que un modelo determinado no existe en la base de datos:

    use App\Models\User;

    $user = User::factory()->create();

    $user->delete();

    $this->assertModelMissing($user);
