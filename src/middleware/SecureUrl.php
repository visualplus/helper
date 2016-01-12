<?php namespace Visualplus\Helper\Middleware;

use Illuminate\Support\Str;

use Closure;

class SecureUrl
{
    /**
     * ssl을 적용해야하는 url
     * @var array
     */
    protected $mustBeSecure = [];

    /**
     * ssl 적용하지 않는 url
     * @var array
     */
    protected $except = [];

    public function __construct()
    {
        $this->mustBeSecure = config('o2helper.must_be_secure');
        $this->except = config('o2helper.except');
    }

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = $request->path();
        $mustBeSecure = $this->isSecurePath($path);

        if ($mustBeSecure != $request->isSecure()) {
            $url = url($path, [], $mustBeSecure);

            if ($request->method() === 'GET') {
                $url .= '?' . http_build_query($request->all());
            }

            return redirect($url);
        }

        return $next($request);
    }

    /**
     * 넘겨받은 path가 ssl 적용 대상인지 판단.
     *
     * @param string $path
     * @return bool
     */
    protected function isSecurePath($path)
    {
        if ($path != '/') {
            $path = trim($path, '/');
        }

        foreach ($this->except as $except) {
            if (preg_match('#^' . $except . '#', $path)) {
                return false;
            }
        }

        foreach ($this->mustBeSecure as $mustBeSecure) {
            if (preg_match('#^' . $mustBeSecure . '#', $path)) {
                return true;
            }
        }

        return false;
    }
}