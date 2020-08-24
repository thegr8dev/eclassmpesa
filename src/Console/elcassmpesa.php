<?php

namespace Thegr8dev\Eclassmpesa\Console;

use Illuminate\Console\Command;

class elcassmpesa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eclassmpesa:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will publish all require config files by just hitting this command !';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Installing BlogPackage...');

        $this->info('Publishing configuration...');

        // $this->call('vendor:publish', [
        //     '--provider' => "JohnDoe\BlogPackage\BlogPackageServiceProvider",
        //     '--force' => "true"
        // ]);

        $this->info('Installed BlogPackage');
    }
}
