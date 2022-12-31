# Verificación de correo electrónico

- [Introducción](#introduction)
  - [Preparación del modelo](#model-preparation)
  - [Preparación de la base de datos](#database-preparation)
- [Enrutamiento](#verification-routing)
  - [El aviso de verificación de correo electrónico](#the-email-verification-notice)
  - [El gestor de verificación de correo electrónico](#the-email-verification-handler)
  - [Reenvío del correo electrónico de verificación](#resending-the-verification-email)
  - [Protección de rutas](#protecting-routes)
- [Personalización](#customization)
- [Eventos](#events)

[]()

## Introducción

Muchas aplicaciones web requieren que los usuarios verifiquen sus direcciones de correo electrónico antes de utilizar la aplicación. En lugar de forzarte a reimplementar esta característica a mano para cada aplicación que crees, Laravel proporciona unos prácticos servicios integrados para enviar y verificar las solicitudes de verificación de correo electrónico.

> **Nota**  
> ¿Quieres empezar rápido? Instala uno de los kits de inicio [de aplicaciones Laravel](/docs/%7B%7Bversion%7D%7D/starter-kits) en una aplicación Laravel nueva. Los kits de inicio se encargarán del andamiaje de todo su sistema de autenticación, incluyendo el soporte de verificación de correo electrónico.

[]()

### Preparación del modelo

Antes de empezar, compruebe que su modelo `App\Models\User` implementa el contrato `Illuminate\Contracts\Auth\MustVerifyEmail`:

    <?php

    namespace App\Models;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

    class User extends Authenticatable implements MustVerifyEmail
    {
        use Notifiable;

        // ...
    }

Una vez añadida esta interfaz a su modelo, a los usuarios recién registrados se les enviará automáticamente un correo electrónico con un enlace de verificación de correo electrónico. Como puedes ver examinando el `App\Providers\EventServiceProvider` de tu aplicación, Laravel ya contiene un [listener](/docs/%7B%7Bversion%7D%7D/events) `SendEmailVerificationNotification` que se adjunta al evento `Illuminate\Auth\Events\Registered`. Este receptor de eventos enviará el enlace de verificación de correo electrónico al usuario.

Si está implementando manualmente el registro dentro de su aplicación en lugar de utilizar [un kit de inicio](/docs/%7B%7Bversion%7D%7D/starter-kits), debe asegurarse de que está enviando el evento `Illuminate\Auth\Events\Registered` después de que el registro de un usuario se haya realizado correctamente:

    use Illuminate\Auth\Events\Registered;

    event(new Registered($user));

[]()

### Preparación de la base de datos

A continuación, la tabla `users` debe contener una columna `email_verified_at` para almacenar la fecha y hora en que se verificó la dirección de correo electrónico del usuario. Por defecto, la migración de la tabla `users` incluida con el framework Laravel ya incluye esta columna. Por lo tanto, todo lo que necesitas hacer es ejecutar tus migraciones de base de datos:

```shell
php artisan migrate
```

[]()

## Enrutamiento

Para implementar correctamente la verificación del correo electrónico, será necesario definir tres rutas. En primer lugar, se necesitará una ruta para mostrar un aviso al usuario de que debe hacer clic en el enlace de verificación de correo electrónico en el correo electrónico de verificación que Laravel le envió después del registro.

En segundo lugar, se necesitará una ruta para gestionar las peticiones generadas cuando el usuario haga clic en el enlace de verificación del correo electrónico.

En tercer lugar, se necesitará una ruta para reenviar un enlace de verificación si el usuario pierde accidentalmente el primer enlace de verificación.

[]()

### El aviso de verificación de correo electrónico

Como se mencionó anteriormente, una ruta debe ser definida que devolverá una vista instruyendo al usuario a hacer clic en el enlace de verificación de correo electrónico que fue enviado a ellos por Laravel después del registro. Esta vista se mostrará a los usuarios cuando intenten acceder a otras partes de la aplicación sin verificar primero su dirección de correo electrónico. Recuerda, el enlace se envía automáticamente al usuario siempre que tu modelo `App\Models\User` implemente la interfaz `MustVerifyEmail`:

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

La ruta que devuelve el aviso de verificación de correo electrónico debe llamarse `verification.notice`. Es importante que a la ruta se le asigne este nombre exacto ya que el middleware de `verificación` [incluido con Laravel](#protecting-routes) redirigirá automáticamente a este nombre de ruta si un usuario no ha verificado su dirección de correo electrónico.

> **Nota**  
> Cuando se implementa manualmente la verificación de correo electrónico, es necesario definir el contenido de la vista del aviso de verificación. Si quieres un andamiaje que incluya todas las vistas de autenticación y verificación necesarias, echa un vistazo a [los kits de inicio de aplicaciones Laravel](/docs/%7B%7Bversion%7D%7D/starter-kits).

[]()

### El controlador de verificación de correo electrónico

A continuación, tenemos que definir una ruta que se encargará de las solicitudes generadas cuando el usuario haga clic en el enlace de verificación que se le envió por correo electrónico. Esta ruta debería llamarse `verification.verify` y tener asignados los middlewares `auth` y `signed`:

    use Illuminate\Foundation\Auth\EmailVerificationRequest;

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect('/home');
    })->middleware(['auth', 'signed'])->name('verification.verify');

Antes de continuar, echemos un vistazo más de cerca a esta ruta. En primer lugar, observará que estamos utilizando un tipo de petición `EmailVerificationRequest` en lugar de la típica instancia `Illuminate\Http\Request`. El `EmailVerificationRequest` es una solicitud [de formulario](/docs/%7B%7Bversion%7D%7D/validation#form-request-validation) que se incluye con Laravel. Esta petición se encargará automáticamente de validar los parámetros `id` y `hash` de la petición.

A continuación, podemos proceder directamente a llamar al método `fulfill` de la petición. Este método llamará al método `markEmailAsVerified` en el usuario autenticado y enviará el evento `Illuminate\Auth\Events\Verified`. El método `markEmailAsVerified` está disponible para el modelo `App\Models\User` predeterminado a través de la clase base `Illuminate\Foundation\Auth\User`. Una vez verificada la dirección de correo electrónico del usuario, puede redirigirlo a donde desee.

[]()

### Reenvío del correo electrónico de verificación

A veces un usuario puede extraviar o borrar accidentalmente el correo electrónico de verificación de la dirección de correo electrónico. Para dar cabida a esto, es posible que desee definir una ruta para permitir que el usuario solicite que el correo electrónico de verificación sea reenviado. A continuación, puede realizar una solicitud a esta ruta colocando un simple botón de envío de formulario dentro de su [vista de aviso de verificación](#the-email-verification-notice):

    use Illuminate\Http\Request;

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

[]()

### Protección de rutas

[middleware](/docs/%7B%7Bversion%7D%7D/middleware)[ middleware](/docs/%7B%7Bversion%7D%7D/middleware) middleware de[ruta](/docs/%7B%7Bversion%7D%7D/middleware) se puede utilizar para permitir que sólo los usuarios verificados accedan a una ruta determinada. Laravel incluye un middleware `verificado`, que hace referencia a la clase `Illuminate\Auth\middleware\EnsureEmailIsVerified`. Dado que este middleware ya está registrado en el núcleo HTTP de tu aplicación, todo lo que necesitas hacer es adjuntar el middleware a una definición de ruta. Típicamente, este middleware está emparejado con el `auth` middleware:

    Route::get('/profile', function () {
        // Only verified users may access this route...
    })->middleware(['auth', 'verified']);

Si un usuario no verificado intenta acceder a una ruta a la que se ha asignado este middleware, será redirigido automáticamente a la [ruta denominada](/docs/%7B%7Bversion%7D%7D/routing#named-routes) `verification.notice`.

[]()

## Personalización

[]()

#### Personalización del correo electrónico de verificación

Aunque la notificación de verificación de correo electrónico por defecto debería satisfacer los requisitos de la mayoría de las aplicaciones, Laravel permite personalizar cómo se construye el mensaje de correo de verificación de correo electrónico.

Para empezar, pase un closure al método `toMailUsing` proporcionado por la notificación `Illuminate\Auth\Notifications\VerifyEmail`. El closure recibirá la instancia del modelo notificable que está recibiendo la notificación, así como la URL de verificación de correo electrónico firmado que el usuario debe visitar para verificar su dirección de correo electrónico. El closure debe devolver una instancia de `Illuminate\Notifications\Messages\MailMessage`. Normalmente, debería llamar al método `toMailUsing` desde el método `boot` de la clase `App\Providers\AuthServiceProvider` de su aplicación:

    use Illuminate\Auth\Notifications\VerifyEmail;
    use Illuminate\Notifications\Messages\MailMessage;

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // ...

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });
    }

> **Nota**  
> Para obtener más información sobre las notificaciones de correo, consulte la [documentación de notificaciones de correo](/docs/%7B%7Bversion%7D%7D/notifications#mail-notifications).

[]()

## Eventos

Cuando se utilizan los [kits de inicio de aplicaciones](/docs/%7B%7Bversion%7D%7D/starter-kits) Laravel, Laravel envía [eventos](/docs/%7B%7Bversion%7D%7D/events) durante el proceso de verificación de correo electrónico. Si estás gestionando manualmente la verificación del correo electrónico para tu aplicación, puede que desees enviar manualmente estos eventos una vez completada la verificación. Puedes adjuntar oyentes a estos eventos en el `EventServiceProvider` de tu aplicación:

    use App\Listeners\LogVerifiedUser;
    use Illuminate\Auth\Events\Verified;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Verified::class => [
            LogVerifiedUser::class,
        ],
    ];
