<?php

namespace app\Controllers;

use nanophp\Libraries\Template as Template;

/**
 * Class: SiteController
 *
 * @see \app\Controllers\Controller
 */
class SiteController extends \app\Controllers\Controller
{
    /**
     * __construct
     *
     * Call parent controllers constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * indexAction
     *
     */
    public function indexAction()
    {
        Template::factory()->render('site/index');
    }
}
