<?php
// Explicit production-only entry point. No credentials are embedded in this file.
declare(strict_types=1);
$root = '/home/u205641829/domains/elitecleanps.com/laravel';
chdir($root);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$items = json_decode(file_get_contents('/tmp/ec-invoice-payload.json'), true, 512, JSON_THROW_ON_ERROR);
$mode = $argv[1] ?? '';
$pointer = '/tmp/ec-invoice-backup-path';

function validatePath(string $path): void {
    if (str_contains($path, '..') || !preg_match('~^(app/|routes/|resources/views/|public/web/|database/migrations/)[a-zA-Z0-9_./-]+$~', $path)) {
        throw new RuntimeException('Unexpected payload path');
    }
}
function verifyOriginal(array $x): void {
    validatePath($x['path']);
    if ($x['before'] === null ? file_exists($x['path']) : (!is_file($x['path']) || hash_file('sha256', $x['path']) !== $x['before'])) {
        throw new RuntimeException('Production differs: '.$x['path']);
    }
    if (hash('sha256', base64_decode($x['data'], true)) !== $x['after']) {
        throw new RuntimeException('Invalid payload');
    }
}

if ($mode === 'prepare') {
    foreach ($items as $x) verifyOriginal($x);
    if (Illuminate\Support\Facades\Schema::hasTable('vendor_invoice_templates')) throw new RuntimeException('Table already exists');
    $backup = '/home/u205641829/backups/invoice-builder-'.gmdate('Ymd\THis\Z');
    if (!mkdir($backup, 0700, true)) throw new RuntimeException('Cannot create backup');
    file_put_contents($pointer, $backup); chmod($pointer, 0600);
    file_put_contents($backup.'/manifest.json', json_encode(array_map(fn ($x) => array_diff_key($x, ['data' => 1]), $items), JSON_PRETTY_PRINT));
    foreach ($items as $x) {
        foreach (['files', 'staged'] as $folder) {
            $dir = $backup.'/'.$folder.'/'.dirname($x['path']);
            if (!is_dir($dir)) mkdir($dir, 0700, true);
        }
        if ($x['before'] !== null && !copy($x['path'], $backup.'/files/'.$x['path'])) throw new RuntimeException('Backup copy failed');
        $staged = $backup.'/staged/'.$x['path'];
        file_put_contents($staged, base64_decode($x['data'], true));
        if (str_ends_with($x['path'], '.php')) {
            $process = proc_open([PHP_BINARY, '-l', $staged], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
            fclose($pipes[0]); $out = stream_get_contents($pipes[1]); fclose($pipes[1]); $err = stream_get_contents($pipes[2]); fclose($pipes[2]);
            if (proc_close($process) !== 0) throw new RuntimeException('PHP lint failed: '.$x['path']);
        }
    }
    $connection = config('database.connections.'.config('database.default'));
    if (($connection['driver'] ?? '') !== 'mysql') throw new RuntimeException('Unexpected database driver');
    $quote = fn ($value) => '"'.str_replace(['\\', '"', "\n", "\r"], ['\\\\', '\\"', '\\n', '\\r'], (string) $value).'"';
    $defaults = $backup.'/mysql-client.cnf';
    $cnf = "[client]\n";
    foreach (['user' => 'username', 'password' => 'password', 'host' => 'host', 'port' => 'port'] as $key => $field) {
        $cnf .= $key.'='.$quote($connection[$field] ?? '')."\n";
    }
    file_put_contents($defaults, $cnf); chmod($defaults, 0600);
    try {
        $process = proc_open(['/usr/bin/mysqldump', '--defaults-extra-file='.$defaults, '--single-transaction', '--quick', '--skip-lock-tables', '--triggers', $connection['database']],
            [0 => ['pipe', 'r'], 1 => ['file', $backup.'/database.sql', 'w'], 2 => ['file', $backup.'/dump.stderr', 'w']], $pipes);
        if (!is_resource($process)) throw new RuntimeException('Cannot start database backup');
        fclose($pipes[0]); $exit = proc_close($process);
        if ($exit !== 0 || filesize($backup.'/database.sql') < 1024) throw new RuntimeException('Database backup failed; inspect private dump.stderr');
        chmod($backup.'/database.sql', 0600);
    } finally { unlink($defaults); }
    $invariants = [];
    foreach (['orders', 'order_products', 'vendors', 'users', 'role_has_permissions', 'model_has_permissions', 'oauth_clients'] as $table) {
        $rows = Illuminate\Support\Facades\DB::table($table)->get()->map(fn ($r) => json_encode($r))->sort()->values()->all();
        $invariants[$table] = ['count' => count($rows), 'sha256' => hash('sha256', json_encode($rows))];
    }
    $invariants['log_bytes'] = is_file('storage/logs/laravel.log') ? filesize('storage/logs/laravel.log') : 0;
    $invariants['routes_cached'] = $app->routesAreCached();
    file_put_contents($backup.'/invariants.before.json', json_encode($invariants, JSON_PRETTY_PRINT));
    echo 'BACKUP='.$backup.PHP_EOL.'DATABASE_BYTES='.filesize($backup.'/database.sql').PHP_EOL.'STAGED_FILES='.count($items).PHP_EOL;
} elseif ($mode === 'migration') {
    $backup = trim(file_get_contents($pointer));
    foreach ($items as $x) verifyOriginal($x);
    $x = $items[0];
    if ($x['path'] !== 'database/migrations/2026_11_08_100000_create_vendor_invoice_templates_table.php') throw new RuntimeException('Unexpected migration');
    copy($backup.'/staged/'.$x['path'], $x['path'].'.invoice-new'); chmod($x['path'].'.invoice-new', 0644);
    if (!rename($x['path'].'.invoice-new', $x['path'])) throw new RuntimeException('Cannot install migration');
    echo 'Migration file installed; existing application files unchanged.'.PHP_EOL;
} elseif ($mode === 'activate') {
    $backup = trim(file_get_contents($pointer));
    if (!Illuminate\Support\Facades\Schema::hasTable('vendor_invoice_templates')) throw new RuntimeException('Migration not applied');
    foreach ($items as $index => $x) {
        if ($index === 0) {
            if (hash_file('sha256', $x['path']) !== $x['after']) throw new RuntimeException('Migration changed');
        } else verifyOriginal($x);
        if (hash_file('sha256', $backup.'/staged/'.$x['path']) !== $x['after']) throw new RuntimeException('Staged file changed');
    }
    foreach ($items as $index => $x) {
        if ($index === 0) continue;
        if (!is_dir(dirname($x['path']))) mkdir(dirname($x['path']), 0755, true);
        copy($backup.'/staged/'.$x['path'], $x['path'].'.invoice-new');
        chmod($x['path'].'.invoice-new', $x['before'] !== null ? fileperms($x['path']) & 0777 : 0644);
        if (!rename($x['path'].'.invoice-new', $x['path'])) throw new RuntimeException('Cannot activate '.$x['path']);
        echo 'Installed '.$x['path'].PHP_EOL;
    }
} else { throw new RuntimeException('Choose prepare, migration, or activate'); }
