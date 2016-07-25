<?php

namespace app\Commands;

/**
 * Class: CliCommand
 *
 * @see \app\Commands\Command
 */
class CliCommand extends \app\Commands\Command
{
    /**
     * indexAction
     *
     */
    public function indexAction()
    {
        var_dump("Test");
    }
}
