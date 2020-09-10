<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class rconfigMigrate2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rconfig:migratetemplatefix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migratetemplatefix';

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

        if (DB::connection()->getDatabaseName()) {
            $this->info("Conncted sucessfully to database '" . DB::connection()->getDatabaseName() . "' on server '" . DB::connection('mysql2')->getConfig()['host'] . "'");
        } else {
            $this->error("Could not connect sucessfully to database '" . DB::connection()->getDatabaseName() . "' on server '" . DB::connection()->getConfig()['host'] . "'");
            $this->error("The script ends here!!");
            return;
        }
        if (DB::connection('mysql2')->getDatabaseName()) {
            $this->info("Conncted sucessfully to database '" . DB::connection('mysql2')->getDatabaseName() . "' on server '" . DB::connection('mysql2')->getConfig()['host'] . "'");
        } else {
            $this->error("Could not connect sucessfully to database '" . DB::connection('mysql2')->getDatabaseName() . "' on server '" . DB::connection('mysql2')->getConfig()['host'] . "'");
            $this->error("The script ends here!!");
            return;
        }


        $v5devices = DB::connection()->select('select * from devices');

        foreach ($v5devices as $v5device) {
                try {
                    $array['device_id'] = $v5device->id;
                    $array['template_id'] = $v5device->device_template;
                    DB::connection()->table('device_template')->insert($array);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
        }

        $this->info('TEmplate references per devices applied correctly.');
        return 0;
    }
}

