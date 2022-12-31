# Hashing

- [Introducción](#introduction)
- [Configuración](#configuration)
- [Uso básico](#basic-usage)
  - [Cifrado de contraseñas](#hashing-passwords)
  - [Verificación de la coincidencia de una contraseña con un hash](#verifying-that-a-password-matches-a-hash)
  - [Determinar si una contraseña necesita ser reutilizada](#determining-if-a-password-needs-to-be-rehashed)

[]()

## Introducción

La [facade](/docs/%7B%7Bversion%7D%7D/facades) Laravel `Hash` proporciona hashing seguro Bcrypt y Argon2 para almacenar contraseñas de usuario. Si estás utilizando uno de los [kits de inicio de aplicaciones Lar](/docs/%7B%7Bversion%7D%7D/starter-kits)avel, Bcrypt se utilizará por defecto para el registro y la autenticación.

Bcrypt es una gran elección para el hash de contraseñas porque su "factor de trabajo" es ajustable, lo que significa que el tiempo que tarda en generar un hash puede incrementarse a medida que aumenta la potencia del hardware. En el hash de contraseñas, la lentitud es buena. Cuanto más tarde un algoritmo en generar el hash de una contraseña, más tiempo tardarán los usuarios malintencionados en generar "tablas arco iris" con todos los posibles valores hash de cadenas que pueden utilizarse en ataques de fuerza bruta contra las aplicaciones.

[]()

## Configuración

El controlador hash predeterminado para su aplicación se configura en el archivo de configuración `config/hashing.php` de su aplicación. Actualmente hay varios controladores soportados: [Bcrypt](https://en.wikipedia.org/wiki/Bcrypt) y [Argon2](https://en.wikipedia.org/wiki/Argon2) (variantes Argon2i y Argon2id).

[]()

## Uso Básico

[]()

### Cifrado de Contraseñas

Puede hacer un hash de una contraseña llamando al método `make` de la facade `Hash`:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;

    class PasswordController extends Controller
    {
        /**
         * Update the password for the user.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request)
        {
            // Validate the new password length...

            $request->user()->fill([
                'password' => Hash::make($request->newPassword)
            ])->save();
        }
    }

[]()

#### Ajuste del factor de trabajo de Bcrypt

Si está utilizando el algoritmo Bcrypt, el método `make` le permite gestionar el factor de trabajo del algoritmo utilizando la opción `rounds`; sin embargo, el factor de trabajo por defecto gestionado por Laravel es aceptable para la mayoría de las aplicaciones:

    $hashed = Hash::make('password', [
        'rounds' => 12,
    ]);

[]()

#### Ajustando el factor de trabajo de Argon2

Si estás utilizando el algoritmo Argon2, el método `make` te permite gestionar el factor de trabajo del algoritmo utilizando las opciones `memory`, `time` y `threads`; sin embargo, los valores por defecto gestionados por Laravel son aceptables para la mayoría de las aplicaciones:

    $hashed = Hash::make('password', [
        'memory' => 1024,
        'time' => 2,
        'threads' => 2,
    ]);

> **Nota**  
> Para más información sobre estas opciones, por favor consulte la [documentación oficial de PHP referente al hashing Argon](https://secure.php.net/manual/en/function.password-hash.php).

[]()

### Verificación de que una contraseña coincide con un hash

El método `check` proporcionado por la facade `Hash` permite verificar que una cadena de texto plano dada se corresponde con un hash dado:

    if (Hash::check('plain-text', $hashedPassword)) {
        // The passwords match...
    }

[]()

### Determinar si una contraseña necesita ser reutilizada

El método `needsRehash` proporcionado por la facade `Hash` permite determinar si el factor de trabajo utilizado por el hasher ha cambiado desde que se realizó el hash de la contraseña. Algunas aplicaciones optan por realizar esta comprobación durante el proceso de autenticación de la aplicación:

    if (Hash::needsRehash($hashed)) {
        $hashed = Hash::make('plain-text');
    }
