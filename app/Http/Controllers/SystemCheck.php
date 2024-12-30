<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class SystemCheck extends Controller
{
    public function main()
    {
        try {
            Artisan::call('app:system-check');
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
