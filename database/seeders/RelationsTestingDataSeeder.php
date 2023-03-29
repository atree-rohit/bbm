<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Taxa;
use App\Models\CountForm;
use App\Models\FormRow;
use App\Models\InatObservation;
use App\Models\IbpObservation;
use App\Models\IfbObservation;
 
class RelationsTestingDataSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        User::factory()
            ->count(50)
            ->create();
        Taxa::factory()
            ->count(50)
            ->create();
        CountForm::factory()
            ->count(50)
            ->create();
        FormRow::factory()
            ->count(50)
            ->create();
        InatObservation::factory()
            ->count(50)
            ->create();
        IbpObservation::factory()
            ->count(50)
            ->create();
        IfbObservation::factory()
            ->count(50)
            ->create();
    }
}