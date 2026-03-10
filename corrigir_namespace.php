<?php
$dir = __DIR__ . '/app/Livewire/';
foreach (glob($dir . '*.php') as $file) {
    $content = file_get_contents($file);
    $content = str_replace(
        'namespace App\Http\Livewire;',
        'namespace App\Livewire;',
        $content
    );
    file_put_contents($file, $content);
    echo "✅ Corrigido: " . basename($file) . "\n";
}
echo "Pronto!\n";