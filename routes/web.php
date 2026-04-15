<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    // Verificar se o sistema está instalado
    $installedFile = storage_path('installed');
    
    if (!File::exists($installedFile)) {
        // Sistema não instalado - redirecionar para instalador
        return redirect('/install');
    }
    
    // Sistema instalado - exibir página inicial do site
    return view('modules.frontend.home');
});
