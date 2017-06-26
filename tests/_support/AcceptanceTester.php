<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    public function login($username, $password)
    {
        $I = $this;
        $I->amOnPage('/accounts/login/');
//        $I->see("Don't have an account?");
        $I->waitForElement('._rwf8p', 2);
        $I->submitForm('._rwf8p', [
            'username' => $username,
            'password' => $password
        ]);

    }

    /**
     * @param $fileName
     * @return array
     */
    function readSearchPages($fileName)
    {
        $data = [];
        ini_set("auto_detect_line_endings", true);
        $file = fopen($fileName, 'r');
        //fgetcsv($file); // skip first line
        //$columnsCount = sizeof($columnsRow);
        while (($line = fgetcsv($file)) !== FALSE) {
            $data = $line;
        }
        fclose($file);

        return $data;
    }

    function getWords()
    {
        $data = [];
        ini_set("auto_detect_line_endings", true);
        $file = fopen('', 'r');
        fgetcsv($file); // skip first line
        //$columnsCount = sizeof($columnsRow);
        while (($line = fgetcsv($file)) !== FALSE) {
            $data[$line[0]] = $line[1];
        }
        fclose($file);

        return $data;
    }

    public function saveComments($product, $columnLine, $outputPath)
    {
        $productID = $product['id'];
        $filePath = $outputPath . $productID . '-' . date("Y-m-d") . '.csv';
        $oldFile = file_exists($filePath);
        $file = fopen($filePath, 'a');
        if (!$oldFile) {
            fputcsv($file, $columnLine);
        }
        if (isset($product['comments'])) {
            foreach ($product['comments'] as $comment) {
                $line = [];
                $line[] = $comment['positivePoints'];
                $line[] = $comment['negativePoints'];
                $line[] = $comment['commentText'];
                $line[] = $comment['rate']['like'];
                $line[] = $comment['rate']['dislike'];
                foreach ($comment['aspect'] as $aspectRate) {
                    $line[] = $aspectRate;
                }
                fputcsv($file, $line);
            }
        }
        fclose($file);
    }

    /**
     * @param $product
     * @param $columnLine
     * @param $outputPath
     */
    public function saveProduct($product, $columnLine, $outputPath)
    {
        $filePath = $outputPath . 'products-' . date("Y-m-d") . '.csv';
        $oldFile = file_exists($filePath);
        $file = fopen($filePath, 'a');
        if (!$oldFile) {
            fputcsv($file, $columnLine);
        }
        fputcsv($file, $product);
        fclose($file);
    }

    public function convertToInt($string)
    {
        if (strpos($string, ',') !== false) {
            $string = str_replace(',', '', $string);
        } else {
            switch (strtolower(substr($string, -1))) {
                case 'k':
                    $string *= 1000;
                    break;
                case 'm':
                    $string *= 1000000;
                    break;
                default:
                    break;
            }
        }
        return intval($string);
    }

    public function elementExist($path, AcceptanceTester $I)
    {
        try {
            $I->seeElement($path);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
