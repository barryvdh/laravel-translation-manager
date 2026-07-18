<?php namespace Barryvdh\TranslationManager;

use Illuminate\Translation\Translator as LaravelTranslator;
use Illuminate\Events\Dispatcher;

class Translator extends LaravelTranslator {

    /** @var  Dispatcher */
    protected $events;

    /**
     * Get the translation for the given key.
     *
     * @param string $key
     * @param array $replace
     * @param null $locale
     * @param bool $fallback
     * @return string
     */
    public function get($key, array $replace = array(), $locale = null, $fallback = true): string
    {
        // Get without a fallback
        $result = parent::get($key, $replace, $locale, false);
        if($result === $key){
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
        list($namespace, $group, $item) = $this->parseKey($key);
        if($this->manager && $namespace === '*' && $group && $item ){
            $this->manager->missingKey($namespace, $group, $item);
        }
    }

}
