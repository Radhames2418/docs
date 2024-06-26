# Programación de Tareas

- [Programación de Tareas](#programación-de-tareas)
  - [Introducción](#introducción)
  - [Definir Horarios](#definir-horarios)
    - [Programación de Comandos Artisan](#programación-de-comandos-artisan)
    - [Programación de Trabajos en Cola](#programación-de-trabajos-en-cola)
    - [Programación de Comandos Shell](#programación-de-comandos-shell)
    - [Opciones de Frecuencia del Horario](#opciones-de-frecuencia-del-horario)
      - [Restricciones por Día](#restricciones-por-día)
      - [Restricciones por Horario](#restricciones-por-horario)
      - [Restricciones por Prueba de Veracidad](#restricciones-por-prueba-de-veracidad)
      - [Restricciones por Entorno](#restricciones-por-entorno)
    - [Zonas Horarias](#zonas-horarias)
    - [Evitar Superposición de Tareas](#evitar-superposición-de-tareas)
    - [Ejecución de Tareas en un Solo Servidor](#ejecución-de-tareas-en-un-solo-servidor)
      - [Nombrando Trabajos de un Solo Servidor](#nombrando-trabajos-de-un-solo-servidor)
    - [Tareas en Segundo Plano](#tareas-en-segundo-plano)
    - [Modo de Mantenimiento](#modo-de-mantenimiento)
  - [Ejecución del Planificador](#ejecución-del-planificador)
  - [Ejecutando el Planificador Localmente](#ejecutando-el-planificador-localmente)
  - [Salida de Tareas](#salida-de-tareas)
  - [Ganchos de Tareas](#ganchos-de-tareas)
      - [Pings a URLs](#pings-a-urls)
  - [Eventos](#eventos)

<a name="introduction"></a>
## Introducción

En el pasado, puede que hayas escrito una entrada de configuración de cron para cada tarea que necesitabas programar en tu servidor. Sin embargo, esto puede volverse rápidamente un dolor de cabeza porque tu programación de tareas ya no está bajo control de versiones y debes acceder mediante SSH a tu servidor para ver tus entradas cron existentes o agregar entradas adicionales.

El programador de comandos de Laravel ofrece un enfoque novedoso para gestionar tareas programadas en tu servidor. El programador te permite definir de manera fluida y expresiva tu programación de comandos dentro de tu propia aplicación Laravel. Al utilizar las tareas programadas, solo se necesita una única entrada cron en tu servidor. Tu programación de tareas se define en el método `schedule` del archivo `app/Console/Kernel.php`. Para ayudarte a comenzar, se define un ejemplo simple dentro del método.

<a name="defining-schedules"></a>
## Definir Horarios

Puedes definir todas tus tareas programadas en el método `schedule` de la clase `App\Console\Kernel` de tu aplicación. Para comenzar, echemos un vistazo a un ejemplo. En este ejemplo, programaremos un closure para que se ejecute todos los días a medianoche. Dentro del closure ejecutaremos una consulta a la base de datos para limpiar una tabla:

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

Además de programar usando closures, también puedes programar [objetos invocables](https://secure.php.net/manual/en/language.oop5.magic.php#object.invoke). Los objetos invocables son clases PHP simples que contienen un método `__invoke`:

    $schedule->call(new DeleteRecentUsers)->daily();

Si deseas ver una vista general de tus tareas programadas y la próxima vez que están programadas para ejecutarse, puedes usar el comando Artisan `schedule:list`:

```bash
php artisan schedule:list
```

<a name="scheduling-artisan-commands"></a>
### Programación de Comandos Artisan

Además de programar closures, también puedes programar [comandos Artisan](/docs/{{version}}/artisan) y comandos del sistema. Por ejemplo, puedes usar el método `command` para programar un comando Artisan utilizando el nombre del comando o su clase.

Al programar comandos Artisan usando el nombre de la clase del comando, puedes pasar un arreglo de argumentos adicionales de línea de comandos que deben proporcionarse al comando cuando se invoque:

    use App\Console\Commands\SendEmailsCommand;

    $schedule->command('emails:send Taylor --force')->daily();

    $schedule->command(SendEmailsCommand::class, ['Taylor', '--force'])->daily();

<a name="scheduling-queued-jobs"></a>
### Programación de Trabajos en Cola

El método `job` puede usarse para programar un [trabajo en cola](/docs/{{version}}/queues). Este método proporciona una manera conveniente de programar trabajos en cola sin usar el método `call` para definir closures para encolar el trabajo:

    use App\Jobs\Heartbeat;

    $schedule->job(new Heartbeat)->everyFiveMinutes();

Se pueden proporcionar argumentos opcionales segundo y tercero al método `job`, que especifican el nombre de la cola y la conexión de cola que deben usarse para encolar el trabajo:

    use App\Jobs\Heartbeat;

    // Dispatch the job to the "heartbeats" queue on the "sqs" connection...
    $schedule->job(new Heartbeat, 'heartbeats', 'sqs')->everyFiveMinutes();

<a name="scheduling-shell-commands"></a>
### Programación de Comandos Shell

El método `exec` puede usarse para emitir un comando al sistema operativo:

    $schedule->exec('node /home/forge/script.js')->daily();

<a name="schedule-frequency-options"></a>
### Opciones de Frecuencia del Horario

Ya hemos visto algunos ejemplos de cómo puedes configurar una tarea para que se ejecute en intervalos específicos. Sin embargo, hay muchas más frecuencias de programación de tareas que puedes asignar a una tarea:

Método  | Descripción
------------- | -------------
`->cron('* * * * *');`  |  Ejecutar la tarea en un horario personalizado de cron
`->everyMinute();`  |  Ejecutar la tarea cada minuto
`->everyTwoMinutes();`  |  Ejecutar la tarea cada dos minutos
`->everyThreeMinutes();`  |  Ejecutar la tarea cada tres minutos
`->everyFourMinutes();`  |  Ejecutar la tarea cada cuatro minutos
`->everyFiveMinutes();`  |  Ejecutar la tarea cada cinco minutos
`->everyTenMinutes();`  |  Ejecutar la tarea cada diez minutos
`->everyFifteenMinutes();`  |  Ejecutar la tarea cada quince minutos
`->everyThirtyMinutes();`  |  Ejecutar la tarea cada treinta minutos
`->hourly();`  |  Ejecutar la tarea cada hora
`->hourlyAt(17);`  |  Ejecutar la tarea cada hora a los 17 minutos
`->everyOddHour();`  |  Ejecutar la tarea cada hora impar
`->everyTwoHours();`  |  Ejecutar la tarea cada dos horas
`->everyThreeHours();`  |  Ejecutar la tarea cada tres horas
`->everyFourHours();`  |  Ejecutar la tarea cada cuatro horas
`->everySixHours();`  |  Ejecutar la tarea cada seis horas
`->daily();`  |  Ejecutar la tarea todos los días a medianoche
`->dailyAt('13:00');`  |  Ejecutar la tarea todos los días a las 13:00
`->twiceDaily(1, 13);`  |  Ejecutar la tarea dos veces al día a las 1:00 y 13:00
`->twiceDailyAt(1, 13, 15);`  |  Ejecutar la tarea dos veces al día a las 1:15 y 13:15
`->weekly();`  |  Ejecutar la tarea todos los domingos a las 00:00
`->weeklyOn(1, '8:00');`  |  Ejecutar la tarea cada semana el lunes a las 8:00
`->monthly();`  |  Ejecutar la tarea el primer día de cada mes a las 00:00
`->monthlyOn(4, '15:00');`  |  Ejecutar la tarea cada mes el día 4 a las 15:00
`->twiceMonthly(1, 16, '13:00');`  |  Ejecutar la tarea mensualmente el 1 y el 16 a las 13:00
`->lastDayOfMonth('15:00');` | Ejecutar la tarea el último día del mes a las 15:00
`->quarterly();` |  Ejecutar la tarea el primer día de cada trimestre a las 00:00
`->quarterlyOn(4, '14:00');` |  Ejecutar la tarea cada trimestre el día 4 a las 14:00
`->yearly();`  |  Ejecutar la tarea el primer día de cada año a las 00:00
`->yearlyOn(6, 1, '17:00');`  |  Ejecutar la tarea cada año el 1 de junio a las 17:00
`->timezone('America/New_York');` | Establecer la zona horaria para la tarea

Estos métodos pueden combinarse con restricciones adicionales para crear horarios aún más ajustados que solo se ejecuten ciertos días de la semana. Por ejemplo, puedes programar un comando para que se ejecute semanalmente los lunes:

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

A continuación se muestra una lista de restricciones adicionales de programación:

Método  | Descripción
------------- | -------------
`->weekdays();`  |  Limitar la tarea a los días laborables
`->weekends();`  |  Limitar la tarea a los fines de semana
`->sundays();`  |  Limitar la tarea al domingo
`->mondays();`  |  Limitar la tarea al lunes
`->tuesdays();`  |  Limitar la tarea al martes
`->wednesdays();`  |  Limitar la tarea al miércoles
`->thursdays();`  |  Limitar la tarea al jueves
`->fridays();`  |  Limitar la tarea al viernes
`->saturdays();`  |  Limitar la tarea al sábado
`->days(array\|mixed);`  |  Limitar la tarea a días específicos
`->between($startTime, $endTime);`  |  Limitar la tarea para que se ejecute entre la hora de inicio y la hora de fin
`->unlessBetween($startTime, $endTime);`  |  Limitar la tarea para que no se ejecute entre la hora de inicio y la hora de fin
`->when(Closure);`  |  Limitar la tarea basada en una prueba de verdad
`->environments($env);`  |  Limitar la tarea a entornos específicos

<a name="day-constraints"></a>
#### Restricciones por Día

El método `days` puede usarse para limitar la ejecución de una tarea a días específicos de la semana. Por ejemplo, puedes programar un comando para que se ejecute cada hora los domingos y miércoles:

    $schedule->command('emails:send')
                    ->hourly()
                    ->days([0, 3]);

Alternativamente, puedes utilizar las constantes disponibles en la clase `Illuminate\Console\Scheduling\Schedule` al definir los días en los cuales una tarea debería ejecutarse:

    use Illuminate\Console\Scheduling\Schedule;

    $schedule->command('emails:send')
                    ->hourly()
                    ->days([Schedule::SUNDAY, Schedule::WEDNESDAY]);

<a name="between-time-constraints"></a>
#### Restricciones por Horario

El método `between` puede usarse para limitar la ejecución de una tarea según la hora del día:

    $schedule->command('emails:send')
                        ->hourly()
                        ->between('7:00', '22:00');

Del mismo modo, el método `unlessBetween` puede usarse para excluir la ejecución de una tarea durante un período de tiempo:

    $schedule->command('emails:send')
                        ->hourly()
                        ->unlessBetween('23:00', '4:00');

<a name="truth-test-constraints"></a>
#### Restricciones por Prueba de Veracidad

El método `when` puede usarse para limitar la ejecución de una tarea basándose en el resultado de una prueba de veracidad dada. En otras palabras, si el closure proporcionado devuelve `true`, la tarea se ejecutará siempre que no haya otras condiciones restrictivas que impidan la ejecución de la tarea:

    $schedule->command('emails:send')->daily()->when(function () {
        return true;
    });

El método `skip` puede ser visto como el inverso de `when`. Si el método `skip` devuelve `true`, la tarea programada no se ejecutará:

    $schedule->command('emails:send')->daily()->skip(function () {
        return true;
    });

Al usar métodos `when` encadenados, el comando programado se ejecutará solo si todas las condiciones `when` devuelven `true`.

<a name="environment-constraints"></a>
#### Restricciones por Entorno

El método `environments` puede usarse para ejecutar tareas solo en los entornos especificados (según lo definido por la variable de entorno `APP_ENV` [variable de entorno](/docs/{{version}}/configuration#environment-configuration)):

    $schedule->command('emails:send')
                ->daily()
                ->environments(['staging', 'production']);

<a name="timezones"></a>
### Zonas Horarias

Usando el método `timezone`, puedes especificar que la hora de ejecución de una tarea programada debe interpretarse dentro de una zona horaria específica:

    $schedule->command('report:generate')
             ->timezone('America/New_York')
             ->at('2:00')

Si estás asignando repetidamente la misma zona horaria a todas tus tareas programadas, es posible que desees definir un método `scheduleTimezone` en tu clase `App\Console\Kernel`. Este método debería devolver la zona horaria predeterminada que se debe asignar a todas las tareas programadas:

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'America/Chicago';
    }

> **Warning**  
> Recuerda que algunas zonas horarias utilizan el horario de verano. Cuando ocurren los cambios de horario de verano, tu tarea programada puede ejecutarse dos veces o incluso no ejecutarse en absoluto. Por esta razón, recomendamos evitar la programación basada en zonas horarias cuando sea posible.

<a name="preventing-task-overlaps"></a>
### Evitar Superposición de Tareas

Por defecto, las tareas programadas se ejecutarán incluso si la instancia anterior de la tarea aún está en ejecución. Para evitar esto, puedes usar el método `withoutOverlapping`:

    $schedule->command('emails:send')->withoutOverlapping();

En este ejemplo, el comando [Artisan](/docs/{{version}}/artisan) `emails:send` se ejecutará cada minuto si no está en ejecución. El método `withoutOverlapping` es especialmente útil si tienes tareas que varían drásticamente en su tiempo de ejecución, lo que te impide predecir exactamente cuánto tiempo tomará una tarea específica.

Si es necesario, puedes especificar cuántos minutos deben pasar antes de que expire el bloqueo "sin superposición". Por defecto, el bloqueo expirará después de 24 horas.

    $schedule->command('emails:send')->withoutOverlapping(10);

Detrás de escena, el método `withoutOverlapping` utiliza la [caché](/docs/{{version}}/cache) de tu aplicación para obtener bloqueos. Si es necesario, puedes limpiar estos bloqueos de caché utilizando el comando Artisan `schedule:clear-cache`. Normalmente, esto solo es necesario si una tarea queda bloqueada debido a un problema inesperado del servidor.

<a name="running-tasks-on-one-server"></a>
### Ejecución de Tareas en un Solo Servidor

> **Warning**  
> Para utilizar esta característica, tu aplicación debe estar utilizando el controlador de caché `database`, `memcached`, `dynamodb` o `redis` como el controlador de caché predeterminado de la aplicación. Además, todos los servidores deben estar comunicándose con el mismo servidor de caché central.

Si el planificador de tareas de tu aplicación está funcionando en múltiples servidores, puedes limitar una tarea programada para que se ejecute solo en un servidor. Por ejemplo, supón que tienes una tarea programada que genera un nuevo informe cada viernes por la noche. Si el planificador de tareas está ejecutándose en tres servidores trabajadores, la tarea programada se ejecutará en los tres servidores y generará el informe tres veces. ¡No es bueno!

Para indicar que la tarea debe ejecutarse en un solo servidor, utiliza el método `onOneServer` al definir la tarea programada. El primer servidor que obtenga la tarea asegurará un bloqueo atómico en el trabajo para evitar que otros servidores ejecuten la misma tarea al mismo tiempo:

    $schedule->command('report:generate')
                    ->fridays()
                    ->at('17:00')
                    ->onOneServer();

<a name="naming-unique-jobs"></a>
#### Nombrando Trabajos de un Solo Servidor

A veces puede que necesites programar el mismo trabajo para ser despachado con diferentes parámetros, mientras aún instruyes a Laravel para que ejecute cada combinación del trabajo en un solo servidor. Para lograr esto, puedes asignar a cada definición de programación un nombre único mediante el método `name`:

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

De manera similar, los closures programados deben ser asignados un nombre si se pretende que se ejecuten en un solo servidor:

```php
$schedule->call(fn () => User::resetApiRequestCount())
    ->name('reset-api-request-count')
    ->daily()
    ->onOneServer();
```

<a name="background-tasks"></a>
### Tareas en Segundo Plano

Por defecto, varias tareas programadas al mismo tiempo se ejecutarán secuencialmente según el orden en que están definidas en tu método `schedule`. Si tienes tareas de larga duración, esto puede hacer que las tareas siguientes comiencen mucho más tarde de lo anticipado. Si deseas ejecutar tareas en segundo plano para que puedan ejecutarse todas simultáneamente, puedes usar el método `runInBackground`:

    $schedule->command('analytics:report')
             ->daily()
             ->runInBackground();

> **Warning**  
> El método `runInBackground` solo puede utilizarse al programar tareas mediante los métodos `command` y `exec`.

<a name="maintenance-mode"></a>
### Modo de Mantenimiento

Las tareas programadas de tu aplicación no se ejecutarán cuando la aplicación esté en [modo de mantenimiento](/docs/{{version}}/configuration#maintenance-mode), ya que no queremos que tus tareas interfieran con cualquier mantenimiento incompleto que puedas estar realizando en tu servidor. Sin embargo, si deseas forzar que una tarea se ejecute incluso en modo de mantenimiento, puedes llamar al método `evenInMaintenanceMode` al definir la tarea:

    $schedule->command('emails:send')->evenInMaintenanceMode();

<a name="running-the-scheduler"></a>
## Ejecución del Planificador

Ahora que hemos aprendido cómo definir tareas programadas, discutamos cómo ejecutarlas en nuestro servidor. El comando Artisan `schedule:run` evaluará todas tus tareas programadas y determinará si necesitan ejecutarse según la hora actual del servidor.

Por lo tanto, al usar el planificador de Laravel, solo necesitamos agregar una única entrada de configuración de cron a nuestro servidor que ejecute el comando `schedule:run` cada minuto. Si no sabes cómo agregar entradas de cron a tu servidor, considera utilizar un servicio como [Laravel Forge](https://forge.laravel.com), que puede gestionar las entradas de cron por ti.

```shell
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

<a name="running-the-scheduler-locally"></a>
## Ejecutando el Planificador Localmente

Normalmente, no agregarías una entrada cron para el planificador en tu máquina de desarrollo local. En su lugar, puedes usar el comando Artisan `schedule:work`. Este comando se ejecutará en primer plano e invocará el planificador cada minuto hasta que termines el comando:

```shell
php artisan schedule:work
```

<a name="task-output"></a>
## Salida de Tareas

El planificador de Laravel proporciona varios métodos convenientes para trabajar con la salida generada por las tareas programadas. Primero, utilizando el método `sendOutputTo`, puedes enviar la salida a un archivo para inspección posterior:

    $schedule->command('emails:send')
             ->daily()
             ->sendOutputTo($filePath);

Si deseas agregar la salida a un archivo dado, puedes utilizar el método `appendOutputTo`:

    $schedule->command('emails:send')
             ->daily()
             ->appendOutputTo($filePath);

Utilizando el método `emailOutputTo`, puedes enviar por correo electrónico la salida a una dirección de correo de tu elección. Antes de enviar por correo electrónico la salida de una tarea, debes configurar los [servicios de correo electrónico](/docs/{{version}}/mail) de Laravel:

    $schedule->command('report:generate')
             ->daily()
             ->sendOutputTo($filePath)
             ->emailOutputTo('taylor@example.com');

Si solo deseas enviar por correo electrónico la salida si el comando Artisan programado o del sistema termina con un código de salida distinto de cero, utiliza el método `emailOutputOnFailure`:

    $schedule->command('report:generate')
             ->daily()
             ->emailOutputOnFailure('taylor@example.com');

> **Warning**  
> Los métodos `emailOutputTo`, `emailOutputOnFailure`, `sendOutputTo` y `appendOutputTo` son exclusivos de los métodos `command` y `exec`.

<a name="task-hooks"></a>
## Ganchos de Tareas

Utilizando los métodos `before` y `after`, puedes especificar código que se ejecutará antes y después de que se ejecute la tarea programada:

    $schedule->command('emails:send')
             ->daily()
             ->before(function () {
                 // The task is about to execute...
             })
             ->after(function () {
                 // The task has executed...
             });

Los métodos `onSuccess` y `onFailure` te permiten especificar código que se ejecutará si la tarea programada tiene éxito o falla. Un fallo indica que el comando Artisan o del sistema programado terminó con un código de salida distinto de cero:

    $schedule->command('emails:send')
             ->daily()
             ->onSuccess(function () {
                 // The task succeeded...
             })
             ->onFailure(function () {
                 // The task failed...
             });

Si la salida está disponible desde tu comando, puedes acceder a ella en tus ganchos `after`, `onSuccess` o `onFailure` tipificando una instancia de `Illuminate\Support\Stringable` como el argumento `$output` en la definición del cierre de tu gancho:

    use Illuminate\Support\Stringable;

    $schedule->command('emails:send')
             ->daily()
             ->onSuccess(function (Stringable $output) {
                 // The task succeeded...
             })
             ->onFailure(function (Stringable $output) {
                 // The task failed...
             });

<a name="pinging-urls"></a>
#### Pings a URLs

Utilizando los métodos `pingBefore` y `thenPing`, el planificador puede enviar automáticamente una solicitud a una URL especificada antes o después de que se ejecute una tarea. Este método es útil para notificar a un servicio externo, como [Envoyer](https://envoyer.io), que tu tarea programada está comenzando o ha terminado de ejecutarse:

    $schedule->command('emails:send')
             ->daily()
             ->pingBefore($url)
             ->thenPing($url);

Los métodos `pingBeforeIf` y `thenPingIf` se pueden usar para enviar una solicitud a una URL especificada solo si una condición dada es `true`:

    $schedule->command('emails:send')
             ->daily()
             ->pingBeforeIf($condition, $url)
             ->thenPingIf($condition, $url);

Los métodos `pingOnSuccess` y `pingOnFailure` se pueden usar para enviar una solicitud a una URL especificada solo si la tarea tiene éxito o falla. Un fallo indica que el comando Artisan o del sistema programado terminó con un código de salida distinto de cero:

    $schedule->command('emails:send')
             ->daily()
             ->pingOnSuccess($successUrl)
             ->pingOnFailure($failureUrl);

Todos los métodos de ping requieren la biblioteca HTTP Guzzle. Guzzle generalmente se instala de forma predeterminada en todos los nuevos proyectos de Laravel, pero puedes instalarlo manualmente en tu proyecto usando el administrador de paquetes Composer si se ha eliminado accidentalmente:

```shell
composer require guzzlehttp/guzzle
```

<a name="events"></a>
## Eventos

Si es necesario, puedes escuchar [eventos](/docs/{{version}}/events) despachados por el planificador. Normalmente, los mapas de escuchadores de eventos se definen dentro de la clase `App\Providers\EventServiceProvider` de tu aplicación:

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
