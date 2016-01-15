<?php namespace Visualplus\Helper;

use Illuminate\Routing\RouteCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SecureUrlGenerator extends \Illuminate\Routing\UrlGenerator
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

    /**
     * SecureUrlGenerator constructor.
     * @param RouteCollection $routes
     * @param Request $request
     */
    public function __construct(RouteCollection $routes, Request $request)
    {
        parent::__construct($routes, $request);

        $this->mustBeSecure = config('o2helper.must_be_secure');

        $this->except = config('o2helper.except');
    }

    /**
     * secure url 적용
     *
     * @param $path
     * @param array $extra
     * @param bool $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = false)
    {
        $secure = ( $secure || $this->isSecurePath($path) );

        return parent::to($path, $extra, $secure);
    }

    /**
     * route로 지정된 url은 이 trimUrl을 거쳐 해당 url을 생성하게 됨.
     * 이 함수에서 ssl을 적용할지를 결정함.
     *
     * @param $root
     * @param $path
     * @param string $tail
     * @return string
     */
    protected function trimUrl($root, $path, $tail = '')
    {
        if ($this->isSecurePath($path)) {
            $root = preg_replace('/https?:\/\//', 'https://', $root);
        } else {
            $root = preg_replace('/https?:\/\//', 'http://', $root);
        }

        return parent::trimUrl($root, $path, $tail);
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