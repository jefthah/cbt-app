<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("categories")->insert([
            ["name" => "Programming", "slug" => "programming", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
            ["name" => "Business", "slug" => "business", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
            ["name" => "Lifestyle", "slug" => "lifestyle", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
        ]);
    }
}
