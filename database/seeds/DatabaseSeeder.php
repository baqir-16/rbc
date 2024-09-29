3<?php

use App\User;
use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ask for db migration refresh, default is no
        if ($this->command->confirm('Do you wish to refresh migration before seeding, it will clear all old data ?')) {
            // disable fk constrain check
            // \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Call the php artisan migrate:refresh
            $this->command->call('migrate:refresh');
            $this->command->warn("Data cleared, starting from blank database.");

            // enable back fk constrain check
            // \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Seed the default permissions
        $permissions = Permission::defaultPermissions();

        foreach ($permissions as $perms) {
            Permission::firstOrCreate(['name' => $perms]);
        }

        $this->command->info('Default Permissions added.');

        // Confirm roles needed
        if ($this->command->confirm('Create Roles for user, default is admin? [y|N]', true)) {

            // Ask for roles from input
            $input_roles = $this->command->ask('Enter admin only.', 'Admin');

            // Explode roles
            $roles_array = explode(',', $input_roles);

            // add roles
            foreach($roles_array as $role) {
                $role = Role::firstOrCreate(['name' => trim($role)]);

                if( $role->name == 'Admin' OR $role->name == 'admin' ) {
                    // assign all permissions
                    $role->syncPermissions(Permission::all());
                    $this->command->info('Admin granted all the permissions');
                } else {
                    // for others by default only read access
                    $role->syncPermissions(Permission::where('name', 'LIKE', 'view_%')->get());
                }

                // create one user for each role
                $this->createUser($role);
            }
            $this->command->info('Roles ' . $input_roles . ' added successfully');
        } else {
            Role::firstOrCreate(['name' => 'User']);
            $this->command->info('Added only default user role.');
        }
        // now lets seed some posts for demo
        factory(\App\Post::class, 30)->create();
        $this->command->info('Some Posts data seeded.');
        $this->command->warn('All done :)');

        //opco table
        if(DB::table('opco')->get()->count() == 0){
            DB::table('opco')->insert([
//                [
//                    'opco' => 'HQ',
//                    'status' => 2,
//                ],
//                [
//                    'opco' => 'CoE',
//                    'status' => 2,
//                ],
                [
                    'opco' => 'Axiata Digital Services (Malaysia)',
                    'status' => 1,
                ],
//                [
//                    'opco' => 'Axiata (Malaysia)',
//                    'status' => 1,
//                ],
//                [
//                    'opco' => 'E.Co (Malaysia)',
//                    'status' => 1,
//                ],
//                [
//                    'opco' => 'XL (Indonesia)',
//                    'status' => 1,
//                ],
//                [
//                    'opco' => 'NCELL (Nepal)',
//                    'status' => 1,
//                ],
//                [
//                    'opco' => 'SMART (Cambodia)',
//                    'status' => 1,
//                ],
//                [
//                    'opco' => 'DIALOG (Sri Lanka)',
//                    'status' => 1,
//                ],
//                [
//                    'opco' => 'ROBI (Bangladesh)',
//                    'status' => 1,
//                ],
//                [
//                    'opco' => 'Celcom (Malaysia)',
//                    'status' => 1,
//                ]
            ]);
        }

        //modules table
        if(DB::table('modules')->get()->count() == 0){
            DB::table('modules')->insert([

                [
                    'name' => 'Nessus',
                ],
                [
                    'name' => 'Acunetix',
                ],
                [
                    'name' => 'Nexpose',
                ],
                [
                    'name' => 'Appscan',
                ],
                [
                    'name' => 'Burpsuite',
                ],
            ]);
        }

        if(DB::table('vul_categories')->get()->count() == 0){
            DB::table('vul_categories')->insert([
                 [
                    'name' => 'Broken Access Control',
                    'user_id' => 1,
                ],
                [
                    'name' => 'Broken Authentication',
                    'user_id' => 1,
                ],
                [
                    'name' => 'Injection',
                    'user_id' => 1,
                ],
                [
                    'name' => 'Patch Management',
                    'user_id' => 1,
                ],
                [
                    'name' => 'Security Misconfiguration',
                    'user_id' => 1,
                ],

                [
                    'name' => 'Sensitive Data Exposure',
                    'user_id' => 1,
                ],
                [
                    'name' => 'System Development and Maintenance',
                    'user_id' => 1,
                ],
                [
                    'name' => 'Unprotected Services',
                    'user_id' => 1,
                ],
                [
                    'name' => 'Weak Cryptography',
                    'user_id' => 1,
                ],
            ]);
        }

        if(DB::table('pdfreports')->get()->count() == 0){
            DB::table('pdfreports')->insert([
                [
                    'name' => 'PDF Report',
                    'avatar' => 'avatar.jpg',
                    'c_hours' => 48,
                    'c_responsible' => 'Tester Name',
                    'c_escalation' => 'HoD Name',
                    'h_hours' => 120,
                    'h_responsible' => 'Tester Name',
                    'h_escalation' => 'HoD Name',
                    'm_hours' => 720,
                    'm_responsible' => 'Tester Name',
                    'm_escalation' => 'HoD Name',
                    'l_hours' => 820,
                    'l_responsible' => 'Tester Name',
                    'l_escalation' => 'HoD Name',
                    'disclaimer' => '',
                    'm_title' => '',
                    'm_description' => '',
                    'user_id' => 1,
                ],
            ]);
        }

//        //users table - add user 'Victor'
//        DB::table('users')->insert([
//            [
//                'name' => 'Fitim',
//                'username' => 'Fitim',
//                'email' => 'fitafejzullahu24@gmail.com',
//                'password' => bcrypt('test123'),
//                'opco_id' => '1',
//                'created_at' =>'2018-02-23 03:31:55'
//            ],
//            [
//                'name' => 'Victor',
//                'username' => 'victor',
//                'email' => 'victor@abc.com',
//                'password' => bcrypt('qqqqqq'),
//                'opco_id' => '2',
//                'created_at' =>'2018-02-23 03:31:55'
//            ],
//            [
//                'name' => 'Amir',
//                'username' => 'amir',
//                'email' => 'amir@test.com',
//                'password' => bcrypt('123'),
//                'opco_id' => '3',
//                'created_at' =>'2018-02-23 03:31:55'
//            ],
//        ]);

        Artisan::call('schedule:run');
        Artisan::call('passport:install');
    }

    /**
     * Create a user with given role
     *
     * @param $role
     */
    private function createUser($role)
    {
        $user = factory(User::class)->create();
        $user->assignRole($role->name);

        if( $role->name == 'Admin' OR $role->name == 'admin' ) {
            $this->command->info('Here is your admin details to login:');
            $this->command->warn($user->username);
            $this->command->warn($user->email);
            $this->command->warn('Password is "Kpmg1234"');
        }
    }
}
