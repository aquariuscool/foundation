<?php namespace Orchestra\Foundation\Console\Commands;

use Illuminate\Console\Command;
use Orchestra\Contracts\Memory\Provider;
use Orchestra\Contracts\Foundation\Foundation;
use Symfony\Component\Console\Input\InputOption;

class AssembleCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'orchestra:assemble';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh application setup (during composer install/update)';

    /**
     * The application foundation implementation.
     *
     * @var \Orchestra\Contracts\Foundation\Foundation
     */
    protected $foundation;

    /**
     * The memory provider implementation.
     *
     * @var \Orchestra\Contracts\Memory\Provider
     */
    protected $memory;

    /**
     * Construct a new command.
     *
     * @param  \Orchestra\Contracts\Foundation\Foundation  $foundation
     * @param  \Orchestra\Contracts\Memory\Provider  $memory
     */
    public function __construct(Foundation $foundation, Provider $memory)
    {
        $this->foundation = $foundation;
        $this->memory     = $memory;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->setupApplication();

        $this->refreshApplication();

        $this->optimizeApplication();
    }

    /**
     * Refresh application for Orchestra Platform.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        if (! $this->foundation->installed()) {
            return;
        }

        $extensions = $this->memory->get('extensions.active', []);

        foreach ($extensions as $extension => $config) {
            $this->call('extension:refresh', ['name' => $extension, '--force' => true]);
        }
    }

    /**
     * Setup application for Orchestra Platform.
     *
     * @return void
     */
    protected function setupApplication()
    {
        $this->call('publish:assets', ['package' => 'orchestra/foundation']);
    }

    /**
     * Optimize application for Orchestra Platform.
     *
     * @return void
     */
    protected function optimizeApplication()
    {
        if ($this->laravel->environment('production') || $this->option('cache')) {
            $this->call('config:cache');
            $this->call('route:cache');
        }

        $this->call('orchestra:optimize');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['cache', null, InputOption::VALUE_NONE, 'Force to run route and config caching.'],
        ];
    }
}