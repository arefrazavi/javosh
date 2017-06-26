<?php

namespace App\Console\Commands;

use App\Helpers\Common;
use App\Libraries\CommentLib;
use Illuminate\Console\Command;

class StoreComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {

        $fileDir = base_path("data/comments/Laptop/");
        $filePaths = Common::getDirFiles($fileDir);

        $files = [
            [
                'filePaths' => $filePaths,
                'categoryId' => 6
            ]
        ];

        CommentLib::storeComments($files);
    }
}
