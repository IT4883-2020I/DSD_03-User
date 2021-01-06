<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $limit = 1000;

        for ($i = 0; $i < $limit; $i++) {
            $email = $faker->unique()->email;
            $username = explode("@", $email)[0];
            \DB::table('users')->insert([
                'full_name' => $faker->name,
                'email' => $faker->unique()->email,
                'phone' => $faker->randomelement(['09' . rand(10000000, 100000000), '03' . rand(10000000, 100000000)]),
                'address' => $faker->address,
                'username' => $username,
                'password' => app('hash')->make($username),
                'birthday' => $faker->dateTime()->format('Y-m-d'),
                'avatar' => '/media/users/blank.png',
                'status' => $faker->randomelement(['ACTIVE', 'PENDING', 'INACTIVE']),
                'role' => $faker->randomelement(['ADMIN', 'MANAGER', 'SUPERVISOR', 'DRONE_STAFF', 'INCIDENT_STAFF']),
                'type' => $faker->randomelement(['CHAY_RUNG', 'DE_DIEU', 'LUOI_DIEN', 'CAY_TRONG']),
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ]);
        }
    }
}
