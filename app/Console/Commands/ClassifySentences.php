<?php

namespace App\Console\Commands;

use App\Libraries\SentenceLib;
use App\Models\Product;
use App\Models\Sentence;
use Illuminate\Console\Command;

class ClassifySentences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:classify-sentences';

    protected $sentenceLib;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * ClassifySentences constructor.
     * @param SentenceLib $sentenceLib
     */
    public function __construct(SentenceLib $sentenceLib)
    {
        parent::__construct();
        $this->sentenceLib = $sentenceLib;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $whereClause = 'id = 81294';
        $products = Product::fetchProducts('*', $whereClause);

        foreach ($products as $product) {
            $this->sentenceLib->classifySentences(-1.4, $product->id);
        }
    }
}
