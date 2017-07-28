<?php

namespace App\Console\Commands;

use App\Libraries\WordLib;
use App\Models\Word;
use Illuminate\Console\Command;

class UpdatePosTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:update-pos-tag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected $wordLib;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = [
            [
                'filePath' => base_path('data/words/verbs.csv'),
                'posTag' => 'V',
                'header' => 0
            ],

            [
                'filePath' => base_path('data/words/adjectives.csv'),
                'posTag' => 'ADJ',
                'header' => 1
            ],
        ];
        WordLib::updatePosTag($files);



    }
}
