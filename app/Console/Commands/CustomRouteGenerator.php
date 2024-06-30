<?php

namespace App\Console\Commands;

use App\Console\Commands\Output\File;
use Tighten\Ziggy\CommandRouteGenerator;
use Tighten\Ziggy\Output\Types;
use Tighten\Ziggy\Ziggy;

class CustomRouteGenerator extends CommandRouteGenerator
{
//    /**
//     * The name and signature of the console command.
//     *
//     * @var string
//     */
//    protected $signature = 'route:generate
//                            {path? : Path to the generated JavaScript file. Default: `resources/js/ziggy.js`.}
//                            {--types : Generate a TypeScript declaration file.}
//                            {--types-only : Generate only a TypeScript declaration file.}
//                            {--url=}
//                            {--group=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a JSON file containing Ziggy’s routes.';

    public function handle(): void
    {
        $ziggy = new Ziggy($this->option('group'), $this->option('url') ? url($this->option('url')) : null);

        $path = $this->argument('path') ?? config('ziggy.output.path', 'resources/js/routes.json');

        if ($this->files->isDirectory(base_path($path))) {
            $path .= '/route';
        } else {
            $this->makeDirectory($path);
        }

        $name = preg_replace('/\.json$/', '', $path);

        $output = config('ziggy.output.file', File::class);

        $this->files->put(base_path("{$name}.json"), new $output($ziggy));

        $types = config('ziggy.output.types', Types::class);
        $this->files->put(base_path("{$name}.d.ts"), new $types($ziggy));

        $this->info("{$name}.json created successfully.");

        $this->info('Files generated!');
    }

    private function makeDirectory($path): void
    {
        if (!$this->files->isDirectory(dirname(base_path($path)))) {
            $this->files->makeDirectory(dirname(base_path($path)), 0755, true, true);
        }
    }
}
