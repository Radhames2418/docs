# Laravel Pint

- [Introducción](#introduction)
- [Instalación](#installation)
- [Ejecutar Pint](#running-pint)
- [Configurar Pint](#configuring-pint)
  - [Preconfiguraciones](#presets)
  - [Reglas](#rules)
  - [Excluyendo Archivos / Carpetas](#excluding-files-or-folders)

[]()

## Introducción

[Laravel Pint](https://github.com/laravel/pint) es un corrector de estilo de código PHP para minimalistas. Pint está construido sobre PHP-CS-Fixer y hace que sea sencillo asegurarse de que el estilo de su código se mantiene limpio y consistente.

Pint se instala automáticamente con todas las nuevas aplicaciones Laravel, por lo que puede empezar a utilizarlo inmediatamente. Por defecto, Pint no requiere ninguna configuración y corregirá los problemas de estilo de código en su código siguiendo el estilo de codificación de Laravel.

[]()

## Instalación

Pint se incluye en las versiones recientes del framework Laravel, por lo que su instalación suele ser innecesaria. Sin embargo, para aplicaciones más antiguas, puede instalar Laravel Pint a través de Composer:

```shell
composer require laravel/pint --dev
```

[]()

## Ejecutando Pint

Puedes ordenar a Pint que corrija los problemas de estilo de código invocando el binario `pint` que está disponible en el directorio `vendor/bin` de tu proyecto:

```shell
./vendor/bin/pint
```

También puede ejecutar Pint en archivos o directorios específicos:

```shell
./vendor/bin/pint app/Models

./vendor/bin/pint app/Models/User.php
```

Pint mostrará una lista completa de todos los archivos que actualiza. Puede ver incluso más detalles sobre los cambios de Pint proporcionando la opción `-v` cuando invoque a Pint:

```shell
./vendor/bin/pint -v
```

Si desea que Pint simplemente inspeccione su código en busca de errores de estilo sin cambiar realmente los archivos, puede utilizar la opción `--test`:

```shell
./vendor/bin/pint --test
```

[]()

## Configurando Pint

Como se mencionó anteriormente, Pint no requiere ninguna configuración. Sin embargo, si desea personalizar los preajustes, reglas o carpetas inspeccionadas, puede hacerlo creando un archivo `pint.json` en el directorio raíz de su proyecto:

```json
{
    "preset": "laravel"
}
```

Además, si desea utilizar un pint. `json` de un directorio específico, puede proporcionar la opción `--config` al invocar Pint:

```shell
pint --config vendor/my-company/coding-style/pint.json
```

[]()

### Preconfiguraciones

Presets define un conjunto de reglas que pueden utilizarse para corregir problemas de estilo en su código. Por defecto, Pint utiliza el preset `laravel`, que corrige los problemas siguiendo el estilo de codificación de Laravel. Sin embargo, puede especificar un preajuste diferente proporcionando la opción `--preset` a Pint:

```shell
pint --preset psr12
```

Si lo desea, también puede establecer el preajuste en el archivo `pint.json` de su proyecto:

```json
{
    "preset": "psr12"
}
```

Los preajustes actualmente soportados por Pint son: `laravel`, `psr12`, y `symfony`.

[]()

### Reglas

Las reglas son directrices de estilo que Pint utilizará para corregir problemas de estilo en tu código. Como se mencionó anteriormente, los preajustes son grupos predefinidos de reglas que deberían ser perfectas para la mayoría de los proyectos PHP, por lo que normalmente no tendrá que preocuparse por las reglas individuales que contienen.

Sin embargo, si lo desea, puede activar o desactivar reglas específicas en su archivo `pint.json`:

```json
{
    "preset": "laravel",
    "rules": {
        "simplified_null_return": true,
        "braces": false,
        "new_with_braces": {
            "anonymous_class": false,
            "named_class": false
        }
    }
}
```

Pint está construido sobre [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer). Por lo tanto, puede usar cualquiera de sus reglas para arreglar problemas de estilo de código en su proyecto: [Configurador PHP](https://mlocati.github.io/php-cs-fixer-configurator)-CS-Fixer.

[]()

### Excluyendo Archivos / Carpetas

Por defecto, Pint inspeccionará todos los archivos `.` php de su proyecto excepto los del directorio `vendor`. Si desea excluir más carpetas, puede hacerlo utilizando la opción de configuración `excluir`:

```json
{
    "exclude": [
        "my-specific/folder"
    ]
}
```

Si desea excluir todos los archivos que contengan un determinado patrón de nombres, puede hacerlo utilizando la opción de configuración `notName`:

```json
{
    "notName": [
        "*-my-file.php"
    ]
}
```

Si desea excluir un fichero proporcionando una ruta exacta al fichero, puede hacerlo utilizando la opción de configuración `notPath`:

```json
{
    "notPath": [
        "path/to/excluded-file.php"
    ]
}
```
