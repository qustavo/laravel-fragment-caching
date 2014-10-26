<?php

if ( ! function_exists('cache') )
{
    function cache($key, $condition = true, Closure $closure)
    {
        $content = $condition ? Cache::get($key) : false;
        if ( ! $content ) {
            ob_start();
            
            $closure();
            $content = ob_get_contents();
            ob_end_clean();
	    if ($condition) {
		    Cache::forever($key, $content);
		    Log::debug('writing cache', [$key]);
	    }
        } else {
            Log::debug('reading cache', [$key]);
        }

        return $content;
    }
}
