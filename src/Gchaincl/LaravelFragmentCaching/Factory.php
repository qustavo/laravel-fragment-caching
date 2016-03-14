<?php namespace Gchaincl\LaravelFragmentCaching;

class Factory extends \Illuminate\View\Factory {

    public function cacheif($condition, $key, $minutes, \Closure $closure)
    {
        if ( ! $condition ) {
            return $closure();
        }
        $cache = $this->getContainer()['cache'];
        $log = $this->getContainer()['log'];

        $content = $cache->get($key);
        if ( ! $content ) {
            ob_start();

            $closure();
            $content = ob_get_contents();
            ob_end_clean();
            $cache->put($key, $content, $minutes);
            $log->debug('writing cache', [$key]);
        } else {
            $log->debug('reading cache', [$key]);
        }

        return $content;
    }

    public function cache($key, $minutes, \Closure $closure)
    {
        return $this->cacheif(true, $key, $minutes, $closure);
    }

}
