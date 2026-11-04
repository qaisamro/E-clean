<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = App\Models\User::find(16);
\Illuminate\Support\Facades\Auth::loginUsingId($u->id);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$o = App\Models\Order::first();
$req = Illuminate\Http\Request::create("/orders/".$o->id."/print-invoice","GET");
$res = $kernel->handle($req);
echo $res->getStatusCode()."|".$res->headers->get("Content-Type")."|".strlen($res->getContent());
