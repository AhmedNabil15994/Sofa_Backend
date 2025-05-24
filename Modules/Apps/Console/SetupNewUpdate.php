<?php

namespace Modules\Apps\Console;

use Illuminate\Console\Command;
use Setting;

class SetupNewUpdate extends Command
{
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'setup:update';

    /**
     * The console command description.
     *
     * @var string
     */

     
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        Setting::set('supported_payments',[
            'cache','online'
        ]);
        $this->info('update setup finished');
    }
}
