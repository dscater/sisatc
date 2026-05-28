<?php

namespace Database\Seeders;

use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\Provincia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SecundariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Departamento::insert([
            ["nombre" => "La Paz"],
            ["nombre" => "Cochabamba"],
            ["nombre" => "Santa Cruz"],
            ["nombre" => "Oruro"],
            ["nombre" => "Potosi"],
            ["nombre" => "Chuquisaca"],
            ["nombre" => "Tarija"],
            ["nombre" => "Pando"],
            ["nombre" => "Beni"],
        ]);

        Provincia::insert([
            ["nombre" => "Provincia 1"],
            ["nombre" => "Provincia 2"],
        ]);

        Ciudad::insert([
            ["nombre" => "Ciudad 1"],
            ["nombre" => "Ciudad 2"],
        ]);
    }
}
