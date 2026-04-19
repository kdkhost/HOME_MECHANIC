<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $emails = Illuminate\Support\Facades\DB::table('users')->pluck('email');
    file_put_contents('emails_list.txt', implode("\n", $emails->toArray()));
} catch (\Exception $e) {
    file_put_contents('emails_list.txt', 'ERROR: ' . $e->getMessage());
}
