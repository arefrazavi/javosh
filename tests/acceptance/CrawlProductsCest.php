<?php

use App\Models\Product;

class CrawlProductsCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function CrawlProductsTest(AcceptanceTester $I)
    {
        $filePath = base_path('data/products/uncrawled-products.csv');
        $products = \App\Helpers\Common::readFromCsv($filePath);
        unset($products[0]);
        $requestsCount = 0;

        foreach ($products as $product) {
            $productData = [];
            $productData['id'] = $product[0];
            $categoryTitle = $product[1];
            $productsPath = '/var/www/arcrawler/data/products/' . $categoryTitle . '/';
            //$productsPath = 'E:\\wamp\\www\\arcrawler\\data\\products\\' . $categoryTitle . '\\'; //Windows
            $productFields = [
                'id',
                'title',
                'price',
                'userRate',
                'recommendationCount',
                'description'
            ];

            /*** get the list of crawled products ***/
            if (!file_exists($productsPath)) {
                mkdir($productsPath);
            }

            print_r("\n Product: " . $productData['id']. "\n");
            /*** Go to product page ***/
            $productUrl = "/Product/DKP-" . $productData['id'] . "/#!/displaycomment-0/page-1/sort-date/tab-comments/";
            $I->amOnPage($productUrl);
            $I->seeInTitle('دیجی کالا');
            $requestsCount++;
            print_r("\n Page Request Count: " . $requestsCount . "\n");
            $I->waitPageLoad();

            /***Retrieve and store product info***/
            $productPriceXpath = "//div[@class='info-header']/h1";
            $productData['title'] = $I->grabTextFrom($productPriceXpath);

            $productPriceXpath = "//span[@id='frmLblPayablePriceAmount']";
            if ($I->elementExist($productPriceXpath, $I)) {
                $productData['price'] = $I->grabTextFrom($productPriceXpath);
            } else {
                $productData['price'] = '';
            }
            $productUsersRateXpath = "//span[@id='frmSPAN_RateValueAtBottom']";
            if ($I->elementExist($productUsersRateXpath, $I)) {
                $productData['usersRate'] = $I->grabTextFrom($productUsersRateXpath);
            } else {
                $productData['usersRate'] = '';
            }
            $productData['recommendationCount'] = 0;
            $productRecommendationCountXpath = "//div[@id='frmPnlProductOffers']//span[@class='counter']";
            if ($I->elementExist($productRecommendationCountXpath, $I)) {
                $productData['recommendationCount'] = $I->grabTextFrom($productRecommendationCountXpath);
            }
            $productData['description'] = '';
            $productDescriptionCountXpath = "//div[@id='frmSecProductDescription']//div[@class='text']/div[@class='innerContent']";
            if ($I->elementExist($productDescriptionCountXpath, $I)) {
                $productData['description'] = $I->grabTextFrom($productDescriptionCountXpath);
            }

            $productAspects = $this->getAspectTitles('product', $I);
            $productColumnRow = array_merge($productFields, $productAspects);

            foreach ($productAspects as $key => $aspect) {
                $rateXpath = "//ul[@id='frmUL_RatesAverage']/li[" . $key . "]//div[@class='rating-container clearfix']//div//span[@class='number']";
                if ($I->elementExist($rateXpath, $I)) {
                    $aspectRate = $I->grabTextFrom($rateXpath);
                    $productData[$aspect] = $aspectRate;
                } else {
                    $productData[$aspect] = '';
                }
            }
            $categoriesXpath = "//nav[@id='dk-breadcrumbs']/ol/li";
            $categoriesNo = sizeof($I->getElements($categoriesXpath));
            for ($cat = $categoriesNo - 1; $cat >= 1; $cat--) {
                $categoryXpath = $categoriesXpath . "[" . $cat . "]/a/span";
                $categoryName = $I->grabTextFrom($categoryXpath);
                $productData[] = $categoryName;
            }

            $I->saveProduct($productData, $productColumnRow, $productsPath);

            print_r("\n Product ". $productData['id'] ."with title". $productData['title'] ."has been crawled \n");

        }
    }

    public function getAspectTitles($type, AcceptanceTester $I)
    {
        $aspectTitles = [];
        if ($type == 'product') {
            $aspectsXpath = "//ul[@id='frmUL_RatesAverage']/li";
        } else {
            $aspectsXpath = "//ul[@id='frmUL_CommentsList']/li[1]//div[@class='user-comment-content clearfix']//div[@class='rating-panel right']/div[@class='user-rating']/ul/li";
        }
        $aspectsNo = sizeof($I->getElements($aspectsXpath));

        for ($i = 1; $i <= $aspectsNo; $i++) {
            $aspectXpath = $aspectsXpath . "[" . $i . "]/span";
            $aspectTitles[$i] = $I->grabTextFrom($aspectXpath);
        }

        return $aspectTitles;
    }

}
