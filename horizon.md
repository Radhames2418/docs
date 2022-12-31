# Laravel Horizon

- [Introducción](#introduction)
- [Instalación](#installation)
  - [Configuración](#configuration)
  - [Estrategias de equilibrio](#balancing-strategies)
  - [Autorización del Cuadro de Mando](#dashboard-authorization)
- [Actualización de Horizon](#upgrading-horizon)
- [Ejecución de Horizon](#running-horizon)
  - [Despliegue de Horizon](#deploying-horizon)
- [Etiquetas](#tags)
- [Notificaciones](#notifications)
- [Métricas](#metrics)
- [Eliminación de trabajos fallidos](#deleting-failed-jobs)
- [Borrado de trabajos de las colas](#clearing-jobs-from-queues)

[]()

## Introducción

> **Nota**  
> Antes de profundizar en Laravel Horizon, deberías familiarizarte con los [servicios de](/docs/%7B%7Bversion%7D%7D/queues) cola base de Laravel. Horizon aumenta la cola de Laravel con características adicionales que pueden ser confusas si no estás familiarizado con las características básicas de la cola ofrecidas por Laravel.

[Laravel Horizon](https://github.com/laravel/horizon) proporciona un bonito panel de control y una configuración basada en código para las colas [Redis](/docs/%7B%7Bversion%7D%7D/queues) de Laravel. Horizon te permite monitorizar fácilmente las métricas clave de tu sistema de colas, como el rendimiento de los trabajos, el tiempo de ejecución y los fallos de los trabajos.

Al utilizar Horizon, toda la configuración de los trabajadores en cola se almacena en un único y sencillo archivo de configuración. Al definir la configuración de los trabajadores de su aplicación en un archivo de versión controlada, puede escalar o modificar fácilmente los trabajadores de cola de su aplicación al desplegarla.

<img src="https://laravel.com/img/docs/horizon-example.png"/>

[]()

## Instalación

> **Advertencia**  
> Laravel Horizon requiere que utilices [Redis](https://redis.io) para alimentar tu cola. Por lo tanto, debe asegurarse de que su conexión de cola se establece en `redis` en el archivo de configuración `config/queue.php` de su aplicación.

Puede instalar Horizon en su proyecto utilizando el gestor de paquetes Composer:

```shell
composer require laravel/horizon
```

Después de instalar Horizon, publica sus activos utilizando el comando `horizon:install` Artisan:

```shell
php artisan horizon:install
```

[]()

### Configuración

Después de publicar los activos de Horizon, su archivo de configuración principal se encontrará en `config/horizon.php`. Este archivo de configuración te permite configurar las opciones del trabajador de colas para tu aplicación. Cada opción de configuración incluye una descripción de su propósito, así que asegúrese de explorar a fondo este archivo.

> **Advertencia**  
> Horizon utiliza una conexión Redis llamada `horizon` internamente. Este nombre de conexión Redis está reservado y no debe ser asignado a otra conexión Redis en el archivo de configuración `database.` php o como el valor de la opción `use` en el archivo de configuración `horizon.php`.

[]()

#### Entornos

Tras la instalación, la principal opción de configuración de Horizon con la que debe familiarizarse es la opción de configuración de `entornos`. Esta opción de configuración es una array de entornos en los que se ejecuta su aplicación y define las opciones del proceso de trabajador para cada entorno. Por defecto, esta entrada contiene un entorno de `producción` y un entorno `local`. Sin embargo, eres libre de añadir más entornos según sea necesario:

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'maxProcesses' => 10,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'maxProcesses' => 3,
            ],
        ],
    ],

Cuando inicie Horizon, utilizará las opciones de configuración del proceso de trabajador para el entorno en el que se está ejecutando su aplicación. Normalmente, el entorno viene determinado por el valor de la [variable de entorno](/docs/%7B%7Bversion%7D%7D/configuration#determining-the-current-environment) `APP_ENV`. Por ejemplo, el entorno `local` predeterminado de Horizon está configurado para iniciar tres procesos de trabajador y equilibrar automáticamente el número de procesos de trabajador asignados a cada cola. El entorno `de producción` predeterminado está configurado para iniciar un máximo de 10 procesos de trabajador y equilibrar automáticamente el número de procesos de trabajador asignados a cada cola.

> **Advertencia**  
> Debe asegurarse de que la parte de `entornos` de su archivo de configuración `de` Horizon contiene una entrada para cada [entorno](/docs/%7B%7Bversion%7D%7D/configuration#environment-configuration) en el que planea ejecutar Horizon.

[]()

#### Supervisores

Como puede ver en el archivo de configuración por defecto de Horizon, cada entorno puede contener uno o más "supervisores". Por defecto, el archivo de configuración define este supervisor como `supervisor-1`; sin embargo, usted es libre de nombrar a sus supervisores como desee. Cada supervisor es esencialmente responsable de "supervisar" un grupo de procesos de trabajador y se encarga de equilibrar los procesos de trabajador en las colas.

Puedes añadir supervisores adicionales a un entorno dado si quieres definir un nuevo grupo de procesos de trabajador que deberían ejecutarse en ese entorno. Puede hacer esto si desea definir una estrategia de balanceo o un número de procesos de trabajo diferente para una cola determinada utilizada por su aplicación.

[]()

#### Valores por defecto

Dentro del archivo de configuración por defecto de Horizon, observará una opción de configuración `por defecto`. Esta opción de configuración especifica los valores predeterminados para los [supervisores](#supervisors) de su aplicación. Los valores de configuración predeterminados del supervisor se fusionarán en la configuración del supervisor para cada entorno, lo que le permitirá evitar repeticiones innecesarias al definir sus supervisores.

[]()

### Estrategias de equilibrio

A diferencia del sistema de colas por defecto de Laravel, Horizon permite elegir entre tres estrategias de balanceo de trabajadores: `simple`, `auto` y `false`. La estrategia `simple`, que es la predeterminada en el archivo de configuración, divide los trabajos entrantes de manera uniforme entre los procesos de los trabajadores:

    'balance' => 'simple',

La estrategia `automática` ajusta el número de procesos de trabajo por cola en función de la carga de trabajo actual de la cola. Por ejemplo, si su cola de `notificaciones` tiene 1.000 trabajos pendientes mientras que su cola de `renderizado` está vacía, Horizon asignará más trabajadores a su cola de `notificaciones` hasta que la cola esté vacía.

Al utilizar la estrategia `automática`, puede definir las opciones de configuración `minProcesses` y `maxProcesses` para controlar el número mínimo y máximo de procesos de trabajadores a los que Horizon debe escalar:

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 10,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
                'tries' => 3,
            ],
        ],
    ],

Los valores de configuración `balanceMaxShift` y `balanceCooldown` determinan la rapidez con la que Horizon escalará para satisfacer la demanda de los trabajadores. En el ejemplo anterior, se creará o destruirá como máximo un nuevo proceso cada tres segundos. Puede ajustar estos valores según sea necesario en función de las necesidades de su aplicación.

Cuando la opción de `balance` se establece en `false`, se utilizará el comportamiento por defecto de Laravel, en el que las colas se procesan en el orden en que se enumeran en la configuración.

[]()

### Autorización del panel de control

Horizon expone un cuadro de mandos en el URI `/horizon`. Por defecto, sólo podrá acceder a este panel en el entorno `local`. Sin embargo, dentro de su archivo `app/Providers/HorizonServiceProvider.` php, hay una definición de [gate](/docs/%7B%7Bversion%7D%7D/authorization#gates) de autorización. Esta gate de autorización controla el acceso a Horizon en entornos **no locales**. Usted es libre de modificar esta gate según sea necesario para restringir el acceso a su instalación Horizon:

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewHorizon', function ($user) {
            return in_array($user->email, [
                'taylor@laravel.com',
            ]);
        });
    }

[]()

#### Estrategias alternativas de autenticación

Recuerde que Laravel inyecta automáticamente el usuario autenticado en el closure gate puerta. Si su aplicación está proporcionando seguridad Horizon a través de otro método, como las restricciones de IP, entonces sus usuarios Horizon pueden no necesitar "iniciar sesión". Por lo tanto, usted tendrá que cambiar la `función ($user)` firma de closure anterior a la `función ($user = null)` con el fin de forzar Laravel para no requerir autenticación.

[]()

## Actualización de Horizon

Al actualizar a una nueva versión principal de Horizon, es importante que revise cuidadosamente [la guía de actualización](https://github.com/laravel/horizon/blob/master/UPGRADE.md). Además, al actualizar a cualquier nueva versión de Horizon, debe volver a publicar los activos de Horizon:

```shell
php artisan horizon:publish
```

Para mantener los activos actualizados y evitar problemas en futuras actualizaciones, puedes añadir el comando `horizon:publish` a los scripts `post-update-cmd` en el archivo `composer.json` de tu aplicación:

```json
{
    "scripts": {
        "post-update-cmd": [
            "@php artisan horizon:publish --ansi"
        ]
    }
}
```

[]()

## Ejecución de Horizon

Una vez que hayas configurado tus supervisores y trabajadores en el archivo de configuración `config/horizon.php` de tu aplicación, puedes iniciar Horizon utilizando el comando `horizon` Artisan. Este único comando iniciará todos los procesos de trabajadores configurados para el entorno actual:

```shell
php artisan horizon
```

Puedes pausar el proceso Horizon e indicarle que continúe procesando trabajos utilizando los comandos horizon `:pause` y `horizon:continue` de Artisan:

```shell
php artisan horizon:pause

php artisan horizon:continue
```

También puede pausar y continuar [supervisores](#supervisors) Horizon específicos utilizando los comandos Artisan `horizon:pause-supervisor` y `horizon:continue-supervisor`:

```shell
php artisan horizon:pause-supervisor supervisor-1

php artisan horizon:continue-supervisor supervisor-1
```

Puede comprobar el estado actual del proceso Horizon mediante el comando horizon `:status` de Artisan:

```shell
php artisan horizon:status
```

Puede finalizar el proceso Horizon con el comando horizon `:terminate` de Artisan. Cualquier trabajo que esté siendo procesado por se completará y entonces Horizon dejará de ejecutarse:

```shell
php artisan horizon:terminate
```

[]()

### Despliegue de Horizon

Cuando estés listo para desplegar Horizon en el servidor real de tu aplicación, debes configurar un monitor de procesos para monitorear el comando `php artisan` horizon y reiniciarlo si sale inesperadamente. No se preocupe, más adelante explicaremos cómo instalar un monitor de procesos.

Durante el proceso de despliegue de su aplicación, debe instruir al proceso Horizon para que termine de modo que sea reiniciado por su monitor de procesos y reciba sus cambios de código:

```shell
php artisan horizon:terminate
```

[]()

#### Instalación de Supervisor

Supervisor es un monitor de procesos para el sistema operativo Linux y reiniciará automáticamente su proceso `Horizon` si deja de ejecutarse. Para instalar Supervisor en Ubuntu, puede utilizar el siguiente comando. Si no utiliza Ubuntu, puede instalar Supervisor utilizando el gestor de paquetes de su sistema operativo:

```shell
sudo apt-get install supervisor
```

> **Nota**  
> Si configurar Supervisor usted mismo suena abrumador, considere el uso de [Laravel Forge](https://forge.laravel.com), que instalará y configurará automáticamente Supervisor para sus proyectos Laravel.

[]()

#### Configuración de Supervisor

Los archivos de configuración de Supervisor se almacenan normalmente en el directorio `/etc/supervisor/conf.d` de su servidor. Dentro de este directorio, puede crear cualquier número de archivos de configuración que indiquen a Supervisor cómo deben ser monitorizados sus procesos. Por ejemplo, creemos un fichero `horizon.` conf que inicie y monitorice un proceso `horizon`:

```ini
[program:horizon]
process_name=%(program_name)s
command=php /home/forge/example.com/artisan horizon
autostart=true
autorestart=true
user=forge
redirect_stderr=true
stdout_logfile=/home/forge/example.com/horizon.log
stopwaitsecs=3600
```

Cuando defina su configuración de Supervisor, debe asegurarse de que el valor de `stopwaitsecs` es mayor que el número de segundos consumidos por su trabajo en ejecución más largo. De lo contrario, Supervisor puede matar el trabajo antes de que termine de procesarse.

> **Advertencia**  
> Aunque los ejemplos anteriores son válidos para servidores basados en Ubuntu, la ubicación y extensión de los archivos de configuración de Supervisor pueden variar entre otros sistemas operativos de servidor. Por favor, consulte la documentación de su servidor para más información.

[]()

#### Arrancar el Supervisor

Una vez creado el archivo de configuración, puede actualizar la configuración de Supervisor e iniciar los procesos monitorizados utilizando los siguientes comandos:

```shell
sudo supervisorctl reread

sudo supervisorctl update

sudo supervisorctl start horizon
```

> **Nota**  
> Para más información sobre la ejecución de Supervisor, consulte la [documentación de Supervisor](http://supervisord.org/index.html).

[]()

## Etiquetas

Horizon le permite asignar "etiquetas" a los trabajos, incluyendo mailables, eventos de difusión, notificaciones y escuchas de eventos en cola. De hecho, Horizon etiquetará de forma inteligente y automática la mayoría de los trabajos en función de los modelos de Eloquent que se adjunten al trabajo. Por ejemplo, eche un vistazo al siguiente trabajo:

    <?php

    namespace App\Jobs;

    use App\Models\Video;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;

    class RenderVideo implements ShouldQueue
    {
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        /**
         * The video instance.
         *
         * @var \App\Models\Video
         */
        public $video;

        /**
         * Create a new job instance.
         *
         * @param  \App\Models\Video  $video
         * @return void
         */
        public function __construct(Video $video)
        {
            $this->video = $video;
        }

        /**
         * Execute the job.
         *
         * @return void
         */
        public function handle()
        {
            //
        }
    }

Si este trabajo se pone en cola con una instancia `App\Models\Video` que tiene un atributo `id` de `1`, recibirá automáticamente la etiqueta `App\Models\Video:1`. Esto se debe a que Horizon buscará en las propiedades del trabajo cualquier modelo Eloquent. Si se encuentran modelos Eloquent, Horizon etiquetará el trabajo de forma inteligente utilizando el nombre de clase y la clave principal del modelo:

    use App\Jobs\RenderVideo;
    use App\Models\Video;

    $video = Video::find(1);

    RenderVideo::dispatch($video);

[]()

#### Etiquetado manual de trabajos

Si desea definir manualmente las etiquetas para uno de sus objetos encolables, puede definir un método de `etiquetas` en la clase:

    class RenderVideo implements ShouldQueue
    {
        /**
         * Get the tags that should be assigned to the job.
         *
         * @return array
         */
        public function tags()
        {
            return ['render', 'video:'.$this->video->id];
        }
    }

[]()

## Notificaciones

> **Advertencia**  
> Al configurar Horizon para enviar notificaciones Slack o SMS, debe revisar los [requisitos previos para el canal de notificación correspondiente](/docs/%7B%7Bversion%7D%7D/notifications).

Si desea recibir una notificación cuando una de sus colas tenga un tiempo de espera prolongado, puede utilizar los métodos Horizon:: `routeMailNotificationsTo`, `Horizon::routeSlackNotificationsTo` y `Horizon::routeSmsNotificationsTo`. Puede llamar a estos métodos desde el método de `arranque` del `App\Providers\HorizonServiceProvider` de su aplicación:

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Horizon::routeSmsNotificationsTo('15556667777');
        Horizon::routeMailNotificationsTo('example@example.com');
        Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

[]()

#### Configuración de umbrales de tiempo de espera de notificación

Puede configurar cuántos segundos se consideran una "espera larga" en el archivo de configuración `config/horizon.php` de su aplicación. La opción de configuración de `esperas` dentro de este archivo le permite controlar el umbral de espera larga para cada combinación de conexión / cola:

    'waits' => [
        'redis:default' => 60,
        'redis:critical,high' => 90,
    ],

[]()

## Métricas

Horizon incluye un panel de métricas que proporciona información sobre los tiempos de espera y el rendimiento de los trabajos y las colas. Con el fin de poblar este tablero de instrumentos, debe configurar la `instantánea` de Horizon Artisan comando para que se ejecute cada cinco minutos a través del [programador de](/docs/%7B%7Bversion%7D%7D/scheduling) su aplicación:

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

[]()

## Eliminación de trabajos fallidos

Si desea eliminar un trabajo fallido, puede utilizar el comando horizon: `forget`. El comando horizon `:` forget acepta el ID o UUID del trabajo fallido como único argumento:

```shell
php artisan horizon:forget 5
```

[]()

## Eliminación de trabajos de las colas

Si desea eliminar todos los trabajos de la cola por defecto de su aplicación, puede hacerlo utilizando el comando horizon: `clear` Artisan:

```shell
php artisan horizon:clear
```

Puede proporcionar la opción de `cola` para eliminar trabajos de una cola específica:

```shell
php artisan horizon:clear --queue=emails
```
