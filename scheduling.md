# Programación de tareas

- [Introducción](#introduction)
- [Definición de horarios](#defining-schedules)
  - [Programación de comandos Artisan](#scheduling-artisan-commands)
  - [Programación de trabajos en cola](#scheduling-queued-jobs)
  - [Programación de comandos Shell](#scheduling-shell-commands)
  - [Opciones de Frecuencia de Programación](#schedule-frequency-options)
  - [Zonas Horarias](#timezones)
  - [Prevención de solapamiento de tareas](#preventing-task-overlaps)
  - [Ejecución de Tareas en un Servidor](#running-tasks-on-one-server)
  - [Tareas en segundo plano](#background-tasks)
  - [Modo de Mantenimiento](#maintenance-mode)
- [Ejecución del Planificador](#running-the-scheduler)
  - [Ejecución Local del Programador](#running-the-scheduler-locally)
- [Salida de tareas](#task-output)
- [Ganchos de Tarea](#task-hooks)
- [Eventos](#events)

[]()

## Introducción

En el pasado, es posible que haya escrito una entrada de configuración cron para cada tarea que necesitaba programar en su servidor. Sin embargo, esto puede convertirse rápidamente en una molestia porque su programación de tareas ya no está en el control de código fuente y debe SSH en su servidor para ver sus entradas cron existentes o añadir entradas adicionales.

El programador de comandos de Laravel ofrece un nuevo enfoque para la gestión de tareas programadas en el servidor. El programador le permite definir de forma fluida y expresiva su programación de comandos dentro de su propia aplicación Laravel. Cuando se utiliza el programador, sólo se necesita una única entrada cron en su servidor. Tu programación de tareas se define en el método `schedule` del archivo `app/Console/Kernel.php`. Para ayudarle a empezar, se define un ejemplo simple dentro del método.

[]()

## Definiendo Planificaciones

Puede definir todas sus tareas programadas en el método `schedule` de la clase `App\Console\Kernel` de su aplicación. Para empezar, veamos un ejemplo. En este ejemplo, vamos a programar un closure para ser llamado todos los días a medianoche. Dentro del closure ejecutaremos una consulta a la base de datos para limpiar una tabla:

    <?php

    namespace App\Console;

    use Illuminate\Console\Scheduling\Schedule;
    use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
    use Illuminate\Support\Facades\DB;

    class Kernel extends ConsoleKernel
    {
        /**
         * Define the application's command schedule.
         *
         * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
         * @return void
         */
        protected function schedule(Schedule $schedule)
        {
            $schedule->call(function () {
                DB::table('recent_users')->delete();
            })->daily();
        }
    }

Además de programar usando closures, también puede programar [objetos inv](https://secure.php.net/manual/en/language.oop5.magic.php#object.invoke)ocables. Los objetos invocables son clases PHP simples que contienen un método `__invoke`:

    $schedule->call(new DeleteRecentUsers)->daily();

Si desea ver una visión general de sus tareas programadas y la próxima vez que están programadas para ejecutarse, puede utilizar el comando `schedule:list` Artisan:

```bash
php artisan schedule:list
```

[]()

### Programación de Comandos Artisan

Además de programar closures, también puede programar [comandos Artisan](/docs/%7B%7Bversion%7D%7D/artisan) y comandos de sistema. Por ejemplo, puede utilizar el método `command` para programar un comando Artisan utilizando el nombre o la clase del comando.

Al programar comandos Artisan utilizando el nombre de clase del comando, puede pasar una array de argumentos de línea de comandos adicionales que deben proporcionarse al comando cuando se invoca:

    use App\Console\Commands\SendEmailsCommand;

    $schedule->command('emails:send Taylor --force')->daily();

    $schedule->command(SendEmailsCommand::class, ['Taylor', '--force'])->daily();

[]()

### Programación de Trabajos en Cola

El método `job` puede utilizarse para programar un [trabajo en cola](/docs/%7B%7Bversion%7D%7D/queues). Este método proporciona una manera conveniente de programar trabajos en cola sin usar el método `call` para definir closures para poner el trabajo en cola:

    use App\Jobs\Heartbeat;

    $schedule->job(new Heartbeat)->everyFiveMinutes();

Los argumentos opcionales segundo y tercero pueden ser proporcionados al método `job` que especifica el nombre de la cola y la conexión de la cola que debe ser utilizada para poner en cola el trabajo:

    use App\Jobs\Heartbeat;

    // Dispatch the job to the "heartbeats" queue on the "sqs" connection...
    $schedule->job(new Heartbeat, 'heartbeats', 'sqs')->everyFiveMinutes();

[]()

### Programación de Comandos Shell

El método `exec` puede utilizarse para enviar un comando al sistema operativo:

    $schedule->exec('node /home/forge/script.js')->daily();

[]()

### Opciones de Frecuencia de Programación

Ya hemos visto algunos ejemplos de cómo configurar una tarea para que se ejecute a intervalos específicos. Sin embargo, hay muchas más frecuencias de programación de tareas que puede asignar a una tarea:

|Método                           |Descripción                                                  |
|---------------------------------|-------------------------------------------------------------|
|`->cron('* * * * *');`           |Ejecutar la tarea en un horario cron personalizado           |
|`->everyMinute();`               |Ejecutar la tarea cada minuto                                |
|`->everyTwoMinutes();`           |Ejecutar la tarea cada dos minutos                           |
|`->everyThreeMinutes();`         |Ejecutar la tarea cada tres minutos                          |
|`->everyFourMinutes();`          |Ejecutar la tarea cada cuatro minutos                        |
|`->everyFiveMinutes();`          |Ejecutar la tarea cada cinco minutos                         |
|`->everyTenMinutes();`           |Ejecutar la tarea cada diez minutos                          |
|`->everyFifteenMinutes();`       |Ejecutar la tarea cada quince minutos                        |
|`->everyThirtyMinutes();`        |Ejecutar la tarea cada treinta minutos                       |
|`->hourly();`                    |Ejecutar la tarea cada hora                                  |
|`->hourlyAt(17);`                |Ejecutar la tarea cada hora a las 17 horas y 17 minutos      |
|`->everyOddHour();`              |Ejecutar la tarea cada hora impar                            |
|`->everyTwoHours();`             |Ejecutar la tarea cada dos horas                             |
|`->everyThreeHours();`           |Ejecutar la tarea cada tres horas                            |
|`->everyFourHours();`            |Ejecutar la tarea cada cuatro horas                          |
|`->everySixHours();`             |Ejecutar la tarea cada seis horas                            |
|`->daily();`                     |Ejecutar la tarea todos los días a medianoche                |
|`->dailyAt('13:00');`            |Ejecute la tarea todos los días a las 13:00                  |
|`->twiceDaily(1, 13);`           |Ejecutar la tarea diariamente a la 1:00 y a las 13:00        |
|`->twiceDailyAt(1, 13, 15);`     |Ejecutar la tarea diariamente a la 1:15 y a las 13:15        |
|`->weekly();`                    |Ejecute la tarea todos los domingos a las 00:00              |
|`->weeklyOn(1, '8:00');`         |Ejecute la tarea cada semana el lunes a las 8:00             |
|`->monthly();`                   |Ejecute la tarea el primer día de cada mes a las 00:00       |
|`->monthlyOn(4, '15:00');`       |Ejecute la tarea cada mes el día 4 a las 15:00               |
|`->twiceMonthly(1, 16, '13:00');`|Ejecute la tarea mensualmente los días 1 y 16 a las 13:00    |
|`->lastDayOfMonth('15:00');`     |Ejecute la tarea el último día del mes a las 15:00           |
|`->quarterly();`                 |Ejecutar la tarea el primer día de cada trimestre a las 00:00|
|`->quarterlyOn(4, '14:00');`     |Ejecutar la tarea cada trimestre el día 4 a las 14:00        |
|`->yearly();`                    |Ejecutar la tarea el primer día de cada año a las 00:00      |
|`->yearlyOn(6, 1, '17:00');`     |Ejecutar la tarea cada año el 1 de junio a las 17:00         |
|`->timezone('America/New_York');`|Establecer la zona horaria de la tarea                       |

Estos métodos pueden combinarse con restricciones adicionales para crear programaciones aún más precisas que sólo se ejecuten en determinados días de la semana. Por ejemplo, puede programar una orden para que se ejecute semanalmente los lunes:

    // Run once per week on Monday at 1 PM...
    $schedule->call(function () {
        //
    })->weekly()->mondays()->at('13:00');

    // Run hourly from 8 AM to 5 PM on weekdays...
    $schedule->command('foo')
              ->weekdays()
              ->hourly()
              ->timezone('America/Chicago')
              ->between('8:00', '17:00');

A continuación encontrará una lista de restricciones de programación adicionales:

|Método                                  |Descripción                                                            |
|----------------------------------------|-----------------------------------------------------------------------|
|`->weekdays();`                         |Limitar la tarea a los días de la semana                               |
|`->weekends();`                         |Limitar la tarea a los fines de semana                                 |
|`->sundays();`                          |Limitar la tarea al domingo                                            |
|`->mondays();`                          |Limitar la tarea al lunes                                              |
|`->tuesdays();`                         |Limitar la tarea al martes                                             |
|`->wednesdays();`                       |Limitar la tarea al miércoles                                          |
|`->thursdays();`                        |Limitar la tarea al jueves                                             |
|`->fridays();`                          |Limitar la tarea al viernes                                            |
|`->saturdays();`                        |Limitar la tarea al sábado                                             |
|`->days(array\|mixed);`                 |Limitar la tarea a días específicos                                    |
|`->between($startTime, $endTime);`      |Limitar la tarea para que se ejecute entre las horas de inicio y fin   |
|`->unlessBetween($startTime, $endTime);`|Limitar la tarea para que no se ejecute entre las horas de inicio y fin|
|`->when(Closure);`                      |Limitar la tarea en función de una test veracidad                      |
|`->environments($env);`                 |Limitar la tarea a entornos específicos                                |

[]()

#### Restricciones de días

El método `días` puede utilizarse para limitar la ejecución de una tarea a determinados días de la semana. Por ejemplo, puede programar un comando para que se ejecute cada hora los domingos y los miércoles:

    $schedule->command('emails:send')
                    ->hourly()
                    ->days([0, 3]);

Alternativamente, puede utilizar las constantes disponibles en la clase `Illuminate\Console\Scheduling\Schedule` al definir los días en los que debe ejecutarse una tarea:

    use Illuminate\Console\Scheduling\Schedule;

    $schedule->command('emails:send')
                    ->hourly()
                    ->days([Schedule::SUNDAY, Schedule::WEDNESDAY]);

[]()

#### Restricciones entre horas

El método `between` puede utilizarse para limitar la ejecución de una tarea en función de la hora del día:

    $schedule->command('emails:send')
                        ->hourly()
                        ->between('7:00', '22:00');

De forma similar, el método `unlessBetween` puede utilizarse para excluir la ejecución de una tarea durante un periodo de tiempo:

    $schedule->command('emails:send')
                        ->hourly()
                        ->unlessBetween('23:00', '4:00');

[test-constraints">]()

#### Restricciones de la test de verdad

El método `when` puede utilizarse para limitar la ejecución de una tarea en función del resultado de una test de verdad dada. En otras palabras, si el closure dado devuelve `verdadero`, la tarea se ejecutará siempre que no haya otras condiciones restrictivas que impidan la ejecución de la tarea:

    $schedule->command('emails:send')->daily()->when(function () {
        return true;
    });

El método `skip` puede considerarse como la inversa de `when`. Si el método `skip` devuelve `true`, la tarea programada no se ejecutará:

    $schedule->command('emails:send')->daily()->skip(function () {
        return true;
    });

Cuando se utilizan métodos `when` encadenados, la orden programada sólo se ejecutará si todas las condiciones `when` devuelven `verdadero`.

[]()

#### Restricciones de entorno

El método `environments` puede utilizarse para ejecutar tareas sólo en los entornos dados (definidos por la [variable de entorno](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration) `APP_ENV` ):

    $schedule->command('emails:send')
                ->daily()
                ->environments(['staging', 'production']);

[]()

### Zonas Horarias

Utilizando el método `timezone`, puede especificar que la hora de una tarea programada debe interpretarse dentro de una zona horaria determinada:

    $schedule->command('report:generate')
             ->timezone('America/New_York')
             ->at('2:00')

Si asigna repetidamente la misma zona horaria a todas sus tareas programadas, puede definir un método `scheduleTimezone` en su clase `App\Console\Kernel`. Este método debería devolver la zona horaria por defecto que debería asignarse a todas las tareas programadas:

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'America/Chicago';
    }

> **Advertencia**  
> Recuerde que algunas zonas horarias utilizan el horario de verano. Cuando se producen cambios en el horario de verano, la tarea programada puede ejecutarse dos veces o incluso no ejecutarse en absoluto. Por este motivo, le recomendamos que evite la programación por zonas horarias siempre que sea posible.

[]()

### Prevención de solapamiento de tareas

Por defecto, las tareas programadas se ejecutarán incluso si la instancia anterior de la tarea sigue en ejecución. Para evitarlo, puede utilizar el método `withoutOverlapping`:

    $schedule->command('emails:send')->withoutOverlapping();

En este ejemplo, el [comando](/docs/%7B%7Bversion%7D%7D/artisan) `emails:send` [Artisan](/docs/%7B%7Bversion%7D%7D/artisan) se ejecutará cada minuto si no se está ejecutando ya. El método `withoutOverlapping` es especialmente útil si tiene tareas que varían drásticamente en su tiempo de ejecución, impidiéndole predecir exactamente cuánto tiempo tardará una tarea determinada.

Si es necesario, puede especificar cuántos minutos deben pasar antes de que expire el bloqueo "sin solapamiento". Por defecto, el bloqueo expira a las 24 horas:

    $schedule->command('emails:send')->withoutOverlapping(10);

Entre bastidores, el método `sinSolapamiento` utiliza la [cache](/docs/%7B%7Bversion%7D%7D/cache) de su aplicación para obtener bloqueos. Si es necesario, puede borrar estos bloqueos de cache utilizando el comando `schedule:clear-cache` de Artisan. Esto sólo suele ser necesario si una tarea se bloquea debido a un problema inesperado del servidor.

[]()

### Ejecución de Tareas en un Servidor

> **Advertencia**  
> Para utilizar esta función, su aplicación debe utilizar el controlador de cache de `base de datos`, `memcached`, `dynamodb` o `redis` como controlador de cache predeterminado de su aplicación. Además, todos los servidores deben comunicarse con el mismo servidor central de cache.

Si el programador de tu aplicación se ejecuta en varios servidores, puedes limitar un trabajo programado para que sólo se ejecute en un único servidor. Por ejemplo, supongamos que tiene una tarea programada que genera un nuevo informe cada viernes por la noche. Si el programador de tareas se ejecuta en tres servidores de trabajo, la tarea programada se ejecutará en los tres servidores y generará el informe tres veces. Esto no es bueno.

Para indicar que la tarea debe ejecutarse sólo en un servidor, utilice el método `onOneServer` al definir la tarea programada. El primer servidor que obtenga la tarea asegurará un bloqueo atómico en el trabajo para evitar que otros servidores ejecuten la misma tarea al mismo tiempo:

    $schedule->command('report:generate')
                    ->fridays()
                    ->at('17:00')
                    ->onOneServer();

[]()

#### Asignación de nombres a trabajos de un único servidor

A veces puede ser necesario programar el mismo trabajo para ser despachado con diferentes parámetros, sin dejar de instruir a Laravel para ejecutar cada permutación del trabajo en un solo servidor. Para lograr esto, puede asignar a cada definición de programación un nombre único a través del método `name`:

```php
$schedule->job(new CheckUptime('https://laravel.com'))
            ->name('check_uptime:laravel.com')
            ->everyFiveMinutes()
            ->onOneServer();

$schedule->job(new CheckUptime('https://vapor.laravel.com'))
            ->name('check_uptime:vapor.laravel.com')
            ->everyFiveMinutes()
            ->onOneServer();
```

Del mismo modo, a closures programados se les debe asignar un nombre si se pretende que se ejecuten en un solo servidor:

```php
$schedule->call(fn () => User::resetApiRequestCount())
    ->name('reset-api-request-count')
    ->daily()
    ->onOneServer();
```

[]()

### Tareas en Segundo Plano

Por defecto, varias tareas programadas al mismo tiempo se ejecutarán secuencialmente según el orden en que estén definidas en su método `schedule`. Si tiene tareas de larga duración, esto puede provocar que las tareas posteriores se inicien mucho más tarde de lo previsto. Si desea ejecutar tareas en segundo plano para que todas se ejecuten simultáneamente, puede utilizar el método `runInBackground`:

    $schedule->command('analytics:report')
             ->daily()
             ->runInBackground();

> **Advertencia**  
> El método `runInBackground` sólo puede utilizarse cuando se programan tareas mediante los métodos `command` y `exec`.

[]()

### Modo Mantenimiento

Las tareas programadas de su aplicación no se ejecutarán cuando la aplicación esté en [modo de mantenimiento](/docs/%7B%7Bversion%7D%7D/configuration#maintenance-mode), ya que no queremos que sus tareas interfieran con cualquier mantenimiento inacabado que pueda estar realizando en su servidor. Sin embargo, si quieres forzar que una tarea se ejecute incluso en modo mantenimiento, puedes llamar al método `evenInMaintenanceMode` cuando definas la tarea:

    $schedule->command('emails:send')->evenInMaintenanceMode();

[]()

## Ejecución del Planificador

Ahora que hemos aprendido cómo definir tareas programadas, vamos a discutir cómo ejecutarlas en nuestro servidor. El comando `schedule:run` Artisan evaluará todas sus tareas programadas y determinará si necesitan ejecutarse basándose en la hora actual del servidor.

Así, al utilizar el planificador de Laravel, sólo necesitamos añadir una única entrada de configuración cron a nuestro servidor que ejecute el comando `schedule:` run cada minuto. Si no sabes cómo añadir entradas cron a tu servidor, considera utilizar un servicio como [Laravel Forge](https://forge.laravel.com) que puede gestionar las entradas cron por ti:

```shell
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

[]()

## Ejecución Local del Programador

Típicamente, usted no agregaría una entrada cron del programador a su máquina de desarrollo local. En su lugar, puede utilizar el comando `schedule:work` Artisan. Este comando se ejecutará en primer plano e invocará al planificador cada minuto hasta que termines el comando:

```shell
php artisan schedule:work
```

[]()

## Salida de Tareas

El planificador de Laravel proporciona varios métodos convenientes para trabajar con la salida generada por las tareas programadas. En primer lugar, utilizando el método `sendOutputTo`, puede enviar la salida a un archivo para su posterior inspección:

    $schedule->command('emails:send')
             ->daily()
             ->sendOutputTo($filePath);

Si desea añadir la salida a un archivo determinado, puede utilizar el método `appendOutputTo`:

    $schedule->command('emails:send')
             ->daily()
             ->appendOutputTo($filePath);

Utilizando el método `emailOutputTo`, puede enviar la salida por correo electrónico a una dirección de correo electrónico de su elección. Antes de enviar por correo electrónico la salida de una tarea, debes configurar los [servicios de correo electrónico](/docs/%7B%7Bversion%7D%7D/mail) de Laravel:

    $schedule->command('report:generate')
             ->daily()
             ->sendOutputTo($filePath)
             ->emailOutputTo('taylor@example.com');

Si sólo desea enviar la salida por correo electrónico si el comando programado de Artisan o del sistema termina con un código de salida distinto de cero, utilice el método `emailOutputOnFailure`:

    $schedule->command('report:generate')
             ->daily()
             ->emailOutputOnFailure('taylor@example.com');

> **Advertencia**  
> Los métodos `emailOutputTo`, `emailOutputOnFailure`, `sendOutputTo` y `appendOutputTo` son exclusivos de los métodos `command` y `exec`.

[]()

## Ganchos de tarea

Mediante los métodos `before` y `after`, puede especificar el código que se ejecutará antes y después de que se ejecute la tarea programada:

    $schedule->command('emails:send')
             ->daily()
             ->before(function () {
                 // The task is about to execute...
             })
             ->after(function () {
                 // The task has executed...
             });

Los métodos `onSuccess` y `onFailure` permiten especificar el código que se ejecutará si la tarea programada tiene éxito o falla. Un fallo indica que el comando programado de Artisan o del sistema ha finalizado con un código de salida distinto de cero:

    $schedule->command('emails:send')
             ->daily()
             ->onSuccess(function () {
                 // The task succeeded...
             })
             ->onFailure(function () {
                 // The task failed...
             });

Si la salida está disponible desde tu comando, puedes acceder a ella en tus hooks `after`, `onSuccess` o `onFailure` indicando una instancia `Illuminate\Support\Stringable` como argumento `$output` de la definición de closure de tu hook:

    use Illuminate\Support\Stringable;

    $schedule->command('emails:send')
             ->daily()
             ->onSuccess(function (Stringable $output) {
                 // The task succeeded...
             })
             ->onFailure(function (Stringable $output) {
                 // The task failed...
             });

[]()

#### Ping a URLs

Usando los métodos `pingBefore` y `thenPing`, el planificador puede hacer ping automáticamente a una URL dada antes o después de que se ejecute una tarea. Este método es útil para notificar a un servicio externo, como [Envoyer](https://envoyer.io), que su tarea programada está comenzando o ha finalizado su ejecución:

    $schedule->command('emails:send')
             ->daily()
             ->pingBefore($url)
             ->thenPing($url);

Los métodos `pingBeforeIf` y `thenPingIf` pueden ser utilizados para hacer ping a una URL dada sólo si una condición dada es `verdadera`:

    $schedule->command('emails:send')
             ->daily()
             ->pingBeforeIf($condition, $url)
             ->thenPingIf($condition, $url);

Los métodos `pingOnSuccess` y `pingOnFailure` pueden utilizarse para hacer ping a una URL dada sólo si la tarea tiene éxito o falla. Un fallo indica que el comando programado de Artisan o del sistema ha finalizado con un código de salida distinto de cero:

    $schedule->command('emails:send')
             ->daily()
             ->pingOnSuccess($successUrl)
             ->pingOnFailure($failureUrl);

Todos los métodos de ping requieren la librería HTTP Guzzle. Guzzle es típicamente instalada en todos los nuevos proyectos Laravel por defecto, pero, puedes instalar manualmente Guzzle en tu proyecto usando el gestor de paquetes Composer si ha sido accidentalmente removida:

```shell
composer require guzzlehttp/guzzle
```

[]()

## Eventos

Si es necesario, puede escuchar [los eventos](/docs/%7B%7Bversion%7D%7D/events) enviados por el programador. Normalmente, las asignaciones de escucha de eventos se definirán dentro de la clase `AppProviders\EventServiceProvider` de su aplicación:

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Console\Events\ScheduledTaskStarting' => [
            'App\Listeners\LogScheduledTaskStarting',
        ],

        'Illuminate\Console\Events\ScheduledTaskFinished' => [
            'App\Listeners\LogScheduledTaskFinished',
        ],

        'Illuminate\Console\Events\ScheduledBackgroundTaskFinished' => [
            'App\Listeners\LogScheduledBackgroundTaskFinished',
        ],

        'Illuminate\Console\Events\ScheduledTaskSkipped' => [
            'App\Listeners\LogScheduledTaskSkipped',
        ],

        'Illuminate\Console\Events\ScheduledTaskFailed' => [
            'App\Listeners\LogScheduledTaskFailed',
        ],
    ];
