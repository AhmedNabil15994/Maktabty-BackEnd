<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Service\Entities\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $count = Service::count();
            if ($count == 0) {
                $items = [
                    [
                        'title' => [
                            'ar' => 'بطاريات',
                            'en' => 'Batteries',
                        ],
                        'sort' => 14,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/batteries.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'اشتراك',
                            'en' => 'Participation',
                        ],
                        'sort' => 13,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/participation.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'اطارات',
                            'en' => 'Tires',
                        ],
                        'sort' => 12,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/tires.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'بنجر',
                            'en' => 'Beet',
                        ],
                        'sort' => 11,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/beet.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'غاز المكيف',
                            'en' => 'Air conditioner gas',
                        ],
                        'sort' => 10,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/air_conditioner_gas.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'تعبئة وقود',
                            'en' => 'Fuel filling',
                        ],
                        'sort' => 9,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/fuel_filling.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'صيانة يد الجير',
                            'en' => 'Hand lime maintenance',
                        ],
                        'sort' => 8,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/hand_lime_maintenance.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'مخرطة رنقات',
                            'en' => 'Wheel lathe',
                        ],
                        'sort' => 7,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/wheel_lathe.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'برمجة مفاتيح',
                            'en' => 'Programming keys',
                        ],
                        'sort' => 6,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/programming_keys.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'فحص كمبيوتر',
                            'en' => 'Computer check',
                        ],
                        'sort' => 5,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/computer_check.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'زيت و فلتر',
                            'en' => 'Oil and filter',
                        ],
                        'sort' => 4,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/oil_and_filter.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'سطحه',
                            'en' => 'Car winch',
                        ],
                        'sort' => 3,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/car_winch.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'تجفيت جير',
                            'en' => 'Drying lime',
                        ],
                        'sort' => 2,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/drying_lime.png',
                    ],
                    [
                        'title' => [
                            'ar' => 'تجفيت ماكينة',
                            'en' => 'Drying machine',
                        ],
                        'sort' => 1,
                        'status' => 1,
                        'image' => 'storage/photos/shares/test_services/drying_machine.png',
                    ],
                ];

                foreach ($items as $key => $item) {
                    Service::create($item);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
