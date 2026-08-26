<?php

namespace App\Console\Commands;

use App\Http\Controllers\StudentController;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateParentAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parent:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing parent user accounts (@student.sch.id) for all existing students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $students = Student::orderBy('nama', 'asc')->get();
        $createdCount = 0;

        foreach ($students as $student) {
            $parentUser = User::where('student_id', $student->id)->where('role', 'orang_tua')->first();

            if (!$parentUser) {
                $parentEmail = StudentController::generateParentEmail($student->nama, $student->id);

                User::create([
                    'name' => 'Orang Tua (' . $student->nama . ')',
                    'email' => $parentEmail,
                    'password' => Hash::make('password'),
                    'role' => 'orang_tua',
                    'student_id' => $student->id,
                    'email_verified_at' => now(),
                ]);

                $this->info("Created parent account for student {$student->nama}: {$parentEmail}");
                $createdCount++;
            }
        }

        if ($createdCount === 0) {
            $this->info("All {$students->count()} students already have active parent accounts (@student.sch.id).");
        } else {
            $this->info("Successfully created {$createdCount} parent accounts.");
        }

        return Command::SUCCESS;
    }
}
