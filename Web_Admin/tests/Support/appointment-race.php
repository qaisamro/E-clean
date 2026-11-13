<?php
// Opt-in integration probe. It can ONLY use the isolated local test database.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default'=>'mysql', 'database.connections.mysql.host'=>'127.0.0.1',
    'database.connections.mysql.port'=>3309, 'database.connections.mysql.database'=>'ec_appointments_test',
    'database.connections.mysql.username'=>'root', 'database.connections.mysql.password'=>'',
    'database.connections.mysql.url'=>null, 'database.connections.mysql.unix_socket'=>'',
    'cache.default'=>'array', 'session.driver'=>'array', 'app.env'=>'testing']);
Illuminate\Support\Facades\DB::purge('mysql');
$db = Illuminate\Support\Facades\DB::connection();
if ($db->getDatabaseName() !== 'ec_appointments_test' || $db->getConfig('host') !== '127.0.0.1' || (int)$db->getConfig('port') !== 3309) exit(90);
Carbon\CarbonImmutable::setTestNow(Carbon\CarbonImmutable::parse('2026-11-09 07:00', 'Asia/Hebron'));
$folder = realpath(__DIR__.'/../../../.local/appointment-mysql');
if (!$folder) exit(91);
$mode = $argv[1] ?? '';
if ($mode === 'init') {
    Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force'=>true]);
    $u=App\Models\User::create(['first_name'=>'Race','last_name'=>'Test','mobile'=>'race-test','email'=>'race@example.test','password'=>bcrypt('test'),'is_active'=>true]);
    $v=App\Models\Vendor::create(['name'=>'Race Test','slug'=>'race-test','user_id'=>$u->id,'is_active'=>true]);
    $c=App\Models\Customer::create(['user_id'=>$u->id]);
    $a=App\Models\Address::create(['customer_id'=>$c->id,'address_name'=>'Test']);
    $s=App\Models\Service::create(['name'=>'Test','vendor_id'=>$v->id,'is_active'=>true]);
    $variant=App\Models\Variant::create(['name'=>'Test']);
    $p=App\Models\Product::create(['name'=>'Test','vendor_id'=>$v->id,'service_id'=>$s->id,'variant_id'=>$variant->id,'price'=>20,'is_active'=>true]);
    $service=app(App\Services\VendorAppointments::class);$settings=$service->defaults();
    $settings['enabled']=true;$settings['processing_minutes']=60;
    $settings['weekly']=['pickup'=>[['day'=>1,'start'=>'09:00','end'=>'11:00','capacity'=>1]],'delivery'=>[['day'=>1,'start'=>'15:00','end'=>'17:00','capacity'=>1]]];
    $service->save($v->id,$settings);
    file_put_contents($folder.'/fixture.json',json_encode(['user'=>$u->id,'vendor'=>$v->id,'product'=>$p->id,'address'=>$a->id]));
    echo "Isolated fixture ready\n";
} elseif ($mode === 'worker') {
    $id=$argv[2];$f=json_decode(file_get_contents($folder.'/fixture.json'),true);
    auth()->loginUsingId($f['user']);
    $request=App\Http\Requests\OrderRequest::create('/api/orders','POST',[
        'vendor_id'=>$f['vendor'],'products'=>[['id'=>$f['product'],'quantity'=>1]],'address_id'=>$f['address'],
        'pick_date'=>'2026-11-09','pick_hour'=>'09:00','delivery_date'=>'2026-11-09','delivery_hour'=>'15:00','payment_type'=>'cash']);
    $db->beginTransaction();
    // Both requests establish a REPEATABLE READ snapshot BEFORE either can book.
    App\Models\Product::findOrFail($f['product']);
    file_put_contents($folder.'/ready-'.$id,'ready');
    $deadline=microtime(true)+15;
    while (!file_exists($folder.'/go')) {if(microtime(true)>$deadline)throw new RuntimeException('Barrier timeout');usleep(10000);}
    try {
        $o=(new App\Repositories\OrderRepository)->storeByRequest($request);
        $db->commit();echo json_encode(['result'=>'booked','order'=>$o->id]);
    } catch (Illuminate\Validation\ValidationException $e) {
        $db->rollBack();echo json_encode(['result'=>'rejected','errors'=>array_keys($e->errors())]);
    } catch (Throwable $e) {
        $db->rollBack();fwrite(STDERR,get_class($e).': '.$e->getMessage());exit(1);
    }
} elseif ($mode === 'verify') {
    echo json_encode(['orders'=>App\Models\Order::count(),'snapshots'=>App\Models\OrderAppointment::count(),
        'isolation'=>$db->selectOne('SELECT @@tx_isolation AS isolation')->isolation]);
} else exit(92);
