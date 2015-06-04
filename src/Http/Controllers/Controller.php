<?php namespace Aliukevicius\LaravelRbac\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController {

    /**
     * Set status message
     *
     * @param string $message
     * @param string $type
     */
    protected function setStatusMessage($message, $type = 'success')
    {
        if ($type == 'error') {
            $type = 'danger';
        }

        \Session::flash('statusMessage', [
            'type' => $type,
            'message' => $message,
        ]);
    }

}