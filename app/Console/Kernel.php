<?php

namespace App\Console;

use App\Console\Commands\ClassifySentences;
use App\Console\Commands\ComputeSentenceAspectFrequency;
use App\Console\Commands\ComputeSentencesEntropy;
use App\Console\Commands\ComputeWordsEntropy;
use App\Console\Commands\EvaluateResults;
use App\Console\Commands\FindAttributes;
use App\Console\Commands\FindDynamicAspects;
use App\Console\Commands\FindVerbs;
use App\Console\Commands\GenerateWord2Vec;
use App\Console\Commands\StoreClosestAspect;
use App\Console\Commands\StoreComments;
use App\Console\Commands\StoreAspects;
use App\Console\Commands\StoreProducts;
use App\Console\Commands\StoreResults;
use App\Console\Commands\StoreSentences;
use App\Console\Commands\StoreSummaries;
use App\Console\Commands\StoreWords;
use App\Console\Commands\UpdatePosTag;
use App\Console\Commands\WriteSentencesIntoFile;
use App\Libraries\CommentLib;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        StoreWords::class,
        ComputeSentencesEntropy::class,
        ComputeWordsEntropy::class,
        ClassifySentences::class,
        FindAttributes::class,
        FindVerbs::class,
        UpdatePosTag::class,
        StoreAspects::class,
        StoreSentences::class,
        FindDynamicAspects::class,
        StoreSummaries::class,
        EvaluateResults::class,
        StoreResults::class,
        StoreProducts::class,
        StoreComments::class,
        GenerateWord2Vec::class,
        ComputeSentenceAspectFrequency::class,
        StoreClosestAspect::class,
        WriteSentencesIntoFile::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
