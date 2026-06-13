<?php

namespace Larapen\LaravelLocalization\Middleware;


class LaravelLocalizationMiddlewareBase
{
    /**
     * The URIs that should not be localized.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Determine if the request has a URI that should not be localized.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldIgnore($request)
    {
        // Skip static asset requests (css, js, images, fonts, etc.)
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot|map|webp|pdf)(\?.*)?$/', $request->getPathInfo())) {
            return true;
        }

        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
