<?php
namespace U9\Cors\Middleware;

use Closure;

class Cors
{

    protected $maxAge = 31536000; //一年
    protected $allowCredentials = true;
    protected $allowMethods = 'GET,HEAD,PUT,POST,DELETE,PATCH,OPTIONS';
    protected $allowHeaders = 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, u9-Token, u9-Signature, Accept-ApiKey, Accept-ApiSign, Accept-ApiTime, Accept-Language, X-Request-Id';
    protected $exposeHeaders;

    protected function setOrigin($req, $rsp)
    {
        $referer = $req->header('referer');
        $origin = $req->header('origin');

        if ($referer !== null || $origin !== null) {
            if ($origin) {
                $info = parse_url($origin);
            } else {
                $info = parse_url($referer);
            }
            $host = strtolower($info['host'] ?? '');
            preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
            if (isset($matches[0]) && ( $matches[0] === 'uuu9.cn' || $matches[0] === 'uuu9.com' )) {
                $origin = $info['scheme'].'://'.$info['host'];
                $rsp->header('Access-Control-Allow-Origin', $origin);
            }
        }
    }

    protected function setExposeHeaders($req, $rsp)
    {
        if ($this->exposeHeaders !== null) {
            $exposeHeaders = $this->exposeHeaders;
            if (is_array($exposeHeaders)) {
                $exposeHeaders = implode(', ', $exposeHeaders);
            }

            $rsp->header('Access-Control-Expose-Headers', $exposeHeaders);
        }
    }

    protected function setMaxAge($req, $rsp)
    {
        if ($this->maxAge !== null) {
            $rsp->header('Access-Control-Max-Age', $this->maxAge);
        }
    }

    protected function setAllowCredentials($req, $rsp)
    {
        if ($this->allowCredentials) {
            $rsp->header('Access-Control-Allow-Credentials', 'true');
        }
    }

    protected function setAllowMethods($req, $rsp)
    {
        if ($this->allowMethods !== null) {
            $allowMethods = $this->allowMethods;
            if (is_array($allowMethods)) {
                $allowMethods = implode(', ', $allowMethods);
            }
            
            $rsp->header('Access-Control-Allow-Methods', $allowMethods);
        }
    }

    protected function setAllowHeaders($req, $rsp)
    {
        if ($this->allowHeaders !== null) {
            $allowHeaders = $this->allowHeaders;
            if (is_array($allowHeaders)) {
                $allowHeaders = implode(', ', $allowHeaders);
            }
        } else {  // Otherwise, use request headers
            $allowHeaders = $req->header('Access-Control-Request-Headers');
        }

        if ($allowHeaders !== null) {
            $rsp->header('Access-Control-Allow-Headers', $allowHeaders);
        }
    }

    protected function setCorsHeaders($req, $rsp)
    {

        $this->setOrigin($req, $rsp);
        $this->setExposeHeaders($req, $rsp);
        $this->setAllowCredentials($req, $rsp);
    }

    /**
     * options preflight
     *
     * @param $req
     * @param $rsp
     * @return mixed
     */
    public function setOptionsHeaders($req, $rsp)
    {
        $this->setOrigin($req, $rsp);
        $this->setMaxAge($req, $rsp);
        $this->setAllowCredentials($req, $rsp);
        $this->setAllowMethods($req, $rsp);
        $this->setAllowHeaders($req, $rsp);

        return $rsp;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->isMethod('OPTIONS')) {
            return $this->setOptionsHeaders($request, response('', 204));
        }

        $this->setCorsHeaders($request, $response);

        return $response;
    }

}