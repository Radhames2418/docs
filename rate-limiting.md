# Limitación de velocidad

- [Introducción](#introduction)
  - [cache-configuration">Configuración decache](<#\<glossary variable=>)
- [Uso básico](#basic-usage)
  - [Incremento manual de intentos](#manually-incrementing-attempts)
  - [Borrado de intentos](#clearing-attempts)

[]()

## Introducción

Laravel incluye una abstracción de limitación de tasa fácil de usar que, en conjunción con la [cache](cache) de tu aplicación, proporciona una forma sencilla de limitar cualquier acción durante una ventana de tiempo especificada.

> **Nota**  
> Si estás interesado en limitar la tasa de peticiones HTTP entrantes, por favor consulta la [documentación](routing#rate-limiting) del [middleware limitador de tasa](routing#rate-limiting).

[]()

### Configuración decache

Normalmente, el limitador de velocidad utiliza la cache por defecto de su aplicación, definida por la clave `por defecto` dentro del archivo de configuración de `cache` de su aplicación. Sin embargo, puede especificar qué controlador de cache debe utilizar el limitador de velocidad definiendo una clave de `limitador` en el archivo de configuración de `cache` de su aplicación:

    'default' => 'memcached',

    'limiter' => 'redis',

[]()

## Uso Básico

La facade `Illuminate\Support\facades\RateLimiter` puede utilizarse para interactuar con el limitador de velocidad. El método más sencillo ofrecido por el limitador de velocidad es el método `attempt`, que limita la velocidad de una llamada de retorno dada durante un número determinado de segundos.

El método `attempt` devuelve `false` cuando a la llamada de retorno no le quedan intentos disponibles; en caso contrario, el método attempt devolverá el resultado de la llamada de retorno o `true`. El primer argumento aceptado por el método `attempt` es una "clave" de limitación de tasa, que puede ser cualquier cadena de su elección que represente la acción que se está limitando:

    use Illuminate\Support\Facades\RateLimiter;

    $executed = RateLimiter::attempt(
        'send-message:'.$user->id,
        $perMinute = 5,
        function() {
            // Send message...
        }
    );

    if (! $executed) {
      return 'Too many messages sent!';
    }

[]()

### Incremento manual de intentos

Si desea interactuar manualmente con el limitador de velocidad, dispone de otros métodos. Por ejemplo, puede invocar el método `tooManyAttempts` para determinar si una clave de limitador de velocidad dada ha excedido su número máximo de intentos permitidos por minuto:

    use Illuminate\Support\Facades\RateLimiter;

    if (RateLimiter::tooManyAttempts('send-message:'.$user->id, $perMinute = 5)) {
        return 'Too many attempts!';
    }

Alternativamente, puede utilizar el método `remaining` para recuperar el número de intentos restantes para una clave dada. Si una clave dada tiene reintentos restantes, puede invocar el método `hit` para incrementar el número de intentos totales:

    use Illuminate\Support\Facades\RateLimiter;

    if (RateLimiter::remaining('send-message:'.$user->id, $perMinute = 5)) {
        RateLimiter::hit('send-message:'.$user->id);

        // Send message...
    }

[]()

#### Determinación de la disponibilidad del limitador

Cuando a una tecla no le quedan más intentos, el método `availableIn` devuelve el número de segundos que quedan hasta que haya más intentos disponibles:

    use Illuminate\Support\Facades\RateLimiter;

    if (RateLimiter::tooManyAttempts('send-message:'.$user->id, $perMinute = 5)) {
        $seconds = RateLimiter::availableIn('send-message:'.$user->id);

        return 'You may try again in '.$seconds.' seconds.';
    }

[]()

### Borrado de intentos

Puede restablecer el número de intentos para una tecla limitadora de velocidad determinada utilizando el método `clear`. Por ejemplo, puede restablecer el número de intentos cuando el receptor lea un mensaje determinado:

    use App\Models\Message;
    use Illuminate\Support\Facades\RateLimiter;

    /**
     * Mark the message as read.
     *
     * @param  \App\Models\Message  $message
     * @return \App\Models\Message
     */
    public function read(Message $message)
    {
        $message->markAsRead();

        RateLimiter::clear('send-message:'.$message->user_id);

        return $message;
    }
