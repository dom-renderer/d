<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'roles' => ['admin'],
                'user' => [
                    'name' => 'Super Admin',
                    'email' => 'admin@gmail.com',
                    'dial_code' => 91,
                    'status' => 1,
                    'phone_number' => 9999999999,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['job-coordinator'],
                'user' => [
                    'name' => 'Adrian Downes',
                    'email' => 'service@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462317895,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['engineer'],
                'user' => [
                    'name' => 'Alan Alkins',
                    'email' => 'aalkins@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462332891,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['engineer'],
                'user' => [
                    'name' => "Jeremy O'Dowd",
                    'email' => 'jodowd@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462336165,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Yohan Lynch',
                    'email' => 'yohanlynch@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462337762,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Damien Husbands',
                    'email' => 'damienhusbands@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462337764,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Alvin Forde',
                    'email' => 'alvinforde@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462337767,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Winslow Moore',
                    'email' => 'winslowmoore@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462337770,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Jason Williams',
                    'email' => 'jasonwilliams@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462337775,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Dave Harewood',
                    'email' => 'daveharewood@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462317903,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['workshop-supervisor'],
                'user' => [
                    'name' => 'Duane Smith',
                    'email' => 'workshop@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462338791,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['engineer'],
                'user' => [
                    'name' => 'Luke Staffner',
                    'email' => 'lstaffner@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462343341,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['engineer'],
                'user' => [
                    'name' => 'Lewin Stoute',
                    'email' => 'lstoute@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462344105,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['engineer'],
                'user' => [
                    'name' => 'Jean-Claude Prospere',
                    'email' => 'jcprospere@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462345792,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Rommell Wilson',
                    'email' => 'rommellwilson@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462349139,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['messenger'],
                'user' => [
                    'name' => 'Noel Mullin',
                    'email' => 'admin@gmail.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462439577,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['billing-coordinator'],
                'user' => [
                    'name' => 'Julie Howell',
                    'email' => 'payables@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2460000001,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['billing-coordinator'],
                'user' => [
                    'name' => 'Samantha Abraham',
                    'email' => 'corporate@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2460000002,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['billing-coordinator'],
                'user' => [
                    'name' => 'Sarah Brathwaite',
                    'email' => 'receivables@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2460000003,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['inventory-procurement'],
                'user' => [
                    'name' => 'Cherie Moore',
                    'email' => 'procurement@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2460000004,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['admin'],
                'user' => [
                    'name' => 'Chris Sikkens',
                    'email' => 'csikkens@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462536562,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['admin'],
                'user' => [
                    'name' => 'Christine Richards',
                    'email' => 'crichards@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2462457138,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Riko Chase',
                    'email' => 'rikochase@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2460000005,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => ['technician'],
                'user' => [
                    'name' => 'Raheem Lynch',
                    'email' => 'raheemlynch@dmsengineers.com',
                    'dial_code' => 1,
                    'status' => 1,
                    'phone_number' => 2460000006,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ]
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['dial_code' => $user['user']['dial_code'], 'phone_number' => $user['user']['phone_number']], $user['user'])->syncRoles($user['roles']);
        }
    }
}
