<?php

namespace Local\Traits;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use http\Exception\RuntimeException;
use Throwable;

trait ModuleRequireTrait
{
    /**
     * @throws Throwable
     * @throws LoaderException
     */
    protected function requireModule(string $moduleId, string $exceptionClass = \RuntimeException::class, ?string $message = null): void
    {
        if (Loader::includeModule($moduleId)) {
            return;
        }

        $message = $message ?? $this->defaultModuleMessage($moduleId);

        if (is_a($exceptionClass, Throwable::class, true)) {
            throw new $exceptionClass($message);
        }

        throw new RuntimeException($message);
    }

    /**
     * @throws Throwable
     * @throws LoaderException
     */
    protected function requireModules(array $modules): void
    {
        foreach ($modules as $key => $value) {
            if (is_int($key)) {
                $this->requireModule($value);
            } else {
                $moduleId = $key;

                if (is_array($value)) {
                    [$exClass, $message] = $value + [
                        0 => RuntimeException::class,
                        1 => null
                    ];
                    $this->requireModule($moduleId, $exClass, $message);
                } else {
                    $this->requireModule($moduleId, RuntimeException::class, (string)$value);
                }
            }
        }
    }

    private function defaultModuleMessage(string $moduleId): string
    {
        return match ($moduleId) {
            'main' => 'Модуль main не загружен',
            'iblock' => 'Инфоблок не установлен',
            default => "Модуль $moduleId не установлен или не загружен",
        };
    }
}