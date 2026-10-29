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
    protected $description = 'Generate & sync parent/student user accounts (@studentntokupang) for all students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $students = Student::orderBy('nama', 'asc')->get();
        $syncedCount = 0;

        foreach ($students as $student) {
            $parentUser = User::where('student_id', $student->id)->where('role', 'orang_tua')->first();
            $parentEmail = StudentController::generateParentEmail($student->nama, $student->id);

            if (!$parentUser) {
                User::create([
                    'name' => 'Orang Tua (' . $student->nama . ')',
                    'email' => $parentEmail,
                    'password' => Hash::make('password'),
                    'plain_password' => 'password',
                    'role' => 'orang_tua',
                    'student_id' => $student->id,
                    'email_verified_at' => now(),
                ]);

                $this->info("Created parent account for student {$student->nama}: {$parentEmail}");
                $syncedCount++;
            } elseif ($parentUser->email !== $parentEmail) {
                $parentUser->update([
                    'email' => $parentEmail,
                ]);
                $this->info("Updated parent email for {$student->nama}: {$parentEmail}");
                $syncedCount++;
            }
        }

        $this->info("Finished syncing parent accounts. Total {$syncedCount} accounts created/updated.");

        return Command::SUCCESS;
    }
}
