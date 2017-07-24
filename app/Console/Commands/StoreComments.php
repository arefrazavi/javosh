<?php

namespace App\Console\Commands;

use App\Helpers\Common;
use App\Libraries\CommentLib;
use App\Models\Comment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        $fileDir = base_path("data/comments/Laptop-Ultrabook/");
        $filePaths = Common::getDirFiles($fileDir);

        $files = [
            [
                'filePaths' => $filePaths,
                'categoryId' => 6
            ]
        ];

        //temp
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $whereClause = "category_id = 6";
        Comment::deleteComments($whereClause);
        $statement = "ALTER TABLE comments AUTO_INCREMENT = 22660;";
        DB::unprepared($statement);
        //end temp

        CommentLib::storeComments($files);
    }
}
