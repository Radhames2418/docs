# Laravel Envoy

- [Introducción](#introduction)
- [Instalación](#installation)
- [Escribir tareas](#writing-tasks)
  - [Definición de tareas](#defining-tasks)
  - [Múltiples servidores](#multiple-servers)
  - [Configuración](#setup)
  - [Variables](#variables)
  - [Historias](#stories)
  - [Ganchos](#completion-hooks)
- [Ejecución de tareas](#running-tasks)
  - [Confirmación de la ejecución de tareas](#confirming-task-execution)
- [Notificaciones](#notifications)
  - [Slack](#slack)
  - [Discord](#discord)
  - [Telegrama](#telegram)
  - [Microsoft Teams](#microsoft-teams)

[]()

## Introducción

[Laravel Envoy](https://github.com/laravel/envoy) es una herramienta para ejecutar tareas comunes que ejecutas en tus servidores remotos. Usando sintaxis estilo [Blade](/docs/%7B%7Bversion%7D%7D/blade), puedes configurar fácilmente tareas para despliegue, comandos Artisan, y más. Actualmente, Envoy sólo soporta los sistemas operativos Mac y Linux. Sin embargo, el soporte de Windows es posible utilizando [WSL2](https://docs.microsoft.com/en-us/windows/wsl/install-win10).

[]()

## Instalación

En primer lugar, instale Envoy en su proyecto utilizando el gestor de paquetes Composer:

```shell
composer require laravel/envoy --dev
```

Una vez instalado Envoy, el binario de Envoy estará disponible en el directorio `vendor/bin` de su aplicación:

```shell
php vendor/bin/envoy
```

[]()

## Escribiendo Tareas

[]()

### Definición de tareas

Las tareas son el componente básico de Envoy. Las tareas definen los comandos shell que deben ejecutarse en sus servidores remotos cuando se invoca la tarea. Por ejemplo, puede definir una tarea que ejecute el comando `php artisan queue:restart` en todos los servidores de cola de su aplicación.

Todas sus tareas Envoy deben ser definidas en un archivo `Envoy.blade.` php en la raíz de su aplicación. Aquí tiene un ejemplo para empezar:

```blade
@servers(['web' => ['user@192.168.1.1'], 'workers' => ['user@192.168.1.2']])

@task('restart-queues', ['on' => 'workers'])
    cd /home/user/example.com
    php artisan queue:restart
@endtask
```

Como puede ver, se define una array de `@servers` en la parte superior del archivo, lo que le permite hacer referencia a estos servidores a través de la opción `on` de sus declaraciones de tareas. La declaración `@servers` debe colocarse siempre en una sola línea. Dentro de sus declaraciones `@task`, debe colocar los comandos shell que deben ejecutarse en sus servidores cuando la tarea es invocada.

[]()

#### Tareas locales

Puede forzar la ejecución de un script en su ordenador local especificando la dirección IP del servidor como `127.0.0.1`:

```blade
@servers(['localhost' => '127.0.0.1'])
```

[]()

#### Importando Tareas Envoy

Utilizando la directiva `@import`, puede importar otros archivos Envoy para que sus historias y tareas se añadan a las suyas. Una vez importados los archivos, puede ejecutar las tareas que contienen como si estuvieran definidas en su propio archivo Envoy:

```blade
@import('vendor/package/Envoy.blade.php')
```

[]()

### Múltiples Servidores

Envoy le permite ejecutar fácilmente una tarea en varios servidores. En primer lugar, añada servidores adicionales a su declaración `@servers`. A cada servidor debe asignársele un nombre único. Una vez que haya definido sus servidores adicionales, puede listar cada uno de los servidores en la array `on` de la tarea:

```blade
@servers(['web-1' => '192.168.1.1', 'web-2' => '192.168.1.2'])

@task('deploy', ['on' => ['web-1', 'web-2']])
    cd /home/user/example.com
    git pull origin {{ $branch }}
    php artisan migrate --force
@endtask
```

[]()

#### Ejecución Paralela

Por defecto, las tareas se ejecutarán en cada servidor en serie. En otras palabras, una tarea terminará de ejecutarse en el primer servidor antes de proceder a ejecutarse en el segundo servidor. Si desea ejecutar una tarea en varios servidores en paralelo, añada la opción `paralelo` a la declaración de la tarea:

```blade
@servers(['web-1' => '192.168.1.1', 'web-2' => '192.168.1.2'])

@task('deploy', ['on' => ['web-1', 'web-2'], 'parallel' => true])
    cd /home/user/example.com
    git pull origin {{ $branch }}
    php artisan migrate --force
@endtask
```

[]()

### Configurar

A veces, puede que necesite ejecutar código PHP arbitrario antes de ejecutar sus tareas Envoy. Puede utilizar la directiva `@setup` para definir un bloque de código PHP que debería ejecutarse antes de sus tareas:

```php
@setup
    $now = new DateTime;
@endsetup
```

Si necesita requerir otros archivos PHP antes de que se ejecute su tarea, puede utilizar la directiva `@include` en la parte superior de su archivo `Envoy.blade.php`:

```blade
@include('vendor/autoload.php')

@task('restart-queues')
    # ...
@endtask
```

[]()

### Variables

Si es necesario, puede pasar argumentos a las tareas de Envoy especificándolos en la línea de comandos al invocar Envoy:

```shell
php vendor/bin/envoy run deploy --branch=master
```

Puede acceder a las opciones dentro de sus tareas utilizando la sintaxis "echo" de Blade. También puede definir sentencias `if` y bucles Blade dentro de sus tareas. Por ejemplo, verifiquemos la presencia de la variable `$branch` antes de ejecutar el comando `git pull`:

```blade
@servers(['web' => ['user@192.168.1.1']])

@task('deploy', ['on' => 'web'])
    cd /home/user/example.com

    @if ($branch)
        git pull origin {{ $branch }}
    @endif

    php artisan migrate --force
@endtask
```

[]()

### Historias

Las historias agrupan un conjunto de tareas bajo un único y conveniente nombre. Por ejemplo, una historia de `despliegue` puede ejecutar las tareas `update-code` e `install-dependencies` enumerando los nombres de las tareas en su definición:

```blade
@servers(['web' => ['user@192.168.1.1']])

@story('deploy')
    update-code
    install-dependencies
@endstory

@task('update-code')
    cd /home/user/example.com
    git pull origin master
@endtask

@task('install-dependencies')
    cd /home/user/example.com
    composer install
@endtask
```

Una vez escrita la historia, puede invocarla del mismo modo que invocaría una tarea:

```shell
php vendor/bin/envoy run deploy
```

[]()

### Ganchos

Cuando se ejecutan tareas e historias, se ejecutan una serie de ganchos. Los tipos de ganchos soportados por Envoy son `@before`, `@after`, `@error`, `@success` y `@finished`. Todo el código de estos ganchos se interpreta como PHP y se ejecuta localmente, no en los servidores remotos con los que interactúan las tareas.

Puede definir tantos ganchos como desee. Se ejecutarán en el orden en que aparezcan en su script de Envoy.

[]()

#### `@antes`

Antes de la ejecución de cada tarea, se ejecutarán todos los ganchos `@before` registrados en su script de Envoy. Los ganchos `@before` reciben el nombre de la tarea que se va a ejecutar:

```blade
@before
    if ($task === 'deploy') {
        // ...
    }
@endbefore
```

[]()

#### `@after`

Después de la ejecución de cada tarea, se ejecutarán todos los ganchos `@after` registrados en el script de Envoy. Los ganchos `@after` reciben el nombre de la tarea que se ha ejecutado:

```blade
@after
    if ($task === 'deploy') {
        // ...
    }
@endafter
```

[]()

#### `@error`

Después de cada fallo de tarea (salidas con un código de estado superior a `0`), se ejecutarán todos los ganchos `@error` registrados en su script de Envoy. Los ganchos `@error` reciben el nombre de la tarea que se ha ejecutado:

```blade
@error
    if ($task === 'deploy') {
        // ...
    }
@enderror
```

[]()

#### `@éxito`

Si todas las tareas se han ejecutado sin errores, se ejecutarán todos los ganchos `@success` registrados en su script de Envoy:

```blade
@success
    // ...
@endsuccess
```

[]()

#### `@terminado`

Una vez ejecutadas todas las tareas (independientemente del estado de salida), se ejecutarán todos los ganchos `@finished`. Los ganchos `@finished` reciben el código de estado de la tarea completada, que puede ser `nulo` o un `número entero` mayor o igual que `0`:

```blade
@finished
    if ($exitCode > 0) {
        // There were errors in one of the tasks...
    }
@endfinished
```

[]()

## Ejecución de tareas

Para ejecutar una tarea o historia definida en el archivo `Envoy.blade.php` de su aplicación, ejecute el comando de `ejecución` de Envoy, introduciendo el nombre de la tarea o historia que desea ejecutar. Envoy ejecutará la tarea y mostrará los resultados de sus servidores remotos mientras se ejecuta la tarea:

```shell
php vendor/bin/envoy run deploy
```

[]()

### Confirmación de la ejecución de tareas

Si desea que se le pida confirmación antes de ejecutar una tarea determinada en sus servidores, deberá añadir la directiva `confirm` a la declaración de su tarea. Esta opción es especialmente útil para operaciones destructivas:

```blade
@task('deploy', ['on' => 'web', 'confirm' => true])
    cd /home/user/example.com
    git pull origin {{ $branch }}
    php artisan migrate
@endtask
```

[]()

## Notificaciones

[]()

### Slack

Envoy admite el envío de notificaciones a [Slack](https://slack.com) tras la ejecución de cada tarea. La directiva `@slack` acepta una URL de gancho Slack y un canal / nombre de usuario. Puede recuperar la URL de su webhook creando una integración "Incoming WebHooks" en su panel de control de Slack.

Debe pasar la URL completa del webhook como primer argumento de la directiva `@slack`. El segundo argumento dado a la directiva `@slack` debe ser un nombre de canal`(#channel`) o un nombre de usuario`(@user`):

```blade
@finished
    @slack('webhook-url', '#bots')
@endfinished
```

Por defecto, las notificaciones de Envoy enviarán un mensaje al canal de notificación describiendo la tarea que se ha ejecutado. Sin embargo, puede sobrescribir este mensaje con su propio mensaje personalizado pasando un tercer argumento a la directiva `@slack`:

```blade
@finished
    @slack('webhook-url', '#bots', 'Hello, Slack.')
@endfinished
```

[]()

### Discord

Envoy también soporta el envío de notificaciones a [Discord](https://discord.com) después de la ejecución de cada tarea. La directiva `@discord` acepta una URL de gancho de Discord y un mensaje. Puede recuperar la URL de su webhook creando un "Webhook" en la Configuración de su Servidor y eligiendo a qué canal debe enviar el webhook. Debe pasar la URL completa del Webhook a la directiva `@discord`:

```blade
@finished
    @discord('discord-webhook-url')
@endfinished
```

[]()

### Telegram

Envoy también soporta el envío de notificaciones a [Telegram](https://telegram.org) tras la ejecución de cada tarea. La directiva `@telegram` acepta un ID de Bot de Telegram y un ID de Chat. Puedes recuperar tu Bot ID creando un nuevo bot usando [BotFather](https://t.me/botfather). Puedes recuperar un ID de Chat válido usando [@username_to_id_bot](https://t.me/username_to_id_bot). Debe pasar el Bot ID y el Chat ID completos a la directiva `@telegram`:

```blade
@finished
    @telegram('bot-id','chat-id')
@endfinished
```

[]()

### Microsoft Teams

Envoy también admite el envío de notificaciones a [Microsoft Teams](https://www.microsoft.com/en-us/microsoft-teams) después de la ejecución de cada tarea. La directiva `@microsoftTeams` acepta un Teams Webhook (obligatorio), un mensaje, un color de tema (éxito, información, advertencia, error) y una array de opciones. Puede recuperar su Teams Webhook creando un nuevo [webhook entrante](https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook). La API de Teams dispone de muchos otros atributos para personalizar su cuadro de mensaje, como el título, el resumen y las secciones. Puedes encontrar más información en la [documentación de Microsoft Teams](https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/connectors-using?tabs=cURL#example-of-connector-message). Debes pasar la URL completa del Webhook en la directiva `@microsoftTeams`:

```blade
@finished
    @microsoftTeams('webhook-url')
@endfinished
```
