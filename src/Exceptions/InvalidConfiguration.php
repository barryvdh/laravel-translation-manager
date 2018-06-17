<?php

namespace Barryvdh\TranslationManager\Exceptions;

use Exception;
use Barryvdh\TranslationManager\Models\Translation;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className)
    {
        return new static("The given model class `$className` does not extend `".Translation::class.'`');
    }
}
