<?php

if ( ! function_exists('cacheif') )
{
    function cacheif($condition, $key, Closure $closure)
    {
        if ( ! $condition ) {
            return $closure();
	}
        $content = Cache::get($key);
        if ( ! $content ) {
            ob_start();
            $closure();
            $content = ob_get_contents();
            ob_end_clean();
            Cache::forever($key, $content);
            Log::debug('writing cache', [$key]);
        } else {
            Log::debug('reading cache', [$key]);
        }

        return $content;
    }
}

if ( ! function_exists('cache') )
{
    function cache($key, Closure $closure)
    {
        return cacheif(true, $key, $closure);
    }
}


