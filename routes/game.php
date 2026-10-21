<?php
use Illuminate\Support\Facades\Route;
use App\Services\Platform\ModuleRegistry;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        abort_unless(app(ModuleRegistry::class)->isEnabled('game'), 404);
        return redirect()->route('game.asset', ['file' => 'index.html']);
    })->name('game.home');

    Route::get('/{file}', function (string $file) {
        abort_unless(app(ModuleRegistry::class)->isEnabled('game'), 404);
        abort_if(str_contains($file, '..') || str_contains($file, '\\') || ! preg_match('~^[a-zA-Z0-9_./-]+$~D', $file), 404);
        abort_unless((bool) preg_match('~\.(?:html|css|js|png|jpg|jpeg|webp|svg)$~iD', $file), 404);
        $root = realpath(resource_path('tidong/game/v10'));
        $resolved = realpath(resource_path('tidong/game/v10/'.$file));
        abort_unless($root && $resolved && str_starts_with($resolved, $root.DIRECTORY_SEPARATOR) && is_file($resolved), 404);
        return response()->file($resolved, ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, max-age=0']);
    })->where('file', '[a-zA-Z0-9_./-]+')->name('game.asset');
});
