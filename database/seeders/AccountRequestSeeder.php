<?php

namespace Database\Seeders;

use App\Models\AccountRequest;
use Illuminate\Database\Seeder;

class AccountRequestSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->isLocal()) {
            return;
        }

        AccountRequest::factory()->count(3)->create();
        AccountRequest::factory()->rejected()->create();
    }
}
