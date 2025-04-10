<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $salt = random_bytes(16);
        $iterations = 4096;
        $saltedPassword = hash_pbkdf2('sha1', 'ajjou123', $salt, $iterations, 20, true);

        $clientKey = hash_hmac('sha1', 'Client Key', $saltedPassword, true);
        $storedKey = sha1($clientKey, true);
        $serverKey = hash_hmac('sha1', 'Server Key', $saltedPassword, true);

        $saltEncoded = base64_encode($salt);
        $storedKeyEncoded = base64_encode($storedKey);
        $serverKeyEncoded = base64_encode($serverKey);



        $mitarbeiter = DB::table('users')->insert([
            'name' => 'mitarbeiter',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('ajjou123'),
            'phone' => fake()->phoneNumber(),
            'address' => 'mitarbeiter@gmail.com',
            'profile_picture' => fake()->imageUrl(),
            'status' => 'active',
        ]);

        $username = 'mitarbeiter';
        $openfireUserId = DB::table('ofUser')->insertGetId([
            'username' => $username,
            'plainPassword' => null, 
            'name' => $username,
            'email' => 'mitarbeiter@gmail.com',
            'creationDate' => Carbon::now()->timestamp * 1000,
            'modificationDate' => Carbon::now()->timestamp * 1000,
            'storedKey' => $storedKeyEncoded,
            'serverKey' => $serverKeyEncoded,
            'salt' => $saltEncoded,
            'iterations' => $iterations
        ]);
    
        DB::table('xmpp_user_mappings')->insert([
           'xmpp_username' => $username,
            'xmpp_password' => 'ajjou123',
            'user_type' => 'mitarbeiter',
            'user_id' => $mitarbeiter,
        ]);
    
        DB::table('ofGroupUser')->insert([
            'groupName' => 'mitarbeiter', 
            'username' => $username,
            'administrator' => 0
        ]);












        $admin = DB::table('admins')->insert([
            'name' => 'admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('admin'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_picture' => fake()->imageUrl(),
            'status' => 'active',
        ]);


        $username = 'admin1';
        $openfireUserId = DB::table('ofUser')->insertGetId([
            'username' => $username,
            'plainPassword' => null, 
            'name' => $username,
            'email' => 'admin@gmail.com',
            'creationDate' => Carbon::now()->timestamp * 1000,
            'modificationDate' => Carbon::now()->timestamp * 1000,
            'storedKey' => $storedKeyEncoded,
            'serverKey' => $serverKeyEncoded,
            'salt' => $saltEncoded,
            'iterations' => $iterations
        ]);
    
        DB::table('xmpp_user_mappings')->insert([
           'xmpp_username' => $username,
            'xmpp_password' => 'ajjou123',
            'user_type' => 'admin',
            'user_id' => $admin,
        ]);
    
        DB::table('ofGroupUser')->insert([
            'groupName' => 'admin', 
            'username' => $username,
            'administrator' => 0
        ]);
















        $azubi = DB::table('Azubis')->insert([
            'name' => 'azubi',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('azubi'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'profile_picture' => fake()->imageUrl(),
            'status' => 'active',
        ]);

        $username = 'azubi';

        $openfireUserId = DB::table('ofUser')->insertGetId([
            'username' => $username,
            'plainPassword' => null, 
            'name' => $username,
            'email' => 'azubi@gmail.com',
            'creationDate' => Carbon::now()->timestamp * 1000,
            'modificationDate' => Carbon::now()->timestamp * 1000,
            'storedKey' => $storedKeyEncoded,
            'serverKey' => $serverKeyEncoded,
            'salt' => $saltEncoded,
            'iterations' => $iterations
        ]);
    
        DB::table('xmpp_user_mappings')->insert([
           'xmpp_username' => $username,
            'xmpp_password' => 'ajjou123',
            'user_type' => 'azubi',
            'user_id' => $azubi,
        ]);
    
        DB::table('ofGroupUser')->insert([
            'groupName' => 'azubi', 
            'username' => $username,
            'administrator' => 0
        ]);





    }
}
