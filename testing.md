# Pruebas: Primeros pasos

- [Introducción](#introduction)
- [Entorno](#environment)
- [Creación de tests](#creating-tests)
- [Ejecución de tests](#running-tests)
  - [tests-in-parallel">Ejecución de tests en paralelo](<#running-\<glossary variable=>)
  - [test-coverage">Informes de cobertura de las test](<#reporting-\<glossary variable=>)

[]()

## Introducción

Laravel está construido con las pruebas en mente. De hecho, el soporte para pruebas con PHPUnit está incluido de fábrica y un archivo `phpunit.xml` ya está configurado para su aplicación. El framework también incluye convenientes métodos de ayuda que le permiten test sus aplicaciones de forma expresiva.

Por defecto, el directorio de `tests` de su aplicación contiene dos directorios: `Feature` y `Unit`. tests pruebas unitarias tests centran en una parte muy pequeña y aislada del código. De hecho, la mayoría de las tests unitarias probablemente se centran en un solo método. tests dentro de tu directorio de test "Unit" no arrancan tu aplicación Laravel y por lo tanto no pueden acceder a la base de datos de tu aplicación o a otros servicios del framework.

Las tests características pueden test una porción más grande de su código, incluyendo cómo varios objetos interactúan entre sí o incluso una solicitud HTTP completa a un punto final JSON. **En general, la mayoría de las tests deben ser tests características. Estos tipos de tests proporcionan la mayor confianza de que su sistema en su conjunto está funcionando según lo previsto.**

Un archivo `ExampleTest.` php se proporciona tanto en la `característica` y la `unidad` de directorios de test. Después de instalar una nueva aplicación Laravel, ejecuta los comandos `vendor/bin/phpunit` o `php artisan test` para ejecutar tus tests.

[]()

## Entorno

Al ejecutar las tests, Laravel establecerá automáticamente el [entorno de configuración](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration) a `pruebas` debido a las variables de entorno definidas en el archivo `phpunit.xml`. Laravel también configura automáticamente la sesión y la cache al controlador de `array` mientras se realizan las pruebas, lo que significa que no se persistirá en los datos de sesión o cache mientras se realizan las pruebas.

Usted es libre de definir otros valores de configuración del entorno de pruebas según sea necesario. Las variables del entorno de `pruebas` pueden configurarse en el archivo `phpunit.xml` de su aplicación, pero asegúrese de borrar cache configuración utilizando el comando `config:clear` Artisan antes de ejecutar sus tests.

[]()

#### El archivo de entorno . `env.testing`

Además, puede crear un archivo `.` env.testing en la raíz de su proyecto. Este archivo será utilizado en lugar del archivo . `env` cuando se ejecuten tests PHPUnit o comandos Artisan con la opción `--env=testing`.

[]()

#### El rasgo `CreatesApplication`

Laravel incluye un rasgo `CreatesApplication` que se aplica a la clase `TestCase` base de tu aplicación. Este trait contiene un método `createApplication` que arranca la aplicación Laravel antes de ejecutar las tests. Es importante que dejes este trait en su ubicación original, ya que algunas características, como la función de pruebas paralelas de Laravel, dependen de él.

[]()

## Creación de tests

Para crear un nuevo caso de test, utiliza el comando `make:test` Artisan. Por defecto, tests se colocarán en el directorio `tests`:

```shell
php artisan make:test UserTest
```

Si desea crear una test dentro del directorio `tests`, puede utilizar la opción `--unit` al ejecutar el comando make: `test`:

```shell
php artisan make:test UserTest --unit
```

Si desea crear una test de [Pest PHP](https://pestphp.com), puede proporcionar la opción `--pest` al comando `make:test`:

```shell
php artisan make:test UserTest --pest
php artisan make:test UserTest --unit --pest
```

> **Nota**  
> stubs prueba pueden personalizarse utilizando [stub-customization">la publicación destub](</docs/%7B%7Bversion%7D%7D/artisan#\<glossary variable=>).

Una vez que la test ha sido generada, puede definir los métodos de test como lo haría normalmente usando [PHPUnit](https://phpunit.de). Para ejecutar sus tests, ejecute el comando `vendor/bin/phpunit` o `php artisan test` desde su terminal:

    <?php

    namespace Tests\Unit;

    use PHPUnit\Framework\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic test example.
         *
         * @return void
         */
        public function test_basic_test()
        {
            $this->assertTrue(true);
        }
    }

> **Advertencia**  
> Si define sus propios métodos `setUp` / `tearDown` dentro de una clase de test, asegúrese de llamar a los respectivos métodos `parent::setUp()` / `parent::tearDown(` ) en la clase padre.

[]()

## Ejecución de tests

Como se mencionó anteriormente, una vez que haya escrito las tests, puede ejecutarlas usando `phpunit`:

```shell
./vendor/bin/phpunit
```

Además del comando `phpunit`, puedes utilizar el comando `test` Artisan para ejecutar tus tests. El ejecutor de test Artisan proporciona informes detallados de las test para facilitar el desarrollo y la depuración:

```shell
php artisan test
```

Cualquier argumento que pueda ser pasado al comando `phpunit` también puede ser pasado al comando test Artisan:

```shell
php artisan test --testsuite=Feature --stop-on-failure
```

[tests-in-parallel">]()

### Ejecución de tests en paralelo

Por defecto, Laravel y PHPUnit ejecutan tus tests secuencialmente dentro de un único proceso. Sin embargo, usted puede reducir en gran medida la cantidad de tiempo que se tarda en ejecutar sus tests mediante la ejecución de tests de forma simultánea a través de múltiples procesos. Para empezar, asegúrate de que tu aplicación depende de la versión `^5.3` o superior del paquete `nunomaduro/collision`. A continuación, incluye la opción `--parallel` cuando ejecutes el comando `test` Artisan:

```shell
php artisan test --parallel
```

Por defecto, Laravel creará tantos procesos como núcleos de CPU estén disponibles en tu máquina. Sin embargo, puedes ajustar el número de procesos usando la opción `--processes`:

```shell
php artisan test --parallel --processes=4
```

> **Advertencia**  
> Al ejecutar tests en paralelo, algunas opciones de PHPUnit (como `--do-not-cache-result`) pueden no estar disponibles.

[]()

#### Pruebas paralelas y bases de datos

Siempre que hayas configurado una conexión de base de datos primaria, Laravel se encarga automáticamente de crear y migrar una base de datos de test para cada proceso paralelo que esté ejecutando tus tests. Las bases de datos de test serán sufijadas con un token de proceso que es único por proceso. Por ejemplo, si tienes dos procesos de test paralelos, Laravel creará y utilizará `tus_bases_de_datos_de_prueba_1` y test.

Por defecto, las bases de datos de test persisten entre las llamadas al comando Artisan de `test` para que puedan ser utilizadas de nuevo por las invocaciones de `test` posteriores. Sin embargo, puede volver a crearlas utilizando la opción `--recreate-databases`:

```shell
php artisan test --parallel --recreate-databases
```

[]()

#### Ganchos de Pruebas Paralelas

Ocasionalmente, puede que necesite preparar ciertos recursos utilizados por las tests de su aplicación para que puedan ser utilizados de forma segura por múltiples procesos de test.

Usando la facade `ParallelTesting`, puedes especificar código para ser ejecutado en el `setUp` y `tearDown` de un proceso o caso de test. Los closures dados reciben las variables `$token` y `$testCase` que contienen el token del proceso y el caso de test actual, respectivamente:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\ParallelTesting;
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
            ParallelTesting::setUpProcess(function ($token) {
                // ...
            });

            ParallelTesting::setUpTestCase(function ($token, $testCase) {
                // ...
            });

            // Executed when a test database is created...
            ParallelTesting::setUpTestDatabase(function ($database, $token) {
                Artisan::call('db:seed');
            });

            ParallelTesting::tearDownTestCase(function ($token, $testCase) {
                // ...
            });

            ParallelTesting::tearDownProcess(function ($token) {
                // ...
            });
        }
    }

[]()

#### Accediendo al Token de Pruebas Paralelas

Si desea acceder al "token" del proceso paralelo actual desde cualquier otra ubicación del código de test de su aplicación, puede utilizar el método `token`. Este token es un identificador de cadena único para un proceso de test individual y puede ser utilizado para segmentar recursos a través de procesos de test paralelos. Por ejemplo, Laravel añade automáticamente este token al final de las bases de datos de test creadas por cada proceso de prueba paralelo:

    $token = ParallelTesting::token();

[test-coverage">]()

### Reportando la cobertura de las test

> **Advertencia**  
> Esta característica requiere [Xdebug](https://xdebug.org) o [PCOV](https://pecl.php.net/package/pcov).

Cuando ejecute las tests de su aplicación, puede que quiera determinar si sus casos de test están cubriendo realmente el código de la aplicación y cuánto código de la aplicación se utiliza al ejecutar sus tests. Para ello, puede proporcionar la opción `--coverage` al invocar el comando `test`:

```shell
php artisan test --coverage
```

[]()

#### Imponer un umbral mínimo de cobertura

Puede utilizar la opción `--min` para definir un umbral mínimo de cobertura de test para su aplicación. El conjunto de test fallará si no se alcanza este umbral:

```shell
php artisan test --coverage --min=80.3
```
