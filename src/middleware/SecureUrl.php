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

    public function __construct()
    {
        $this->mustBeSecure = config('o2helper.must_be_secure');
    }

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
        $secure = false;

        if ($path != '/') {
            $path = trim($path, '/');
        }

        foreach ($this->mustBeSecure as $mustBeSecure) {
            if (Str::is($mustBeSecure, $path)) {
                $secure = true;
                break;
            }
        }

        return $secure;
    }
}