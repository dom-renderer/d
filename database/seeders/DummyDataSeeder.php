<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            [
                'name' => 'Pumps',
                'slug' => 'pumps',
                'products' => [
                    [
                        'name' => 'Xylem - Lowara',
                        'sku' => 'xylem-lowara'
                    ],
                    [
                        'name' => 'Xylem - Goulds',
                        'sku' => 'xylem-goulds'
                    ],
                    [
                        'name' => 'Xylem - Flygt',
                        'sku' => 'xylem-flygt'
                    ],
                    [
                        'name' => 'Xylem - Godwin',
                        'sku' => 'xylem-godwin'
                    ]
                ]
            ],
            [
                'name' => 'WATER TREATMENT',
                'slug' => 'water-treatment',
                'products' => [
                    [
                        'name' => 'Kinetico',
                        'sku' => 'kinetico'
                    ],
                    [
                        'name' => 'Xylem',
                        'sku' => 'xylem'
                    ]
                ]
            ],
            [
                'name' => 'WATER STORAGE',
                'slug' => 'water-storage',
                'products' => [
                    [
                        'name' => 'Dewey Waters',
                        'sku' => 'dewey-waters'
                    ],
                    [
                        'name' => 'Braithwaite Engineers',
                        'sku' => 'braithwaite-engineers'
                    ],
                    [
                        'name' => 'Rotoplastics',
                        'sku' => 'rotoplastics'
                    ]
                ]
            ],
            [
                'name' => 'WATER HEATER',
                'slug' => 'water-heater',
                'products' => [
                    [
                        'name' => 'AO Smith',
                        'sku' => 'ao-smith'
                    ]
                ]
            ],           
            [
                'name' => 'GENERATORS',
                'slug' => 'generators',
                'products' => [
                    [
                        'name' => 'AJ Power',
                        'sku' => 'aj-power'
                    ]
                ]
            ],
            [
                'name' => 'AIR COMPRESSOR',
                'slug' => 'air-compressor',
                'products' => [
                    [
                        'name' => 'Gardner Denver',
                        'sku' => 'gardner-denver'
                    ]
                ]
            ],
            [
                'name' => 'BOILERS & STEAM',
                'slug' => 'boiler-and-steam',
                'products' => [
                    [
                        'name' => 'Fulton',
                        'sku' => 'fulton'
                    ]
                ]
            ]
        ] as $category) {

            $createdCategory = \App\Models\Category::updateOrCreate([
                'slug' => $category['slug']
            ], [
                'name' => $category['name'],
                'slug' => $category['slug'],
                'status' => 1
            ]);


            if ($createdCategory) {
                foreach ($category['products'] as $product) {
                    \App\Models\Product::updateOrCreate([
                        'sku' => $product['sku']
                    ], [
                        'category_id' => $createdCategory->id,
                        'sku' => $product['sku'],
                        'name' => $product['name'],
                        'status' => 1,
                        'amount' => rand(199, 2499)
                    ]);
                }
            }
        }

        foreach (['Engineering Services', 'Industrial & Manufacturing Sector Support', 'Hospitality Sector Engineering Services', 'Adaptability and Diversification'] as $expertise) {
            \App\Models\Expertise::updateOrCreate([
                'name' => $expertise,
            ],[
                'status' => 1
            ]);
        }

        foreach (['Mechanical Engineering Department', 'Industrial Engineering / Manufacturing Support Department', 'Hospitality Engineering / Facilities Engineering Department', 'Project Management or Engineering Consultancy Department', 'Foundry or Metalworks Department', 'Electrical/Controls Engineering Department', 'Accounting Department'] as $department) {
            \App\Models\Department::updateOrCreate([
                'name' => $department,
            ],[
                'status' => 1
            ]);
        }
    }
}
