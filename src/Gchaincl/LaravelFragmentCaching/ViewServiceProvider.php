<?php namespace Gchaincl\LaravelFragmentCaching;

class ViewServiceProvider extends \Illuminate\View\ViewServiceProvider
{
    protected $defered = false;

    public function register()
    {
        parent::register();
        $this->registerBladeExtensions();
    }

    protected function registerBladeExtensions()
    {
        $blade = $this->app['view']
            ->getEngineResolver()
            ->resolve('blade')
            ->getCompiler();

        $blade->extend(function($view, $compiler) {
            $pattern = $compiler->createMatcher('cache');
            if(!preg_match($pattern, $view, $matched))
                return $view;
            return preg_replace($pattern, '$1' . $this->cacheTemplate(), $view);
        });

        $blade->extend(function($view, $compiler) {
            $pattern = $compiler->createPlainMatcher('endcache');
            return preg_replace($pattern, '$1<?php }); ?>', $view);
        });

    }

    private function cacheTemplate()
    {
        return <<<'EOF'
<?php
if ( ! function_exists('cache') )
{
    function cache($key, Closure $closure)
    {
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

$__fc_vars = get_defined_vars();
echo cache($2, function() use($__fc_vars) {
    foreach($__fc_vars as $k => $v) {
        $$k = $v;
    };

?>
EOF;
    }
}
