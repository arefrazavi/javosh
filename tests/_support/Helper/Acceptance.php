<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\ModuleContainer;

class Acceptance extends \Codeception\Module
{

    protected $webDriverModule;

    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        parent::__construct($moduleContainer, $config);
        $this->webDriverModule = $this->getModule('WebDriver');
    }

    public function getElements($xpath) {
        return $this->webDriverModule->_findElements($xpath);
    }

    /**
     * Ожидание загрузки ajax.
     *
     * @param $timeout
     */
    public function waitAjaxLoad($timeout = 10)
    {
        $this->webDriverModule->waitForJS('return !!window.jQuery && window.jQuery.active == 0;', $timeout);
        $this->webDriverModule->wait(1);
    }
    /**
     * Ожидание загрузки страницы.
     *
     * @param $timeout
     */
    public function waitPageLoad($timeout = 10)
    {
        $this->webDriverModule->waitForJs('return document.readyState == "complete"', $timeout);
        $this->waitAjaxLoad($timeout);
    }
}
