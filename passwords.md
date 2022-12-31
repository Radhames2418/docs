# Restablecer contraseñas

- [Introducción](#introduction)
  - [Preparación del modelo](#model-preparation)
  - [Preparación de la base de datos](#database-preparation)
  - [Configuración de hosts de confianza](#configuring-trusted-hosts)
- [Enrutamiento](#routing)
  - [Solicitud del enlace de restablecimiento de contraseña](#requesting-the-password-reset-link)
  - [Restablecimiento de la contraseña](#resetting-the-password)
- [Eliminación de tokens caducados](#deleting-expired-tokens)
- [Personalización](#password-customization)

[]()

## Introducción

La mayoría de las aplicaciones web proporcionan una forma para que los usuarios restablezcan sus contraseñas olvidadas. En lugar de obligarte a reimplementar esto a mano para cada aplicación que crees, Laravel proporciona servicios convenientes para enviar enlaces de restablecimiento de contraseña y restablecer contraseñas de forma segura.

> **Nota**  
> ¿Quieres empezar rápido? Instale un kit de inicio [de](/docs/%7B%7Bversion%7D%7D/starter-kits) Laravel en una aplicación Laravel nueva. Los kits de inicio de Laravel se encargarán de andamiar todo tu sistema de autenticación, incluyendo el restablecimiento de contraseñas olvidadas.

[]()

### Preparación del modelo

Antes de utilizar las características de restablecimiento de contraseña de Laravel, el modelo `App\Models\User` de su aplicación debe utilizar el rasgo `Illuminate\Notifications\Notifiable`. Normalmente, este rasgo ya está incluido en el modelo `App\Models\User` por defecto que se crea con las nuevas aplicaciones Laravel.

A continuación, compruebe que su modelo `App\Models\User` implementa el contrato `Illuminate\Contracts\Auth\CanResetPassword`. El modelo `App\Models\User` incluido con el framework ya implementa esta interfaz, y utiliza el rasgo `Illuminate\Auth\Passwords\CanResetPassword` para incluir los métodos necesarios para implementar la interfaz.

[]()

### Preparación de la base de datos

Se debe crear una tabla para almacenar los tokens de restablecimiento de contraseña de tu aplicación. La migración para esta tabla está incluida en la aplicación Laravel por defecto, por lo que sólo necesitas migrar tu base de datos para crear esta tabla:

```shell
php artisan migrate
```

[]()

### Configuración de hosts de confianza

Por defecto, Laravel responderá a todas las peticiones que reciba independientemente del contenido de la cabecera `Host` de la petición HTTP. Además, el valor de la cabecera `Host` se utilizará al generar URLs absolutas a su aplicación durante una petición web.

Normalmente, debe configurar su servidor web, como Nginx o Apache, para que sólo envíe solicitudes a su aplicación que coincidan con un nombre de host determinado. Sin embargo, si no tienes la capacidad de personalizar tu servidor web directamente y necesitas instruir a Laravel para que sólo responda a ciertos nombres de host, puedes hacerlo habilitando el middleware `App\Http\TrustHosts` para tu aplicación. Esto es particularmente importante cuando su aplicación ofrece la funcionalidad de restablecimiento de contraseña.

Para obtener más información sobre este middleware, consulte la [documentación](/docs/%7B%7Bversion%7D%7D/requests#configuring-trusted-hosts) del [middleware`TrustHosts`](/docs/%7B%7Bversion%7D%7D/requests#configuring-trusted-hosts).

[]()

## Enrutamiento

Para implementar correctamente el soporte para permitir a los usuarios restablecer sus contraseñas, necesitaremos definir varias rutas. En primer lugar, necesitaremos un par de rutas para permitir al usuario solicitar un enlace de restablecimiento de contraseña a través de su dirección de correo electrónico. En segundo lugar, necesitaremos un par de rutas para gestionar el restablecimiento de la contraseña una vez que el usuario visita el enlace de restablecimiento de contraseña que se le envía por correo electrónico y completa el formulario de restablecimiento de contraseña.

[]()

### Solicitud del enlace de restablecimiento de contraseña

[]()

#### El formulario de solicitud del enlace de restablecimiento de contraseña

En primer lugar, definiremos las rutas necesarias para solicitar los enlaces de restablecimiento de contraseña. Para empezar, definiremos una ruta que devuelva una vista con el formulario de solicitud de enlace de restablecimiento de contraseña:

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->middleware('guest')->name('password.request');

La vista que devuelve esta ruta debe tener un formulario que contenga un campo de `correo electrónico`, que permitirá al usuario solicitar un enlace de restablecimiento de contraseña para una dirección de correo electrónico determinada.

[]()

#### Gestión del envío del formulario

A continuación, definiremos una ruta que gestione la solicitud de envío del formulario desde la vista "Olvidé mi contraseña". Esta ruta se encargará de validar la dirección de correo electrónico y de enviar la solicitud de restablecimiento de contraseña al usuario correspondiente:

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Password;

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    })->middleware('guest')->name('password.email');

Antes de continuar, examinemos esta ruta con más detalle. En primer lugar, se valida el atributo `email` de la petición. A continuación, utilizaremos el "broker de contraseñas" integrado en Laravel (a través de la facade `Password` ) para enviar un enlace de restablecimiento de contraseña al usuario. El broker de contraseñas se encargará de recuperar al usuario por el campo dado (en este caso, la dirección de correo electrónico) y de enviarle un enlace de restablecimiento de contraseña a través del [sistema de notificaciones](/docs/%7B%7Bversion%7D%7D/notifications) integrado en Laravel.

El método `sendResetLink` devuelve un slug "status". Este estado puede ser traducido usando los ayudantes de [localización](/docs/%7B%7Bversion%7D%7D/localization) de Laravel para mostrar un mensaje amigable al usuario sobre el estado de su solicitud. La traducción del estado de restablecimiento de contraseña viene determinada por el archivo de idioma `lang/{lang}/passwords.php` de tu aplicación. Una entrada para cada posible valor de la babosa de estado se encuentra dentro del archivo de idioma de `contraseñas`.

Es posible que se pregunte cómo Laravel sabe cómo recuperar el registro de usuario de la base de datos de su aplicación al llamar al método `sendResetLink` de la facade `Password`. El broker de contraseñas de Laravel utiliza los "proveedores de usuario" de su sistema de autenticación para recuperar los registros de la base de datos. El proveedor de usuario utilizado por el agente de contraseñas se configura en el array configuración de `contraseñas` de su archivo de configuración `config/auth.` php. Para obtener más información sobre cómo escribir proveedores de usuario personalizados, consulte la [documentación de autenticación](/docs/%7B%7Bversion%7D%7D/authentication#adding-custom-user-providers).

> **Nota**  
> Cuando implemente manualmente el restablecimiento de contraseñas, deberá definir usted mismo el contenido de las vistas y rutas. Si deseas un andamiaje que incluya toda la lógica de autenticación y verificación necesaria, consulta los [kits de inicio de aplicaciones Laravel](/docs/%7B%7Bversion%7D%7D/starter-kits).

[]()

### Restablecer la contraseña

[]()

#### El formulario de restablecimiento de contraseña

A continuación, definiremos las rutas necesarias para restablecer la contraseña una vez que el usuario haga clic en el enlace de restablecimiento de contraseña que se le ha enviado por correo electrónico y proporcione una nueva contraseña. En primer lugar, vamos a definir la ruta que mostrará el formulario de restablecimiento de contraseña que se muestra cuando el usuario hace clic en el enlace de restablecimiento de contraseña. Esta ruta recibirá un parámetro `token` que utilizaremos más adelante para verificar la solicitud de restablecimiento de contraseña:

    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->middleware('guest')->name('password.reset');

La vista devuelta por esta ruta debe mostrar un formulario que contenga un campo `email`, un campo `password`, un campo `password_confirmation`, y un campo `token` oculto, que debe contener el valor del `$token` secreto recibido por nuestra ruta.

[]()

#### Gestión del envío del formulario

Por supuesto, necesitamos definir una ruta para gestionar el envío del formulario de restablecimiento de contraseña. Esta ruta será responsable de validar la solicitud entrante y actualizar la contraseña del usuario en la base de datos:

    use Illuminate\Auth\Events\PasswordReset;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Password;
    use Illuminate\Support\Str;

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    })->middleware('guest')->name('password.update');

Antes de continuar, examinemos esta ruta con más detalle. En primer lugar, se validan los atributos `token`, `email` y `contraseña` de la petición. A continuación, utilizaremos el "password broker" integrado de Laravel (a través de la facade `Password` ) para validar las credenciales de la solicitud de restablecimiento de contraseña.

Si el token, la dirección de correo electrónico y la contraseña facilitados al agente de contraseñas son válidos, se invocará el closure pasado al método de `restablecimiento`. Dentro de este closure, que recibe la instancia del usuario y la contraseña en texto plano proporcionada al formulario de restablecimiento de contraseña, podemos actualizar la contraseña del usuario en la base de datos.

El método `reset` devuelve un slug "status". Este estado puede ser traducido usando los ayudantes de [localización](/docs/%7B%7Bversion%7D%7D/localization) de Laravel para mostrar un mensaje amigable al usuario sobre el estado de su solicitud. La traducción del estado de restablecimiento de contraseña viene determinada por el archivo de idioma `lang/{lang}/passwords.php` de tu aplicación. En el archivo de idioma de `passwords` hay una entrada para cada valor posible del slug de estado.

Antes de continuar, puede que te estés preguntando cómo Laravel sabe cómo recuperar el registro de usuario de la base de datos de tu aplicación cuando se llama al método `reset` de la facade `Password`. El broker de contraseñas de Laravel utiliza los "proveedores de usuario" de tu sistema de autenticación para recuperar los registros de la base de datos. El proveedor de usuario utilizado por el gestor de contraseñas se configura en el array configuración `passwords` del fichero de configuración `config/auth.` php. Para obtener más información sobre cómo escribir proveedores de usuario personalizados, consulte la [documentación de autenticación](/docs/%7B%7Bversion%7D%7D/authentication#adding-custom-user-providers).

[]()

## Eliminación de tokens caducados

Los tokens de restablecimiento de contraseña que hayan caducado seguirán presentes en la base de datos. Sin embargo, puede eliminar fácilmente estos registros utilizando el comando `auth:clear-resets` Artisan:

```shell
php artisan auth:clear-resets
```

Si desea automatizar este proceso, considere añadir el comando al [programador de](/docs/%7B%7Bversion%7D%7D/scheduling) su aplicación:

    $schedule->command('auth:clear-resets')->everyFifteenMinutes();

[]()

## Personalización

[]()

#### Restablecer personalización de enlaces

Puede personalizar la URL del enlace de `restablecimiento` de contraseña utilizando el método `createUrlUsing` proporcionado por la clase de notificación `ResetPassword`. Este método acepta un closure que recibe la instancia de usuario que está recibiendo la notificación, así como el token del enlace de restablecimiento de contraseña. Típicamente, usted debería llamar a este método desde el método de `arranque` de su proveedor de servicios `App\Providers\AuthServiceProvider`:

    use Illuminate\Auth\Notifications\ResetPassword;

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return 'https://example.com/reset-password?token='.$token;
        });
    }

[]()

#### Restablecer personalización de correo electrónico

Puede modificar fácilmente la clase de notificación utilizada para enviar el enlace de restablecimiento de contraseña al usuario. Para empezar, anule el método `sendPasswordResetNotification` en su modelo `AppModels\User`. Dentro de este método, puede enviar la notificación utilizando cualquier clase de [notificación](/docs/%7B%7Bversion%7D%7D/notifications) de su propia creación. El `$token` de restablecimiento de contraseña es el primer argumento recibido por el método. Puede utilizar este `$token` para construir la URL de restablecimiento de contraseña de su elección y enviar su notificación al usuario:

    use App\Notifications\ResetPasswordNotification;

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url = 'https://example.com/reset-password?token='.$token;

        $this->notify(new ResetPasswordNotification($url));
    }
