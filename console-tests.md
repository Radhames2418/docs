# tests consola

- [Introducción](#introduction)
- [Expectativas de éxito / fracaso](#success-failure-expectations)
- [Expectativas de Entrada / Salida](#input-output-expectations)

[]()

## Introducción

Además de simplificar las pruebas HTTP, Laravel proporciona una API sencilla para probar los [comandos de consola personalizados](/docs/%7B%7Bversion%7D%7D/artisan) de su aplicación.

[]()

## Expectativas de éxito / fracaso

Para comenzar, exploremos cómo hacer aseveraciones con respecto al código de salida de un comando Artisan. Para lograr esto, utilizaremos el método `artisan` para invocar un comando Artisan desde nuestra test. Luego, utilizaremos el método `assertExitCode` para afirmar que el comando se completó con un código de salida dado:

    /**
     * Test a console command.
     *
     * @return void
     */
    public function test_console_command()
    {
        $this->artisan('inspire')->assertExitCode(0);
    }

Puede utilizar el método `assertNotExitCode` para afirmar que el comando no salió con un código de salida dado:

    $this->artisan('inspire')->assertNotExitCode(1);

Por supuesto, todos los comandos de terminal normalmente salen con un código de estado `0` cuando tienen éxito y un código de salida distinto de cero cuando no tienen éxito. Por lo tanto, por conveniencia, puede utilizar las aserciones `assertSuccessful` y `assertFailed` para afirmar que un comando dado salió con un código de salida exitoso o no:

    $this->artisan('inspire')->assertSuccessful();

    $this->artisan('inspire')->assertFailed();

[]()

## Expectativas de entrada/salida

Laravel le permite fácilmente "simular" la entrada del usuario para sus comandos de consola utilizando el método `expectsQuestion`. Además, puedes especificar el código de salida y el texto que esperas que salga por el comando de consola utilizando los métodos `assertExitCode` y `expectsOutput`. Por ejemplo, considere el siguiente comando de consola:

    Artisan::command('question', function () {
        $name = $this->ask('What is your name?');

        $language = $this->choice('Which language do you prefer?', [
            'PHP',
            'Ruby',
            'Python',
        ]);

        $this->line('Your name is '.$name.' and you prefer '.$language.'.');
    });

Puede test este comando con la siguiente test que utiliza los métodos `expectsQuestion`, `expectsOutput`, `doesntExpectOutput`, `expectsOutputToContain`, `doesntExpectOutputToContain`, y `assertExitCode`:

    /**
     * Test a console command.
     *
     * @return void
     */
    public function test_console_command()
    {
        $this->artisan('question')
             ->expectsQuestion('What is your name?', 'Taylor Otwell')
             ->expectsQuestion('Which language do you prefer?', 'PHP')
             ->expectsOutput('Your name is Taylor Otwell and you prefer PHP.')
             ->doesntExpectOutput('Your name is Taylor Otwell and you prefer Ruby.')
             ->expectsOutputToContain('Taylor Otwell')
             ->doesntExpectOutputToContain('you prefer Ruby')
             ->assertExitCode(0);
    }

[]()

#### Expectativas de confirmación

Cuando escribas un comando que espera confirmación en forma de respuesta "sí" o "no", puedes utilizar el método `expectsConfirmation`:

    $this->artisan('module:import')
        ->expectsConfirmation('Do you really wish to run this command?', 'no')
        ->assertExitCode(1);

[]()

#### Expectativas de tabla

Si su comando muestra una tabla de información utilizando el método de `tabla` de Artisan, puede ser engorroso escribir expectativas de salida para toda la tabla. En su lugar, puede utilizar el método `expectsTable`. Este método acepta las cabeceras de la tabla como primer argumento y los datos de la tabla como segundo argumento:

    $this->artisan('users:all')
        ->expectsTable([
            'ID',
            'Email',
        ], [
            [1, 'taylor@example.com'],
            [2, 'abigail@example.com'],
        ]);
