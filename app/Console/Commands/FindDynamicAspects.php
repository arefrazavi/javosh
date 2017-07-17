<?php

namespace App\Console\Commands;

use App\Helpers\Common;
use App\Http\Controllers\AspectController;
use App\Libraries\AspectLib;
use App\Libraries\SentenceLib;
use App\Libraries\WordLib;
use App\Models\Category;
use App\Models\Word;
use Illuminate\Console\Command;

class FindDynamicAspects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aspect:find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
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
        $aspectDir = 'data/aspects/';
        $categories = Category::fetchCategories();
        $minSupport = 0.01;
        foreach ($categories as $category) {
            $categoryAspectDir = $aspectDir . $category->title . "/";
            $whereClause = "category_id = $category->id AND product_id = 134691";
            $textSentenceTexts = SentenceLib::getTextSentencesTexts($whereClause);
            if (empty($textSentenceTexts)) {
                continue;
            }
            $pointSentenceTexts = SentenceLib::getPointsSentencesTexts($whereClause);

            $minFreq = $minSupport * (sizeof($textSentenceTexts) + sizeof($pointSentenceTexts));
            $whereClause = "category_id = $category->id AND pos_tag IS NULL AND count >= $minFreq";
            $wordValues = WordLib::getWordValues($whereClause);

            $textFrequentItemSets = AspectLib::findFrequentItemSets($wordValues, $textSentenceTexts, $minSupport);
            $filePath = base_path($categoryAspectDir . "frequent_item_sets_text_php.csv");
            $this->writeFrequentItemSetsIntoFile($textFrequentItemSets, $filePath);

//            $pointFrequentItemSets = AspectLib::findFrequentItemSets($wordValues, $pointSentenceTexts);
//            $filePath = base_path($categoryAspectDir . "frequent_item_sets_point_php.csv");
//            $this->writeFrequentItemSetsIntoFile($pointFrequentItemSets, $filePath);
//
//            $aspects = AspectLib::findDynamicAspects($textFrequentItemSets, $textSentenceTexts);
//            $filePath = base_path($categoryAspectDir . "potential_dynamic_aspects.csv");
//            $writingMode = 'w';
//            Common::writeToCsv($aspects, $filePath, $writingMode);

            break;
        }
    }

    public function writeFrequentItemSetsIntoFile(&$frequentItemSets, &$filePath)
    {
        $frequentItems = [];
        foreach ($frequentItemSets as $SizedFrequentItemSets) {
            foreach ($SizedFrequentItemSets as $frequentItemSet) {
                $itemVal = implode(" ", $frequentItemSet['items']);
                $frequentItems[] = [
                    'item' => $itemVal,
                    'support' => $frequentItemSet['support'],
                    'frequency' => $frequentItemSet['frequency'],
                    'transactionIds' => serialize($frequentItemSet['transactionIds'])
                ];
            }
        }
        $writingMode = 'w';
        Common::writeToCsv($frequentItems, $filePath, $writingMode);
    }
}
