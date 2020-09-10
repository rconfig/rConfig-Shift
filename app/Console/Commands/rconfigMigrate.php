<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class rconfigMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rconfig:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

/** CHECK V3 PASSWORD ENCRYPTION */
        $v3PwEncryption = DB::connection('mysql2')->select('select passwordEncryption from settings'); // rconfigv3
        $v3PwEncryptionSet = $v3PwEncryption[0]->passwordEncryption;


/** TEMPLATES */
        // Bring over templates
        $v3templates = DB::connection('mysql2')->select('select * from templates where status = 1'); // rconfigv3
        DB::connection()->select('truncate templates');

        foreach ($v3templates as $v3template) {
            $temp_arry['id'] = $v3template->id;
            $temp_arry['fileName'] = str_replace("/home/rconfig/templates/", "/app/rconfig/templates/", $v3template->fileName);
            $temp_arry['templateName'] = $v3template->name;
            $temp_arry['description'] = $v3template->desc;
            $temp_arry['created_at'] = \Carbon\Carbon::now();
            dump($temp_arry);
            // DB::connection()->insert('insert into templates (id, fileName, templateName, description) values (?, ?, ?, ?)', $temp_arry);
            DB::connection()->table('templates')->insert($temp_arry);
            $temp_arry = [];
        }
        // $v5templates = DB::connection()->select('select * from templates'); // rconfigv3
        // dd($v5templates);
        $this->info("Templates imported to v5 Database...");
/** TEMPLATES */

/** CATEGORIES */
        // Bring over categories
        $v3categories = DB::connection('mysql2')->select('select * from categories where status = 1'); // rconfigv3
        DB::connection()->select('truncate categories');

        foreach ($v3categories as $v3category) {
            $temp_arry['id'] = $v3category->id;
            $temp_arry['categoryName'] = $v3category->categoryName;
            $temp_arry['categoryDescription'] = 'Migrated V3 Category';
            $temp_arry['created_at'] = \Carbon\Carbon::now();
            dump($temp_arry);
            // DB::connection()->insert('insert into templates (id, fileName, templateName, description) values (?, ?, ?, ?)', $temp_arry);
            DB::connection()->table('categories')->insert($temp_arry);
            $temp_arry = [];
        }
        $this->info("Categories imported to v5 Database...");
/** CATEGORIES */

/** COMMANDS */
        // Bring over COMMANDS
        $v3commands = DB::connection('mysql2')->select('select * from configcommands where status = 1'); // rconfigv3
        DB::connection()->select('truncate commands');

        foreach ($v3commands as $v3command) {
            $temp_arry['id'] = $v3command->id;
            $temp_arry['command'] = $v3command->command;
            $temp_arry['description'] = 'Migrated V3 Command';
            $temp_arry['created_at'] = \Carbon\Carbon::now();
            dump($temp_arry);
            // DB::connection()->insert('insert into templates (id, fileName, templateName, description) values (?, ?, ?, ?)', $temp_arry);
            DB::connection()->table('commands')->insert($temp_arry);
            $temp_arry = [];
        }
        $this->info("Commands imported to v5 Database...");
/** COMMANDS */

/** COMMANDS_CATEGORIES */
        // Bring over COMMANDS_CATEGORIES
        $v3cmdCatTbls = DB::connection('mysql2')->select('select * from cmdCatTbl'); // rconfigv3
        DB::connection()->select('truncate category_command');

        foreach ($v3cmdCatTbls as $v3cmdCatTbl) {
            $temp_arry['category_id'] = $v3cmdCatTbl->nodeCatId;
            $temp_arry['command_id'] = $v3cmdCatTbl->configCmdId;
            dump($temp_arry);
            // DB::connection()->insert('insert into templates (id, fileName, templateName, description) values (?, ?, ?, ?)', $temp_arry);
            DB::connection()->table('category_command')->insert($temp_arry);
            $temp_arry = [];
        }
        $this->info("Cat_Commands relationship table imported to v5 Database...");
/** COMMANDS_CATEGORIES */

/** VENDORS */
        // Bring over vendors
        $v3vendors = DB::connection('mysql2')->select('select * from vendors where status = 1'); // rconfigv3
        DB::connection()->select('truncate vendors');

        foreach ($v3vendors as $v3vendor) {
            $temp_arry['id'] = $v3vendor->id;
            $temp_arry['vendorName'] = $v3vendor->vendorName;
            $temp_arry['created_at'] = \Carbon\Carbon::now();
            dump($temp_arry);
            // DB::connection()->insert('insert into templates (id, fileName, templateName, description) values (?, ?, ?, ?)', $temp_arry);
            DB::connection()->table('vendors')->insert($temp_arry);
            $temp_arry = [];
        }
        $this->info("Vendors imported to v5 Database...");
/** VENDORS */

/** USERS */
        // Bring over users
        $v3users = DB::connection('mysql2')->select('select * from users where status = 1'); // rconfigv3
        $new_password = Str::random();

        foreach ($v3users as $v3user) {
            if($v3user->email === 'admin@domain.com'){
                continue;
            }
            $temp_arry['name'] = $v3user->username;
            $temp_arry['email'] = $v3user->email;
            $temp_arry['password'] = \Crypt::encrypt($new_password);
            $temp_arry['role'] = 'Admin';
            $temp_arry['ldap_import'] = 0;
            $temp_arry['created_at'] = \Carbon\Carbon::now();
            dump($temp_arry);
            // DB::connection()->insert('insert into templates (id, fileName, templateName, description) values (?, ?, ?, ?)', $temp_arry);
            try {
                DB::connection()->table('users')->insert($temp_arry);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $temp_arry = [];
        }
        $this->info("Users imported to v5 Database...");
        $this->error("Users passwords are not imported. All users passwords were set to " . $new_password);
        $this->error("Please change all users password immediatley! ");
/** USERS */

/** DEFAULT TAGS */
        $tags_arry['id'] = 1000;
        $tags_arry['tagname'] = "MigratedFromV3";
        $tags_arry['tagDescription'] = "This Tag is for devices migrated from V3";
        $tags_arry['created_at'] = \Carbon\Carbon::now();
        try {
            DB::connection()->table('tags')->insert($tags_arry);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

/** DEVICES */
        // Bring over users
        $v3nodes = DB::connection('mysql2')->select('select * from nodes where status = 1'); // rconfigv3
        // dd($v3nodes);
        $new_device_password = Str::random();

        foreach ($v3nodes as $v3node) {

            $temp_arry['device_name'] = $v3node->deviceName;
            $temp_arry['device_ip'] = $v3node->deviceIpAddr;
            $temp_arry['device_default_creds_on'] = $v3node->defaultCreds;
            $temp_arry['device_username'] = $v3node->deviceUsername;
            $temp_arry['device_password'] = $v3PwEncryptionSet === 0 ? \Crypt::encrypt($v3node->devicePassword) : \Crypt::encrypt($new_device_password);
            $temp_arry['device_enable_password'] = $v3PwEncryptionSet === 0 ? \Crypt::encrypt($v3node->deviceEnablePassword) : \Crypt::encrypt($new_device_password);
            $temp_arry['device_main_prompt'] = $v3node->devicePrompt;
            $temp_arry['device_enable_prompt'] = $v3node->deviceEnablePrompt;
            $temp_arry['device_category_id'] = $v3node->nodeCatId;
            $temp_arry['device_template'] = $v3node->templateId;
            $temp_arry['device_model'] = $v3node->model;
            $temp_arry['device_version'] = $v3node->nodeVersion;
            $temp_arry['device_added_by'] = $v3node->nodeAddedBy;
            $temp_arry['status'] = null;
            $temp_arry1['vendorId'] = $v3node->vendorId;
            $temp_arry['created_at'] = \Carbon\Carbon::now();

            try {
                $device_id = DB::connection()->table('devices')->insert($temp_arry);
                $device_id = DB::getPdo()->lastInsertId();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            try {
                $cat_dev_arry['category_id'] = $temp_arry['device_category_id'];
                $cat_dev_arry['device_id'] = $device_id;
                DB::connection()->table('category_device')->insert($cat_dev_arry);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            try {
                $dev_ven_arry['device_id'] = $device_id;
                $dev_ven_arry['vendor_id'] = $temp_arry1['vendorId'];
                DB::connection()->table('device_vendor')->insert($dev_ven_arry);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            try {
                $dev_tag_arry['device_id'] = $device_id;
                $dev_tag_arry['tag_id'] = 1000;
                DB::connection()->table('device_tag')->insert($dev_tag_arry);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            $temp_arry = [];
        }

        $v3PwEncryptionSet === 1 ? $this->error('Password Encryption was enabled in V3!') : '';
        $v3PwEncryptionSet === 1 ? $this->error("Devices passwords are not imported, and new random passwords were generated. All devices passwords were set to " . $new_device_password) : '';;
        $v3PwEncryptionSet === 1 ? $this->error("Please change all devices password immediatley! ") : '';;
        $this->info("Devices imported to v5 Database...");
/** DEVICES */

        // $devices = DB::connection('mysql2')->select('select * from nodes'); // rconfigv3
        // dd($devices);

        // Bring over commands & cmdcatTbl
        // Bring over vendors
        // Bring over nodes to devices?

        $this->info(' ');
        $this->info('--- Post Migration Manual tasks ---');
        $this->info('- Copy Templates to new server');
        $this->info('- Manually recreate Tasks');
        $this->info('- Manually recreate Policies');
        return 0;
    }
}

