<?php

namespace Tests\Feature;

use Tests\TestCase;

class NamedRouteReferenceIntegrityTest extends TestCase
{
    public function test_all_static_named_route_references_exist(): void
    {
        $routeNames = collect(app('router')->getRoutes()->getRoutesByName())
            ->keys()
            ->all();

        $routeNameMap = array_fill_keys($routeNames, true);
        $directories = [
            base_path('resources/views'),
            base_path('app/Http/Controllers'),
            base_path('app/Livewire'),
            base_path('routes'),
        ];

        $missing = [];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    continue;
                }

                $path = $file->getPathname();
                if (! str_ends_with($path, '.php') && ! str_ends_with($path, '.blade.php')) {
                    continue;
                }

                $content = file_get_contents($path);
                if ($content === false) {
                    continue;
                }

                if (! preg_match_all('/route\(\s*[\'"]([A-Za-z0-9_.-]+)[\'"]/', $content, $matches)) {
                    continue;
                }

                foreach ($matches[1] as $name) {
                    if (! app()->environment('local') && str_starts_with($name, 'local-previews.')) {
                        continue;
                    }

                    if (! isset($routeNameMap[$name])) {
                        $missing[$name][] = str_replace(base_path().'/', '', $path);
                    }
                }
            }
        }

        ksort($missing);

        $message = '';
        foreach ($missing as $name => $paths) {
            $message .= $name.' referenced by '.implode(', ', array_unique($paths)).PHP_EOL;
        }

        $this->assertSame([], $missing, $message);
    }
}
