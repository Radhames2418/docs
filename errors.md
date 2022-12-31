# Manejo de Errores

- [Introducción](#introduction)
- [Configuración](#configuration)
- [El gestor de excepciones](#the-exception-handler)
  - [Notificación de excepciones](#reporting-exceptions)
  - [Niveles de registro de excepciones](#exception-log-levels)
  - [Ignorando Excepciones por Tipo](#ignoring-exceptions-by-type)
  - [Renderización de Excepciones](#rendering-exceptions)
  - [Excepciones Reportables y Renderizables](#renderable-exceptions)
- [Excepciones HTTP](#http-exceptions)
  - [Páginas de error HTTP personalizadas](#custom-http-error-pages)

[]()

## Introducción

Cuando se inicia un nuevo proyecto Laravel, el manejo de errores y excepciones ya está configurado. La clase `App\Exceptions\Handler` es donde todas las excepciones lanzadas por su aplicación se registran y luego se muestran al usuario. Vamos a profundizar en esta clase a lo largo de esta documentación.

[]()

## Configuración

La opción `debug` en su archivo de configuración `config/app.` php determina cuanta información sobre un error es mostrada al usuario. Por defecto, esta opción está configurada para respetar el valor de la variable de entorno `APP_DEBUG`, que está almacenada en su archivo `.env`.

Durante el desarrollo local, debe establecer la variable de entorno `APP_DEBUG` en `true`. **En su entorno de producción, este valor debe ser siempre `false`. Si el valor se establece en `true` en producción, se arriesga a exponer valores de configuración sensibles a los usuarios finales de su aplicación.**

[]()

## El Manejador de Excepciones

[]()

### Informe de Excepciones

Todas las excepciones son manejadas por la clase `AppExceptionsHandler`. Esta clase contiene un método `register` donde puedes registrar informes de excepciones personalizados y callbacks de renderizado. Examinaremos cada uno de estos conceptos en detalle. Los informes de excepciones se utilizan para registrar excepciones o enviarlas a un servicio externo como [Flare](https://flareapp.io), [Bugsnag](https://bugsnag.com) o [Sentry](https://github.com/getsentry/sentry-laravel). Por defecto, las excepciones serán registradas en base a su configuración de [registro](/docs/%7B%7Bversion%7D%7D/logging). No obstante, puede registrar las excepciones como desee.

Por ejemplo, si necesitas reportar diferentes tipos de excepciones de diferentes maneras, puedes usar el método `reportable` para registrar un closure que debe ser ejecutado cuando una excepción de un tipo dado necesita ser reportada. Laravel deducirá qué tipo de excepción reporta el closure examinando el type-hint del closure:

    use App\Exceptions\InvalidOrderException;

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (InvalidOrderException $e) {
            //
        });
    }

Cuando registres un callback personalizado para reportar excepciones utilizando el método `reportable`, Laravel seguirá registrando la excepción utilizando la configuración de registro por defecto para la aplicación. Si deseas detener la propagación de la excepción a la pila de registro por defecto, puedes utilizar el método `stop` cuando definas tu callback de informe o devolver `false` desde el callback:

    $this->reportable(function (InvalidOrderException $e) {
        //
    })->stop();

    $this->reportable(function (InvalidOrderException $e) {
        return false;
    });

> **Nota**  
> Para personalizar el informe de excepciones para una excepción determinada, también puede utilizar [excepciones notificables](/docs/%7B%7Bversion%7D%7D/errors#renderable-exceptions).

[]()

#### Contexto de Registro Global

Si está disponible, Laravel añade automáticamente el ID del usuario actual al mensaje de registro de cada excepción como datos contextuales. Puedes definir tus propios datos contextuales globales sobrescribiendo el método `context` de la clase `App\Exceptions\Handler` de tu aplicación. Esta información será incluida en cada mensaje de registro de excepción escrito por su aplicación:

    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context()
    {
        return array_merge(parent::context(), [
            'foo' => 'bar',
        ]);
    }

[]()

#### Contexto de registro de excepciones

Aunque añadir contexto a cada mensaje de registro puede ser útil, a veces una excepción en particular puede tener un contexto único que le gustaría incluir en sus registros. Definiendo un método de `contexto` en una de las excepciones personalizadas de su aplicación, puede especificar cualquier dato relevante para esa excepción que deba añadirse a la entrada de registro de la excepción:

    <?php

    namespace App\Exceptions;

    use Exception;

    class InvalidOrderException extends Exception
    {
        // ...

        /**
         * Get the exception's context information.
         *
         * @return array
         */
        public function context()
        {
            return ['order_id' => $this->orderId];
        }
    }

[]()

#### El ayudante de `informe`

A veces puede que necesite informar de una excepción pero continuar gestionando la petición actual. La función de ayuda de `informe` le permite informar rápidamente de una excepción a través del manejador de excepciones sin mostrar una página de error al usuario:

    public function isValid($value)
    {
        try {
            // Validate the value...
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

[]()

### Niveles de registro de excepciones

Cuando los mensajes se escriben en los [registros](/docs/%7B%7Bversion%7D%7D/logging) de su aplicación, los mensajes se escriben en un [nivel de registro](/docs/%7B%7Bversion%7D%7D/logging#log-levels) especificado, que indica la gravedad o la importancia del mensaje que se está registrando.

Como se ha indicado anteriormente, incluso cuando se registra una llamada de retorno personalizada para informar de una excepción utilizando el método `reportable`, Laravel registrará la excepción utilizando la configuración de registro predeterminada para la aplicación; sin embargo, dado que el nivel de registro puede influir a veces en los canales en los que se registra un mensaje, es posible que desee configurar el nivel de registro en el que se registran determinadas excepciones.

Para ello, puede definir un array de tipos de excepción y sus niveles de registro asociados dentro de la propiedad `$levels` del manejador de excepciones de su aplicación:

    use PDOException;
    use Psr\Log\LogLevel;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        PDOException::class => LogLevel::CRITICAL,
    ];

[]()

### Ignorando Excepciones por Tipo

Cuando construya su aplicación, habrá algunos tipos de excepciones que simplemente querrá ignorar y nunca reportar. El manejador de excepciones de su aplicación contiene una propiedad `$dontReport` que es inicializada a un array vacío. Cualquier clase que añadas a esta propiedad nunca será reportada; sin embargo, todavía pueden tener lógica de renderizado personalizada:

    use App\Exceptions\InvalidOrderException;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        InvalidOrderException::class,
    ];

> **Nota**  
> Entre bastidores, Laravel ya ignora algunos tipos de errores por ti, como las excepciones resultantes de errores HTTP 404 "no encontrado" o respuestas HTTP 419 generadas por tokens CSRF inválidos.

[]()

### Renderización de excepciones

Por defecto, el manejador de excepciones de Laravel convertirá las excepciones en una respuesta HTTP por ti. Sin embargo, usted es libre de registrar un closure renderizado personalizado para las excepciones de un tipo determinado. Puede hacerlo a través del método `renderable` de su manejador de excepciones.

El closure pasado al método `renderable` debe devolver una instancia de `Illuminate\Http\Response`, que puede ser generada a través del `response` helper. Laravel deducirá qué tipo de excepción devuelve el closure examinando el type-hint del closure:

    use App\Exceptions\InvalidOrderException;

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (InvalidOrderException $e, $request) {
            return response()->view('errors.invalid-order', [], 500);
        });
    }

También puedes usar el método `renderable` para sobreescribir el comportamiento de renderizado para excepciones incorporadas en Laravel o Symfony como `NotFoundHttpException`. Si el closure dado al método `renderable` no devuelve un valor, se utilizará el renderizado de excepciones por defecto de Laravel:

    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Record not found.'
                ], 404);
            }
        });
    }

[]()

### Excepciones Reportables y Renderizables

En lugar de comprobar el tipo de las excepciones en el método `register` del manejador de excepciones, puedes definir los métodos `report` y `render` directamente en tus excepciones personalizadas. Cuando estos métodos existan, serán llamados automáticamente por el framework:

    <?php

    namespace App\Exceptions;

    use Exception;

    class InvalidOrderException extends Exception
    {
        /**
         * Report the exception.
         *
         * @return bool|null
         */
        public function report()
        {
            //
        }

        /**
         * Render the exception into an HTTP response.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function render($request)
        {
            return response(/* ... */);
        }
    }

Si tu excepción extiende una excepción que ya es renderizable, como una excepción integrada de Laravel o Symfony, puedes devolver `false` desde el método `render` de la excepción para renderizar la respuesta HTTP por defecto de la excepción:

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        // Determine if the exception needs custom rendering...

        return false;
    }

Si tu excepción contiene lógica de reporte personalizada que sólo es necesaria cuando se cumplen ciertas condiciones, puede que necesites instruir a Laravel para que a veces reporte la excepción usando la configuración de manejo de excepciones por defecto. Para ello, puede devolver `false` desde el método `report` de la excepción:

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        // Determine if the exception needs custom reporting...

        return false;
    }

> **Nota**:  
> Puede indicar las dependencias necesarias del método de `informe` y el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) de Laravel las inyectará automáticamente en el método.

[]()

## Excepciones HTTP

Algunas excepciones describen códigos de error HTTP del servidor. Por ejemplo, puede ser un error de "página no encontrada" (404), un "error no autorizado" (401) o incluso un error 500 generado por el desarrollador. Para generar una respuesta de este tipo desde cualquier parte de tu aplicación, puedes utilizar el ayudante `abort`:

    abort(404);

[]()

### Páginas de error HTTP personalizadas

Laravel facilita la visualización de páginas de error personalizadas para varios códigos de estado HTTP. Por ejemplo, si deseas personalizar la página de error para códigos de estado HTTP 404, crea una plantilla de vista `resources/views/errors/404.blade.php`. Esta vista se mostrará en todos los errores 404 generados por su aplicación. Las vistas dentro de este directorio deben tener un nombre que coincida con el código de estado HTTP al que corresponden. La instancia de `Symfony\Component\HttpKernel\Exception\HttpException` levantada por la función `abort` será pasada a la vista como una variable `$exception`:

    <h2>{{ $exception->getMessage() }}</h2>

Puedes publicar las plantillas de páginas de error por defecto de Laravel utilizando el comando `vendor:publish` Artisan. Una vez publicadas las plantillas, puedes personalizarlas a tu gusto:

```shell
php artisan vendor:publish --tag=laravel-errors
```

[]()

#### Páginas de Error HTTP Fallback

También puede definir una página de error "fallback" para una serie dada de códigos de estado HTTP. Esta página se mostrará si no existe una página correspondiente para el código de estado HTTP específico que se ha producido. Para ello, defina una plantilla `4xx.blade.php` y una plantilla `5xx.blade.php` en el directorio `resources/views/errors` de su aplicación.
