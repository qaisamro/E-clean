<?php
declare(strict_types=1);
chdir('/home/u205641829/domains/elitecleanps.com/laravel');
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$backup = trim(file_get_contents('/tmp/ec-invoice-backup-path'));
$results = [];
function check(bool $condition, string $name): void {
    global $results;
    if (!$condition) throw new RuntimeException('Verification failed: '.$name);
    $results[] = $name;
    echo 'PASS '.$name.PHP_EOL;
}
function requestAs($user, string $url) {
    global $app;
    Illuminate\Support\Facades\Auth::guard('web')->setUser($user);
    $request = Illuminate\Http\Request::create('https://elitecleanps.com'.$url, 'GET');
    return $app->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
}
foreach (json_decode(file_get_contents($backup.'/manifest.json'), true) as $x) {
    check(hash_file('sha256', $x['path']) === $x['after'], 'file '.$x['path']);
}
$owners = App\Models\User::role('vendor_admin')->whereHas('vendor')->get();
check($owners->isNotEmpty(), 'linked vendor accounts found');
$printOrders = [];
foreach ($owners as $owner) {
    $vendor = $owner->vendor;
    $response = requestAs($owner, '/vendor/invoice-template');
    check($response->getStatusCode() === 200, 'vendor '.$vendor->id.' editor HTTP 200');
    check(str_contains($response->getContent(), 'id="invoice-preview"'), 'vendor '.$vendor->id.' editor includes live preview');
    $response = requestAs($owner, '/vendor/invoice-template/preview');
    check($response->getStatusCode() === 200, 'vendor '.$vendor->id.' empty preview HTTP 200');
    $orders = App\Support\OrderVendorOwnership::filterConsistentOrders(
        App\Models\Order::where('vendor_id', $vendor->id)->with('products.service')->latest()->limit(50)->get()
    );
    if ($order = $orders->first()) {
        $printOrders[$vendor->id] = $order;
        $dataHash = null;
        $settingsHash = null;
        foreach (['normal', 'customer', 'store'] as $copy) {
            $response = requestAs($owner, '/orders/'.$order->id.'/pos-invoice?copy='.$copy);
            check($response->getStatusCode() === 200, 'vendor '.$vendor->id.' '.$copy.' print HTTP 200');
            $payload = $response->getOriginalContent()->getData()['payload'];
            check($payload['copy'] === $copy && $payload['data']['order']['id'] === $order->id,
                'vendor '.$vendor->id.' '.$copy.' identifies actual order');
            check((float) $payload['data']['order']['total'] === (float) $order->total_amount,
                'vendor '.$vendor->id.' '.$copy.' uses stored total');
            $hash = hash('sha256', json_encode($payload['data']));
            $settings = hash('sha256', json_encode($payload['settings']));
            check(($dataHash === null || $dataHash === $hash) && ($settingsHash === null || $settingsHash === $settings),
                'vendor '.$vendor->id.' '.$copy.' shares data and settings');
            $dataHash = $hash; $settingsHash = $settings;
        }
        $response = requestAs($owner, '/vendor/invoice-template/preview?order='.$order->id);
        check($response->getStatusCode() === 200, 'vendor '.$vendor->id.' real preview HTTP 200');
    }
    // Exercise actual MySQL persistence inside an outer transaction, then undo it.
    // No logo upload/removal is involved, so there are no nontransactional file changes.
    Illuminate\Support\Facades\Auth::guard('web')->setUser($owner);
    $before = App\Models\VendorInvoiceTemplate::where('vendor_id', $vendor->id)->first()?->getAttributes();
    Illuminate\Support\Facades\DB::beginTransaction();
    try {
        $settings = App\Support\InvoiceTemplate::defaults();
        $settings['text']['title'] = 'Temporary deployment verification';
        $request = Illuminate\Http\Request::create('/vendor/invoice-template', 'POST', ['settings' => $settings]);
        $response = $app->make(App\Http\Controllers\Web\VendorInvoiceTemplateController::class)->update($request);
        check($response->getStatusCode() === 200, 'vendor '.$vendor->id.' transactional save HTTP 200');
        check(App\Models\VendorInvoiceTemplate::where('vendor_id', $vendor->id)->firstOrFail()->settings['text']['title'] === $settings['text']['title'],
            'vendor '.$vendor->id.' MySQL save/read verified');
    } finally { Illuminate\Support\Facades\DB::rollBack(); }
    check(App\Models\VendorInvoiceTemplate::where('vendor_id', $vendor->id)->first()?->getAttributes() === $before,
        'vendor '.$vendor->id.' test save rolled back fully');
}
check(count($printOrders) > 0, 'real orders available for copy verification');
foreach ($owners as $owner) {
    foreach ($printOrders as $vendorId => $order) {
        if ((int) $owner->vendor->id === (int) $vendorId) continue;
        foreach (['/orders/'.$order->id.'/pos-invoice', '/vendor/invoice-template/preview?order='.$order->id] as $url) {
            check(requestAs($owner, $url)->getStatusCode() === 404, 'foreign order blocked '.$owner->vendor->id.' '.$url);
        }
    }
}
$root = App\Models\User::role('root')->first();
if ($root) {
    $order = reset($printOrders);
    check(requestAs($root, '/orders/'.$order->id.'/pos-invoice')->getStatusCode() === 200, 'root real invoice HTTP 200');
    check(requestAs($root, '/vendor/invoice-template')->getStatusCode() === 403, 'root cannot edit another owners template');
}
$customer = App\Models\User::role('customer')->first();
if ($customer) {
    check(requestAs($customer, '/vendor/invoice-template')->getStatusCode() === 403, 'customer editor denied');
    $order = reset($printOrders);
    check(requestAs($customer, '/orders/'.$order->id.'/pos-invoice')->getStatusCode() === 403, 'customer print denied');
}
$before = json_decode(file_get_contents($backup.'/invariants.before.json'), true);
foreach ($before as $table => $expected) {
    if (!is_array($expected)) continue;
    $rows = Illuminate\Support\Facades\DB::table($table)->get()->map(fn ($r) => json_encode($r))->sort()->values()->all();
    check(count($rows) === $expected['count'] && hash('sha256', json_encode($rows)) === $expected['sha256'], 'unchanged table '.$table);
}
clearstatcache();
check(filesize('storage/logs/laravel.log') === $before['log_bytes'], 'no new Laravel log bytes');
file_put_contents($backup.'/verification.json', json_encode(['checks' => $results, 'time_utc' => gmdate('c')], JSON_PRETTY_PRINT));
echo 'COMPLETE '.count($results).' verification checks'.PHP_EOL;
