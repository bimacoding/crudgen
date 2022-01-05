<?php

namespace Erendi\Crudgenerator\Commands;

use Illuminate\Console\Command;

class CrudInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inisialisasikan route';

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
     * @return int
     */
    public function handle()
    {
        $this->call('migrate');
        $this->call('optimize');
    }
}
