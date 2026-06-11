<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransportRoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            'التجارة',
            'العباسيين',
            'البرامكة',
            'الصناعة',
            'الزاهرة',
            'الميدان',
            'دويلعة',
            'صحنايا',
            'جرمانا',
            'مليحة',
            'غزلانية',
            'الحسينية',
            'الهيجانة',
            'مزة اتستراد',
            'كفر سوسة',
            'شارع بغداد',
            'باب الجابية',
            'نهر عيشة',
            'دير العصافير',
            'القزاز',
            'يلدا',
            'ببيلا',
            'بيت سحم',
            'قرحتا',
            'حتيتة التركمان',
            'السيدة زينب',
            'حموريا',
            'عين ترما',
        ];

        foreach ($routes as $index => $route) {
            DB::table('transport_routes')->insert([
                'name' => $route,
                'code' => 'R'.str_pad($index + 1, 3, '0', STR_PAD_LEFT), // R001, R002 ...
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
