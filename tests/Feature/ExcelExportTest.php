<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_excel_export_route_has_been_removed(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('attendances.export'));
    }
}
