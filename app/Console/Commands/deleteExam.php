<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Console\Command;

class deleteExam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Exam:expaired_exam';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command works when the time passes today date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $exams = Exam::where("date", Carbon::today())->get();
        foreach ($exams as $exam) {
            $exam->delete();
        }
    }
}