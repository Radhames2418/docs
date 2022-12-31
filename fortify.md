# Laravel Fortify

- [Introducción](#introduction)
  - [¿Qué es Fortify?](#what-is-fortify)
  - [¿Cuándo debo utilizar Fortify?](#when-should-i-use-fortify)
- [Instalación](#installation)
  - [El proveedor de servicios Fortify](#the-fortify-service-provider)
  - [Características de Fortify](#fortify-features)
  - [Desactivación de vistas](#disabling-views)
- [Autenticación](#authentication)
  - [Personalización de la autenticación de usuarios](#customizing-user-authentication)
  - [Personalización del proceso de autenticación](#customizing-the-authentication-pipeline)
  - [Personalización de redireccionamientos](#customizing-authentication-redirects)
- [Autenticación de dos factores](#two-factor-authentication)
  - [Activación de la autenticación de dos factores](#enabling-two-factor-authentication)
  - [Autenticación con autenticación de dos factores](#authenticating-with-two-factor-authentication)
  - [Desactivación de la autenticación de dos factores](#disabling-two-factor-authentication)
- [Registro](#registration)
  - [Personalización del registro](#customizing-registration)
- [Restablecimiento de contraseña](#password-reset)
  - [Solicitud de un enlace de restablecimiento de contraseña](#requesting-a-password-reset-link)
  - [Restablecer la contraseña](#resetting-the-password)
  - [Personalización del restablecimiento de contraseña](#customizing-password-resets)
- [Verificación de correo electrónico](#email-verification)
  - [Protección de rutas](#protecting-routes)
- [Confirmación de Contraseña](#password-confirmation)

[]()

## Introducción

[Laravel Fortify](https://github.com/laravel/fortify) es una implementación de backend de autenticación agnóstica de frontend para Laravel. Fortify registra las rutas y controladores necesarios para implementar todas las características de autenticación de Laravel, incluyendo inicio de sesión, registro, restablecimiento de contraseña, verificación de correo electrónico y más. Después de instalar Fortify, puedes ejecutar el comando `route:list` Artisan para ver las rutas que Fortify ha registrado.

Dado que Fortify no proporciona su propia interfaz de usuario, está pensado para ser emparejado con su propia interfaz de usuario que hace peticiones a las rutas que registra. Vamos a discutir exactamente cómo hacer peticiones a estas rutas en el resto de esta documentación.

> **Nota**  
> Recuerda, Fortify es un paquete que está destinado a darte una ventaja en la implementación de las características de autenticación de Laravel. **No estás obligado a usarlo.** Siempre puedes interactuar manualmente con los servicios de autenticación de Laravel siguiendo la documentación disponible sobre [autenticación](/docs/%7B%7Bversion%7D%7D/authentication), [restablecimiento de contraseña](/docs/%7B%7Bversion%7D%7D/passwords) y [verificación de correo electrónico](/docs/%7B%7Bversion%7D%7D/verification).

[]()

### ¿Qué es Fortify?

Como se mencionó anteriormente, Laravel Fortify es una implementación de backend de autenticación agnóstica de frontend para Laravel. Fortify registra las rutas y controladores necesarios para implementar todas las características de autenticación de Laravel, incluyendo inicio de sesión, registro, restablecimiento de contraseña, verificación de correo electrónico, y más.

**No estás obligado a usar Fortify para utilizar las características de autenticación de Laravel.** Siempre puedes interactuar manualmente con los servicios de autenticación de Laravel siguiendo la documentación disponible sobre [autenticación](/docs/%7B%7Bversion%7D%7D/authentication), restablecimiento [de contraseña](/docs/%7B%7Bversion%7D%7D/passwords) y verificación de correo [electrónico](/docs/%7B%7Bversion%7D%7D/verification).

Si eres nuevo en Laravel, es posible que desees explorar el kit de inicio de aplicaciones [Laravel Breeze](/docs/%7B%7Bversion%7D%7D/starter-kits) antes de intentar utilizar Laravel Fortify. Laravel Breeze proporciona un andamiaje de autenticación para tu aplicación que incluye una interfaz de usuario construida con [CSS Tailwind](https://tailwindcss.com). A diferencia de Fortify, Breeze publica sus rutas y controladores directamente en tu aplicación. Esto te permite estudiar y sentirte cómodo con las características de autenticación de Laravel antes de permitir que Laravel Fortify implemente estas características por ti.

Laravel Fortify esencialmente toma las rutas y controladores de Laravel Breeze y los ofrece como un paquete que no incluye una interfaz de usuario. Esto te permite aún rápidamente andamiar la implementación backend de la capa de autenticación de tu aplicación sin estar atado a ninguna opinión frontend en particular.

[]()

### ¿Cuándo debo usar Fortify?

Puede que te estés preguntando cuándo es apropiado utilizar Laravel Fortify. En primer lugar, si estás utilizando uno de los kits de inicio de [aplicaciones](/docs/%7B%7Bversion%7D%7D/starter-kits) de Laravel, no necesitas instalar Laravel Fortify ya que todos los kits de inicio de aplicaciones de Laravel ya proporcionan una implementación completa de autenticación.

Si no estás utilizando un kit de inicio de aplicación y tu aplicación necesita características de autenticación, tienes dos opciones: implementar manualmente las características de autenticación de tu aplicación o utilizar Laravel Fortify para proporcionar la implementación backend de estas características.

Si eliges instalar Fortify, tu interfaz de usuario realizará peticiones a las rutas de autenticación de Fortify que se detallan en esta documentación para autenticar y registrar usuarios.

Si decides interactuar manualmente con los servicios de autenticación de Laravel en lugar de utilizar Fortify, puedes hacerlo siguiendo la documentación disponible en la documentación de [autenticación](/docs/%7B%7Bversion%7D%7D/authentication), [restablecimiento de contraseña](/docs/%7B%7Bversion%7D%7D/passwords) y [verificación de correo electrónico](/docs/%7B%7Bversion%7D%7D/verification).

[]()

#### Laravel Fortify y Laravel Sanctum

Algunos desarrolladores se confunden con respecto a la diferencia entre [Laravel Sanctum](/docs/%7B%7Bversion%7D%7D/sanctum) y Laravel Fortify. Debido a que los dos paquetes resuelven dos problemas diferentes pero relacionados, Laravel Fortify y Laravel Sanctum no son paquetes mutuamente excluyentes o competidores.

Laravel Sanctum sólo se ocupa de gestionar los tokens de la API y de autenticar a los usuarios existentes mediante cookies de sesión o tokens. Sanctum no proporciona ninguna ruta que gestione el registro de usuarios, el restablecimiento de contraseñas, etc.

Si estás intentando construir manualmente la capa de autenticación para una aplicación que ofrece una API o sirve como backend para una aplicación de una sola página, es muy posible que utilices tanto Laravel Fortify (para registro de usuarios, restablecimiento de contraseñas, etc.) como Laravel Sanctum (gestión de tokens de API, autenticación de sesión).

[]()

## Instalación

Para empezar, instala Fortify con el gestor de paquetes Composer:

```shell
composer require laravel/fortify
```

A continuación, publica los recursos de Fortify mediante el comando `vendor:publish`:

```shell
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
```

Este comando publicará las acciones de Fortify en tu directorio `app/Actions`, que se creará si no existe. Además, se publicarán el `FortifyServiceProvider`, el archivo de configuración y todas las migraciones de base de datos necesarias.

A continuación, debe migrar su base de datos:

```shell
php artisan migrate
```

[]()

### El proveedor de servicios Fortify

El comando `vendor:` publish descrito anteriormente también publicará la clase `App\Providers\FortifyServiceProvider`. Debes asegurarte de que esta clase está registrada en el array `providers` del fichero de configuración `config/app.php` de tu aplicación.

El proveedor de servicios Fortify registra las acciones que Fortify publicó e indica a Fortify que las utilice cuando Fortify ejecute sus respectivas tareas.

[]()

### Características de Fortify

El archivo de configuración `de fortify` contiene una matriz array configuración de `características`. Esta array define las rutas backend / características que Fortify expondrá por defecto. Si no está utilizando Fortify en combinación con [Laravel Jetstream](https://jetstream.laravel.com), le recomendamos que sólo habilite las siguientes características, que son las características básicas de autenticación proporcionadas por la mayoría de las aplicaciones Laravel:

```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
],
```

[]()

### Deshabilitar vistas

Por defecto, Fortify define rutas destinadas a devolver vistas, como una pantalla de inicio de sesión o de registro. Sin embargo, si está creando una aplicación de una sola página basada en JavaScript, es posible que no necesite estas rutas. Por esa razón, puede deshabilitar estas rutas por completo estableciendo el valor de configuración de `vistas` dentro del archivo de configuración `config/fortify.php` de su aplicación en `false`:

```php
'views' => false,
```

[]()

#### Deshabilitar Vistas y Restablecimiento de Contraseña

Si decides deshabilitar las vistas de Fortify y vas a implementar funciones de restablecimiento de contraseña en tu aplicación, deberás definir una ruta llamada `password.` reset que se encargue de mostrar la vista "restablecer contraseña" de tu aplicación. Esto es necesario porque la notificación `Illuminate\Auth\Notifications\ResetPassword` de Laravel generará la URL de restablecimiento de contraseña a través de la ruta llamada password. `reset`.

[]()

## Autenticación

Para empezar, tenemos que indicar a Fortify cómo devolver nuestra vista "login". Recuerda que Fortify es una biblioteca de autenticación headless. Si quieres una implementación frontend de las características de autenticación de Laravel que ya estén completadas para ti, deberías utilizar un [kit de inicio de aplicación](/docs/%7B%7Bversion%7D%7D/starter-kits).

Toda la lógica de renderizado de la vista de autenticación se puede personalizar utilizando los métodos apropiados disponibles a través de la clase `Laravel\Fortify\Fortify`. Por lo general, usted debe llamar a este método desde el método de `arranque` de su aplicación `AppProviders\FortifyServiceProvider` clase. Fortify se encargará de definir la ruta `/login` que devuelve esta vista:

    use Laravel\Fortify\Fortify;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ...
    }

La plantilla de inicio de sesión debe incluir un formulario que realice una solicitud POST a `/login`. El punto final `/login` espera una cadena `email` / `nombre de usuario` y una `contraseña`. El nombre del campo email / nombre de usuario debe coincidir con el valor del `nombre de usuario` en el archivo de configuración `config/fortify.php`. Además, un campo booleano `"remember"` puede ser proporcionado para indicar que el usuario desea utilizar la funcionalidad "remember me" proporcionada por Laravel.

Si el intento de inicio de sesión tiene éxito, Fortify le redirigirá al URI configurado a través de la opción de configuración `home` dentro del archivo de configuración `fortify` de su aplicación. Si la solicitud de inicio de sesión fue una solicitud XHR, se devolverá una respuesta HTTP 200.

Si la solicitud no se ha realizado correctamente, el usuario será redirigido de nuevo a la pantalla de inicio de sesión y los errores de validación estarán disponibles a través de la [variable](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors) compartida `$errors` de la [plantilla Blade](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors). O, en el caso de una petición XHR, los errores de validación serán devueltos con la respuesta HTTP 422.

[]()

### Personalización de la autenticación de usuarios

Fortify recuperará y autenticará automáticamente al usuario basándose en las credenciales proporcionadas y en la guarda de autenticación configurada para su aplicación. Sin embargo, a veces es posible que desee tener una personalización completa sobre cómo se autentican las credenciales de inicio de sesión y se recuperan los usuarios. Afortunadamente, Fortify te permite conseguir esto fácilmente usando el método `Fortify::authenticateUsing`.

Este método acepta un closure que recibe la petición HTTP entrante. El closure es responsable de validar las credenciales de inicio de sesión adjuntas a la solicitud y devolver la instancia de usuario asociada. Si las credenciales no son válidas o no se encuentra ningún usuario, el closure devolverá `null` o `false`. Típicamente, este método debe ser llamado desde el método `boot` de tu `FortifyServiceProvider`:

```php
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Fortify::authenticateUsing(function (Request $request) {
        $user = User::where('email', $request->email)->first();

        if ($user &&
            Hash::check($request->password, $user->password)) {
            return $user;
        }
    });

    // ...
}
```

[]()

#### Authentication Guard

Puede personalizar la guarda de autenticación utilizada por Fortify dentro del archivo de configuración de `fortify` de su aplicación. Sin embargo, debe asegurarse de que el guarda configurado es una implementación de `Illuminate\Contracts\Auth\StatefulGuard`. Si estás intentando utilizar Laravel Fortify para autenticar una SPA, deberías utilizar el guarda `web` por defecto de Laravel en combinación con [Laravel Sanctum](https://laravel.com/docs/sanctum).

[]()

### Personalización del proceso de autenticación

Laravel Fortify autentica las peticiones de login a través de un pipeline de clases invocables. Si lo deseas, puedes definir un pipeline personalizado de clases a través de las cuales se canalizarán las peticiones de login. Cada clase debe tener un método `__invoke` que recibe la instancia `Illuminate\Http\Request` entrante y, como [middleware](/docs/%7B%7Bversion%7D%7D/middleware), una variable `$next` que se invoca con el fin de pasar la solicitud a la siguiente clase en la tubería.

Para definir su canalización personalizada, puede utilizar el método `Fortify::authenticateThrough`. Este método acepta un closure que debe devolver la array de clases para canalizar la solicitud de inicio de sesión a través. Por lo general, este método debe ser llamado desde el método de `arranque` de su `App\Providers\FortifyServiceProvider` clase.

El siguiente ejemplo contiene la definición predeterminada de la tubería que puede utilizar como punto de partida al hacer sus propias modificaciones:

```php
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;

Fortify::authenticateThrough(function (Request $request) {
    return array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
    ]);
});
```

[]()

### Personalización de redireccionamientos

Si el intento de inicio de sesión tiene éxito, Fortify le redirigirá a la URI configurada a través de la opción de configuración de `inicio` dentro del archivo de configuración de `fortify` de su aplicación. Si la solicitud de inicio de sesión fue una solicitud XHR, se devolverá una respuesta HTTP 200. Después de que un usuario cierra la sesión de la aplicación, el usuario será redirigido a la `/` URI.

Si necesita una personalización avanzada de este comportamiento, puede enlazar implementaciones de los contratos `LoginResponse` y `LogoutResponse` en el [contenedor de servicios](/docs/%7B%7Bversion%7D%7D/container) Laravel. Normalmente, esto debe hacerse dentro del método `register` de la clase `App\Providers\FortifyServiceProvider` de su aplicación:

```php
use Laravel\Fortify\Contracts\LogoutResponse;

/**
 * Register any application services.
 *
 * @return void
 */
public function register()
{
    $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
        public function toResponse($request)
        {
            return redirect('/');
        }
    });
}
```

[]()

## Autenticación de dos factores

Cuando la función de autenticación de dos factores de Fortify está activada, el usuario debe introducir un token numérico de seis dígitos durante el proceso de autenticación. Este token se genera utilizando una contraseña de un solo uso basada en tiempo (TOTP) que se puede recuperar desde cualquier aplicación de autenticación móvil compatible con TOTP, como Google Authenticator.

Antes de empezar, primero debe asegurarse de que el modelo `App\Models\User` de su aplicación utiliza el rasgo `Laravel\Fortify\TwoFactorAuthenticatable`:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use Notifiable, TwoFactorAuthenticatable;
}
```

A continuación, debes crear una pantalla dentro de tu aplicación en la que los usuarios puedan gestionar su configuración de autenticación de dos factores. Esta pantalla debe permitir al usuario activar y desactivar la autenticación de dos factores, así como regenerar sus códigos de recuperación de autenticación de dos factores.

> Por defecto, la array `rasgos` del archivo de configuración `de Fortify` instruye a la configuración de autenticación de dos factores de Fortify para requerir la confirmación de la contraseña antes de la modificación. Por lo tanto, tu aplicación debe implementar el rasgo de confirmación de [contraseña](#password-confirmation) de Fortify antes de continuar.

[]()

### Activación de la autenticación de dos factores

Para comenzar a habilitar la autenticación de dos factores, la aplicación debe realizar una solicitud POST al punto final `/user/two-factor-authentication` definido por Fortify. Si la solicitud tiene éxito, el usuario será redirigido de vuelta a la URL anterior y la variable de sesión de `estado` se establecerá en `two-factor-authentication-enabled`. Puede detectar esta variable de sesión de `estado` dentro de sus plantillas para mostrar el mensaje de éxito apropiado. Si la solicitud era una solicitud XHR, se devolverá una respuesta HTTP `200`.

Después de elegir habilitar la autenticación de dos factores, el usuario aún debe "confirmar" su configuración de autenticación de dos factores proporcionando un código de autenticación de dos factores válido. Por lo tanto, el mensaje de "éxito" debe indicar al usuario que la confirmación de la autenticación de dos factores sigue siendo necesaria:

```html
@if (session('status') == 'two-factor-authentication-enabled')
    <div class="mb-4 font-medium text-sm">
        Please finish configuring two factor authentication below.
    </div>
@endif
```

A continuación, debe mostrar el código QR de autenticación de dos factores para que el usuario lo escanee en su aplicación de autenticación. Si está utilizando Blade para renderizar el frontend de su aplicación, puede recuperar el código QR SVG utilizando el método `twoFactorQrCodeSvg` disponible en la instancia de usuario:

```php
$request->user()->twoFactorQrCodeSvg();
```

Si está creando una interfaz basada en JavaScript, puede realizar una solicitud XHR GET al punto final `/user/two-factor-qr-code` para recuperar el código QR de autenticación de dos factores del usuario. Este punto final devolverá un objeto JSON que contiene una clave `svg`.

[]()

#### Confirmación de la autenticación de dos factores

Además de mostrar el código QR de autenticación de dos factores del usuario, debe proporcionar una entrada de texto donde el usuario pueda proporcionar un código de autenticación válido para "confirmar" su configuración de autenticación de dos factores. Este código debe ser proporcionado a la aplicación Laravel a través de una solicitud POST al endpoint `/user/confirmed-two-factor-authentication` definido por Fortify.

Si la solicitud se realiza correctamente, el usuario será redirigido a la URL anterior y la variable de sesión de `estado` se establecerá como `autenticación de dos factores confirmada`:

```html
@if (session('status') == 'two-factor-authentication-confirmed')
    <div class="mb-4 font-medium text-sm">
        Two factor authentication confirmed and enabled successfully.
    </div>
@endif
```

Si la solicitud al punto final de confirmación de la autenticación de dos factores se realizó mediante una solicitud XHR, se devolverá una respuesta HTTP `200`.

[]()

#### Visualización de los códigos de recuperación

También debe mostrar los códigos de recuperación de dos factores del usuario. Estos códigos de recuperación permiten al usuario autenticarse si pierde el acceso a su dispositivo móvil. Si estás utilizando Blade para renderizar el frontend de tu aplicación, puedes acceder a los códigos de recuperación a través de la instancia de usuario autenticado:

```php
(array) $request->user()->recoveryCodes()
```

Si está creando una interfaz con JavaScript, puede realizar una solicitud XHR GET al punto final `/user/two-factor-recovery-codes`. Este punto final devolverá una array JSON con los códigos de recuperación del usuario.

Para regenerar los códigos de recuperación del usuario, la aplicación debe realizar una solicitud POST al endpoint `/user/two-factor-recovery-codes`.

[]()

### Autenticación con autenticación de dos factores

Durante el proceso de autenticación, Fortify redirigirá automáticamente al usuario a la pantalla de desafío de autenticación de dos factores de su aplicación. Sin embargo, si su aplicación está haciendo una solicitud de inicio de sesión XHR, la respuesta JSON devuelta después de un intento de autenticación exitoso contendrá un objeto JSON que tiene una propiedad booleana `two_factor`. Debes inspeccionar este valor para saber si debes redirigir a la pantalla de desafío de autenticación de dos factores de tu aplicación.

Para empezar a implementar la funcionalidad de autenticación de dos factores, tenemos que indicar a Fortify cómo devolver nuestra vista de desafío de autenticación de dos factores. Toda la lógica de representación de la vista de autenticación de Fortify puede personalizarse utilizando los métodos apropiados disponibles a través de la clase `Laravel\Fortify\Fortify`. Por lo general, usted debe llamar a este método desde el método de `arranque` de su aplicación `AppProviders\FortifyServiceProvider` clase:

```php
use Laravel\Fortify\Fortify;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Fortify::twoFactorChallengeView(function () {
        return view('auth.two-factor-challenge');
    });

    // ...
}
```

Fortify se encargará de definir la ruta `/two-factor-challenge` que devuelve esta vista. Tu plantilla `two-factor-challenge` debe incluir un formulario que haga una petición POST al endpoint `/two-factor-challenge`. La acción `/two-factor-challenge` espera un campo `code` que contenga un token TOTP válido o un campo `recovery_code` que contenga uno de los códigos de recuperación del usuario.

Si el intento de inicio de sesión tiene éxito, Fortify redirigirá al usuario al URI configurado a través de la opción de configuración de `inicio` dentro del archivo de configuración de `fortify` de su aplicación. Si la solicitud de inicio de sesión fue una solicitud XHR, se devolverá una respuesta HTTP 204.

Si la solicitud no tuvo éxito, el usuario será redirigido de nuevo a la pantalla de desafío de dos factores y los errores de validación estarán disponibles para usted a través de la [variable de plantilla](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors) compartida `$errors` [Blade](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors). O, en el caso de una solicitud XHR, los errores de validación se devolverán con una respuesta HTTP 422.

[]()

### Desactivación de la autenticación de dos factores

Para desactivar la autenticación de dos factores, su aplicación debe hacer una solicitud DELETE al punto final `/user/two-factor-authentication`. Recuerda que los puntos finales de autenticación de dos factores de Fortify requieren la [confirmación de la contraseña](#password-confirmation) antes de ser llamados.

[]()

## Registro

Para empezar a implementar la funcionalidad de registro de nuestra aplicación, tenemos que indicarle a Fortify cómo devolver nuestra vista "register". Recuerda que Fortify es una biblioteca de autenticación headless. Si quieres una implementación frontend de las características de autenticación de Laravel que ya estén completadas para ti, deberías utilizar un [kit de inicio de aplicación](/docs/%7B%7Bversion%7D%7D/starter-kits).

Toda la lógica de renderizado de vistas de Fortify se puede personalizar utilizando los métodos apropiados disponibles a través de la clase `Laravel\Fortify\Fortify`. Por lo general, usted debe llamar a este método desde el método de `arranque` de su `App\Providers\FortifyServiceProvider` clase:

```php
use Laravel\Fortify\Fortify;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Fortify::registerView(function () {
        return view('auth.register');
    });

    // ...
}
```

Fortify se encargará de definir la ruta `/register` que devuelve esta vista. La plantilla de `registro` debe incluir un formulario que realice una solicitud POST al punto final `/register` definido por Fortify.

El punto final `/register` espera un `nombre de` cadena, dirección de correo electrónico de cadena / nombre de usuario, `contraseña` y campos `password_confirmation`. El nombre del campo email / nombre de usuario debe coincidir con el valor de configuración del `nombre de usuario` definido en el archivo de configuración de `fortify` de tu aplicación.

Si el intento de registro tiene éxito, Fortify redirigirá al usuario a la URI configurada mediante la opción de configuración de `inicio` dentro del archivo de configuración de `fortify` de su aplicación. Si la solicitud fue una solicitud XHR, se devolverá una respuesta HTTP 201.

Si la solicitud no se ha realizado correctamente, el usuario será redirigido de nuevo a la pantalla de registro y los errores de validación estarán disponibles para usted a través de la [variable de plantilla](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors) compartida `$errors` [Blade](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors). O, en el caso de una solicitud XHR, los errores de validación se devolverán con una respuesta HTTP 422.

[]()

### Personalización del registro

El proceso de validación y creación de usuarios puede personalizarse modificando la acción `App\Actions\Fortify\CreateNewUser` que se generó al instalar Laravel Fortify.

[]()

## Restablecimiento de contraseña

[]()

### Solicitud de un enlace de restablecimiento de contraseña

Para empezar a implementar la funcionalidad de restablecimiento de contraseña de nuestra aplicación, tenemos que indicar a Fortify cómo devolver nuestra vista "olvidé mi contraseña". Recuerda que Fortify es una librería de autenticación headless. Si quieres una implementación frontend de las funciones de autenticación de Laravel que ya esté terminada para ti, deberías utilizar un [kit de inicio de aplicación](/docs/%7B%7Bversion%7D%7D/starter-kits).

Toda la lógica de renderizado de vistas de Fortify se puede personalizar utilizando los métodos apropiados disponibles a través de la clase `Laravel\Fortify\Fortify`. Por lo general, usted debe llamar a este método desde el método de `arranque` de su aplicación `App\Providers\FortifyServiceProvider` clase:

```php
use Laravel\Fortify\Fortify;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Fortify::requestPasswordResetLinkView(function () {
        return view('auth.forgot-password');
    });

    // ...
}
```

Fortify se encargará de definir el endpoint `/forgot-password` que devuelve esta vista. La plantilla de `olvido` de contraseña debe incluir un formulario que realice una solicitud POST al punto final `/forgot-password`.

El punto final `/forgot-password` espera un campo de `correo electrónico` de cadena. El nombre de este campo / columna de la base de datos debe coincidir con el valor de configuración de `correo electrónico` dentro del archivo de configuración de `fortify` de su aplicación.

[]()

#### Gestión de la respuesta a la solicitud de enlace de restablecimiento de contraseña

Si la solicitud de enlace de restablecimiento de contraseña se ha realizado correctamente, Fortify redirigirá al usuario de nuevo al punto final `/forgot-password` y le enviará un correo electrónico con un enlace seguro que puede utilizar para restablecer su contraseña. Si la solicitud era una solicitud XHR, se devolverá una respuesta HTTP 200.

Después de ser redirigido de vuelta al endpoint `/forgot-password` después de una solicitud exitosa, la variable de sesión `status` puede ser utilizada para mostrar el estado del intento de solicitud del enlace de restablecimiento de contraseña. El valor de esta variable de sesión coincidirá con una de las cadenas de traducción definidas en el [archivo de idioma](/docs/%7B%7Bversion%7D%7D/localization) de `contraseñas` de tu aplicación:

```html
@if (session('status'))
    <div class="mb-4 font-medium text-sm text-green-600">
        {{ session('status') }}
    </div>
@endif
```

Si la solicitud no ha tenido éxito, el usuario será redirigido de vuelta a la pantalla de solicitud de enlace de restablecimiento de contraseña y los errores de validación estarán disponibles a través de la variable de [plantilla](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors) compartida `$errors` [Blade](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors). O, en el caso de una petición XHR, los errores de validación serán devueltos con una respuesta HTTP 422.

[]()

### Restablecer la contraseña

Para terminar de implementar la funcionalidad de restablecimiento de contraseña de nuestra aplicación, tenemos que indicar a Fortify cómo devolver nuestra vista "restablecer contraseña".

Toda la lógica de renderizado de vistas de Fortify puede personalizarse utilizando los métodos apropiados disponibles a través de la clase `Laravel\Fortify\Fortify`. Por lo general, usted debe llamar a este método desde el método de `arranque` de su aplicación `App\Providers\FortifyServiceProvider` clase:

```php
use Laravel\Fortify\Fortify;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Fortify::resetPasswordView(function ($request) {
        return view('auth.reset-password', ['request' => $request]);
    });

    // ...
}
```

Fortify se encargará de definir la ruta para mostrar esta vista. Su plantilla `reset-password` debe incluir un formulario que haga una petición POST a `/reset-password`.

El endpoint `/reset-password` espera un campo string `email`, un campo `password`, un campo `password_confirmation`, y un campo oculto llamado `token` que contiene el valor de `request()->route('token')`. El nombre del campo "email" / columna de la base de datos debe coincidir con el valor de configuración de `email` definido en el archivo de configuración de `fortify` de tu aplicación.

[]()

#### Gestión de la respuesta de restablecimiento de contraseña

Si la solicitud de restablecimiento de contraseña se ha realizado correctamente, Fortify redirigirá de nuevo a la ruta `/login` para que el usuario pueda iniciar sesión con su nueva contraseña. Además, se establecerá una variable de sesión de `estado` para que pueda mostrar el estado correcto del restablecimiento en su pantalla de inicio de sesión:

```blade
@if (session('status'))
    <div class="mb-4 font-medium text-sm text-green-600">
        {{ session('status') }}
    </div>
@endif
```

Si la solicitud era una petición XHR, se devolverá una respuesta HTTP 200.

Si la solicitud no ha tenido éxito, el usuario será redirigido de nuevo a la pantalla de restablecimiento de contraseña y los errores de validación estarán disponibles a través de la [variable](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors) compartida `$errors` de la [plantilla Blade](/docs/%7B%7Bversion%7D%7D/validation#quick-displaying-the-validation-errors). O, en el caso de una solicitud XHR, los errores de validación se devolverán con una respuesta HTTP 422.

[]()

### Personalización del restablecimiento de contraseña

El proceso de restablecimiento de contraseña puede personalizarse modificando la acción `App\Actions\ResetUserPassword` que se generó al instalar Laravel Fortify.

[]()

## Verificación de correo electrónico

Después del registro, es posible que desee que los usuarios verifiquen su dirección de correo electrónico antes de continuar accediendo a su aplicación. Para empezar, asegúrate de que la función `emailVerification` está activada en la array `características` de tu archivo de configuración de `Fortify`. A continuación, debe asegurarse de que su clase `App\Models\User` implementa la interfaz `Illuminate\Contracts\Auth\MustVerifyEmail`.

Una vez completados estos dos pasos de configuración, los usuarios recién registrados recibirán un correo electrónico solicitándoles que verifiquen la titularidad de su dirección de correo electrónico. Sin embargo, tenemos que informar a Fortify de cómo mostrar la pantalla de verificación de correo electrónico que informa al usuario de que tiene que ir a hacer clic en el enlace de verificación del correo electrónico.

Toda la lógica de renderizado de la vista de Fortify se puede personalizar utilizando los métodos apropiados disponibles a través de la clase `Laravel\Fortify\Fortify`. Por lo general, usted debe llamar a este método desde el método de `arranque` de su aplicación `App\Providers\FortifyServiceProvider` clase:

```php
use Laravel\Fortify\Fortify;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Fortify::verifyEmailView(function () {
        return view('auth.verify-email');
    });

    // ...
}
```

Fortify se encargará de definir la ruta que muestra esta vista cuando un usuario es redirigido al endpoint `/email/verify` por el middleware `verificado` integrado de Laravel.

La plantilla de `correo electrónico de verificación` debe incluir un mensaje informativo que indique al usuario que haga clic en el enlace de verificación de correo electrónico que se envió a su dirección de correo electrónico.

[]()

#### Reenvío de enlaces de verificación de correo electrónico

Si lo desea, puede añadir un botón a la plantilla `verify-email` de su aplicación que active una solicitud POST al punto final `/email/verification-notification`. Cuando este punto final reciba una solicitud, se enviará al usuario un nuevo enlace de verificación por correo electrónico, lo que le permitirá obtener un nuevo enlace de verificación si el anterior se ha eliminado o perdido accidentalmente.

Si la solicitud para reenviar el enlace de verificación por correo electrónico se ha realizado correctamente, Fortify redirigirá al usuario de nuevo al punto final `/email/verify` con una variable de sesión de `estado`, lo que le permitirá mostrar un mensaje informativo al usuario informándole de que la operación se ha realizado correctamente. Si la solicitud era una solicitud XHR, se devolverá una respuesta HTTP 202:

```blade
@if (session('status') == 'verification-link-sent')
    <div class="mb-4 font-medium text-sm text-green-600">
        A new email verification link has been emailed to you!
    </div>
@endif
```

[]()

### Protección de rutas

Para especificar que una ruta o grupo de rutas requiere que el usuario haya verificado su dirección de correo electrónico, debe adjuntar el middleware `verificado` incorporado de Laravel a la ruta. Este middleware se registra dentro de la clase `App\Http\Kernel` de su aplicación:

```php
Route::get('/dashboard', function () {
    // ...
})->middleware(['verified']);
```

[]()

## Confirmación de contraseña

Mientras construyes tu aplicación, puede que ocasionalmente tengas acciones que deban requerir que el usuario confirme su contraseña antes de que se realice la acción. Normalmente, estas rutas están protegidas por el middleware `password.confirm` integrado en Laravel.

Para empezar a implementar la funcionalidad de confirmación de contraseña, tenemos que indicarle a Fortify cómo devolver la vista de "confirmación de contraseña" de nuestra aplicación. Recuerda que Fortify es una librería de autenticación headless. Si quieres una implementación frontend de las funciones de autenticación de Laravel que ya esté terminada para ti, deberías utilizar un [kit de inicio de aplicación](/docs/%7B%7Bversion%7D%7D/starter-kits).

Toda la lógica de renderizado de vistas de Fortify se puede personalizar utilizando los métodos apropiados disponibles a través de la clase `Laravel\Fortify\Fortify`. Por lo general, usted debe llamar a este método desde el método de `arranque` de su aplicación `App\Providers\FortifyServiceProvider` clase:

```php
use Laravel\Fortify\Fortify;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Fortify::confirmPasswordView(function () {
        return view('auth.confirm-password');
    });

    // ...
}
```

Fortify se encargará de definir el endpoint `/user/confirm-password` que devuelve esta vista. Tu plantilla `confirm-password` debe incluir un formulario que haga una petición POST al endpoint `/user/confirm-password`. El endpoint `/user/confirm-password` espera un campo de `contraseña` que contenga la contraseña actual del usuario.

Si la contraseña coincide con la contraseña actual del usuario, Fortify redirigirá al usuario a la ruta a la que estaba intentando acceder. Si la solicitud era una solicitud XHR, se devolverá una respuesta 201 HTTP.

Si la solicitud no tuvo éxito, el usuario será redirigido de nuevo a la pantalla de confirmación de contraseña y los errores de validación estarán disponibles a través de la variable de plantilla compartida `$errors` Blade. O, en el caso de una solicitud XHR, los errores de validación se devolverán con una respuesta HTTP 422.
