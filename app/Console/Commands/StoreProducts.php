<?php

namespace App\Console\Commands;

use App\Helpers\Common;
use App\Libraries\CommentLib;
use App\Libraries\ProductLib;
use App\Libraries\ResultLib;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Console\Command;

class StoreProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:store';

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

        $fileDir = base_path("data/products/Laptop-Ultrabook/");
        $laptopFilePaths = Common::getDirFiles($fileDir);


        $fileDir = base_path("data/products/Mobile-Phone/");
        $mobileFilePaths = Common::getDirFiles($fileDir);


        $files = [
            [
                'filePaths' => $mobileFilePaths,
                'categoryId' => 4
            ],
            [
                'filePaths' => $laptopFilePaths,
                'categoryId' => 6
            ]
        ];

        ProductLib::storeProducts($files);
        //ProductLib::SaveProductsIntoFile();
        //CommentLib::storeComments($fileDir);
    }
}
