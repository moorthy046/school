<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerifyUpdate
{
    protected $except = [
        'api/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {
        if (file_exists(storage_path('installed')) && !$request->is('update*')) {
            $version = config('app.version');
            $current_version = Schema::hasTable('version')?DB::table('version')->first()->version:0;
            if ($version > $current_version) {
                return redirect()->to('update/'.$version);
            }
        }

        return $next($request);
    }
}
