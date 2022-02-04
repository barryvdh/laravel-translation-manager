<?php

namespace Barryvdh\TranslationManager;

use Illuminate\Events\Dispatcher;
use Illuminate\Translation\Translator as LaravelTranslator;

class Translator extends LaravelTranslator
{
    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $events;
    /**
     * @var \Barryvdh\TranslationManager\Manager
     */
    private $manager;

    /**
     * Get the translation for the given key.
     *
     * @param string $key
     * @param string $locale
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true): string
    {
        // Get without fallback
        $result = parent::get($key, $replace, $locale, false);
        if ($result === $key) {
            $this->notifyMissingKey($key);

            // Reget with fallback
            $result = parent::get($key, $replace, $locale, $fallback);
        }

        return $result;
    }

    public function setTranslationManager(Manager $manager): void
    {
        $this->manager = $manager;
    }

    protected function notifyMissingKey($key): void
    {
        [$namespace, $group, $item] = $this->parseKey($key);
        if ($this->manager && '*' === $namespace && $group && $item) {
            $this->manager->missingKey($namespace, $group, $item);
        }
    }
}
