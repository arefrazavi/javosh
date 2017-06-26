<?php


class CrawlDigikalaCest
{
    public function _before(AcceptanceTester $I)
    {
        //exec('phantomjs --webdriver=4444 &');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function CrawlTest(AcceptanceTester $I)
    {
        /***TODO Get list of search pages ***/
        //$inputPath = '/var/www/digikala-crawler/data/input/input.csv';
        //$searchPages = $I->readSearchPages($inputPath);

        /**Initialization**/
//        $categoryNames = [
//            "Category-Computer-Part",
//        ];
//        $mainSearchPageUrls = [
//            '/Search/Category-Computer-Parts/#!/Category-Computer-Parts/Category-Electronic-Devices/SortBy-1/Status-2/'
//        ];

        $categoryNames = [
            "Game-Console"
        ];
        $mainSearchPageUrls = [
            '/Search/Category-game-console/#!/Category-electronic-devices/Category-game-console/Category-video-audio-entertainment/SortBy-1/Status-2/',
        ];

        $mainSearchPageUrlsSize = sizeof($mainSearchPageUrls);

        for ($catIndex = 0; $catIndex < $mainSearchPageUrlsSize; $catIndex++) {
            $categoryName = $categoryNames[$catIndex];
            $mainSearchPageUrl = $mainSearchPageUrls[$catIndex];
            $commentsPath = '/var/www/arcrawler/data/comments/' . $categoryName . '/';
            $productsPath = '/var/www/arcrawler/data/products/' . $categoryName . '/';
            //$commentsPath = 'E:\\wamp\\www\\arcrawler\\data\\comments\\' . $categoryName . '\\'; //Windows
            //$productsPath = 'E:\\wamp\\www\\arcrawler\\data\\products\\' . $categoryName . '\\'; //Windows
            $requestsCount = 0;
            $productColumnRow = [];
            $commentsColumnRow = [];
            $commentFields = [
                'positivePoints',
                'negativePoints',
                'commentText',
                'like',
                'dislike',
            ];
            $productFields = [
                'id',
                'title',
                'price',
                'userRate',
                'recommendationCount',
                'description'
            ];
            $first = true;
            $commentAspects = [];
            $productAspects = [];

            /*** get the list of crawled products ***/
            $crawledProducts = [];
            if (file_exists($commentsPath)) {
                $fileNames = scandir($commentsPath);
                foreach ($fileNames as $fileName) {
                    $delimiterPos = strpos($fileName, '-');
                    if ($delimiterPos !== false) {
                        $productId = substr($fileName, 0, $delimiterPos);
                        $crawledProducts[$productId] = $productId;
                    }
                }
            } else {
                mkdir($commentsPath);
                mkdir($productsPath);
            }

            $I->amOnPage($mainSearchPageUrl);
            $I->seeInTitle('دیجی کالا');
            $requestsCount++;
            print_r("\n Page Request Count: " . $requestsCount . "\n");
            $I->waitPageLoad();

            $lastPageLinkXpath = "//article[@id='items']/div[@class='paging']/ul[@class='light-theme simple-pagination']/li[last()-1]/a";
            $pageCount = $I->grabTextFrom($lastPageLinkXpath);
            print_r("\n Number of search pages: " . $pageCount . "\n");

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                //$searchPage = '/Search/Category-Mobile-Phone/#!/Category-Electronic-Devices/Category-Mobile/Category-Mobile-Phone/Category-Mobile-Phone/PageNo-' . strval($pageNo) . '/SortBy-1/Status-2/';
                //$searchPage = 'Search/Category-tablet/#!/Category-electronic-devices/Category-tablet/Category-tablet-ebook-reader/PageNo-'. strval($pageNo) .'/SortBy-1/Status-2/';
                $searchPage = $mainSearchPageUrl . 'PageNo-' . strval($pageNo);

                $I->amOnPage($searchPage);
                $I->seeInTitle('دیجی کالا');
                $requestsCount++;
                print_r("\n Page Request Count: " . $requestsCount . "\n");
                print_r("\n Search page: " . $pageNo . "\n");
                $I->waitPageLoad();
                if ($I->cantSee('//div[@class="item"]')) {
                    continue;
                }

                $productIds = $I->grabMultiple("//div[@class='item']", "data-id");
                foreach ($productIds as $productId) {
                    if (isset($crawledProducts[$productId])) {
                        continue;
                    }

                    /*** Go to product page ***/
                    print_r("\n Product: " . $productId . "\n");
                    $product = array();
                    $product['id'] = $productId;
                    $productUrl = "/Product/DKP-" . $product['id'] . "/#!/displaycomment-0/page-1/sort-date/tab-comments/";
                    $I->amOnPage($productUrl);
                    $I->seeInTitle('دیجی کالا');
                    $requestsCount++;
                    print_r("\n Page Request Count: " . $requestsCount . "\n");
                    $I->waitPageLoad();

                    /***Retrieve and store product info***/
                    $productPriceXpath = "//div[@class='info-header']/h1";
                    $product['title'] = $I->grabTextFrom($productPriceXpath);

                    $productPriceXpath = "//span[@id='frmLblPayablePriceAmount']";
                    if ($I->elementExist($productPriceXpath, $I)) {
                        $product['price'] = $I->grabTextFrom($productPriceXpath);
                    } else {
                        $product['price'] = '';
                    }
                    $productUsersRateXpath = "//span[@id='frmSPAN_RateValueAtBottom']";
                    if ($I->elementExist($productUsersRateXpath, $I)) {
                        $product['usersRate'] = $I->grabTextFrom($productUsersRateXpath);
                    } else {
                        $product['usersRate'] = '';
                    }
                    $product['recommendationCount'] = 0;
                    $productRecommendationCountXpath = "//div[@id='frmPnlProductOffers']//span[@class='counter']";
                    if ($I->elementExist($productRecommendationCountXpath, $I)) {
                        $product['recommendationCount'] = $I->grabTextFrom($productRecommendationCountXpath);
                    }
                    $product['description'] = '';
                    $productDescriptionCountXpath = "//div[@id='frmSecProductDescription']//div[@class='text']/div[@class='innerContent']";
                    if ($I->elementExist($productDescriptionCountXpath, $I)) {
                        $product['description'] = $I->grabTextFrom($productDescriptionCountXpath);
                    }
                    //if ($first) {
                    $productAspects = $this->getAspectTitles('product', $I);
                    $productColumnRow = array_merge($productFields, $productAspects);
                    $commentAspects = $this->getAspectTitles('comment', $I);
                    $commentsColumnRow = array_merge($commentFields, $commentAspects);
                    //   $first = false;
                    //}
                    foreach ($productAspects as $key => $aspect) {
                        $rateXpath = "//ul[@id='frmUL_RatesAverage']/li[" . $key . "]//div[@class='rating-container clearfix']//div//span[@class='number']";
                        if ($I->elementExist($rateXpath, $I)) {
                            $aspectRate = $I->grabTextFrom($rateXpath);
                            $product[$aspect] = $aspectRate;
                        } else {
                            $product[$aspect] = '';
                        }
                    }
                    $categoriesXpath = "//nav[@id='dk-breadcrumbs']/ol/li";
                    $categoriesNo = sizeof($I->getElements($categoriesXpath));
                    for ($cat = $categoriesNo - 1; $cat >= 1; $cat--) {
                        $categoryXpath = $categoriesXpath . "[" . $cat . "]/a/span";
                        $categoryName = $I->grabTextFrom($categoryXpath);
                        $product[] = $categoryName;
                    }

                    $I->saveProduct($product, $productColumnRow, $productsPath);

                    /***Retrieve and store comments***/
                    while (true) {
                        $rateXpathGeneral = "//ul[@id='frmUL_CommentsList']/li";
                        $commentsNo = sizeof($I->getElements($rateXpathGeneral));
                        for ($c = 1; $c <= $commentsNo; $c++) {
                            $commentPath = "//ul[@id='frmUL_CommentsList']/li[" . $c . "]";

                            $product['comments'][$c]['rate']['like'] = 0;
                            $commentLikeXpath = $commentPath . "//div[@class='user-comment-header clearfix']//div[@class='like-container left clearfix']//a[@class='like']//span[@class='counter']";
                            if ($I->elementExist($commentLikeXpath, $I)) {
                                $product['comments'][$c]['rate']['like'] = $I->grabTextFrom($commentLikeXpath);
                            }

                            $product['comments'][$c]['rate']['dislike'] = 0;
                            $commentDislikeXpath = $commentPath . "//div[@class='user-comment-header clearfix']//div[@class='like-container left clearfix']//a[@class='dislike']//span[@class='counter']";
                            if ($I->elementExist($commentDislikeXpath, $I)) {
                                $product['comments'][$c]['rate']['dislike'] = $I->grabTextFrom($commentDislikeXpath);
                            }

                            foreach ($commentAspects as $key => $aspect) {
                                $rateXpath = $commentPath . "//div[@class='user-comment-content clearfix']//div[@class='rating-panel right']/div[@class='user-rating']/ul/li[" . $key . "]/div[@class='rating-container clearfix']/div[@class='bar done']";
                                $doneRatings = $I->getElements($rateXpath);
                                $product['comments'][$c]['aspect'][$aspect] = sizeof($doneRatings);
                            }

                            $product['comments'][$c]['positivePoints'] = '';
                            $product['comments'][$c]['negativePoints'] = '';
                            $commentEvaluationPath = '//div[@class="content-panel right"]//div[@class="comment-evaluation clearfix"]';
                            $positivePointsXpath = $commentPath . $commentEvaluationPath . '//div[@class="positive-point"]//ul/li/span';
                            if ($I->elementExist($positivePointsXpath, $I)) {
                                $positivePoints = $I->grabMultiple($positivePointsXpath);
                                $product['comments'][$c]['positivePoints'] = implode("\n", $positivePoints);
                            }
                            $negativePointsXpath = $commentPath . $commentEvaluationPath . '//div[@class="negetive-point"]//ul/li/span';
                            if ($I->elementExist($negativePointsXpath, $I)) {
                                $negativePoints = $I->grabMultiple($negativePointsXpath);
                                $product['comments'][$c]['negativePoints'] = implode("\n", $negativePoints);
                            }

                            $commentTextXpath = $commentPath . '//div[@class="content-panel right"]//div[@class="comment-text"]//p';
                            $product['comments'][$c]['commentText'] = $I->grabTextFrom($commentTextXpath);

                        }
                        $I->saveComments($product, $commentsColumnRow, $commentsPath);

                        //Go to the next page of comments if it exists
                        $nextPageXpath = "//a[@class='page-link next']";
                        if (!$I->elementExist($nextPageXpath, $I)) {
                            break;
                        }
                        $I->click($nextPageXpath);
                        $I->waitAjaxLoad(10);
                    }
                }
            }
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
