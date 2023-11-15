<?php

namespace App\Exception;

/**
 * Кастомный exception для ошибки авторизации
 *
 * @author Daniil Ilin <daniil.ilin@gmail.com>
 */
class AuthException extends \Exception
{
    public const ERROR_KEY = 'signature_error';

    protected $message = 'Ошибка авторизации в приложении';
}