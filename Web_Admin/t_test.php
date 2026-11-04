<?php
require __DIR__ . "/vendor/autoload.php";
$c = new App\Models\Customer();
$c->setRelation("addresses", collect([]));
$v = optional($c->addresses->first())->address_line ?? "X";
var_dump(gettype($v));
echo "RESULT:";
var_dump($v);
