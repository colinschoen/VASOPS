<?php

class Index_Controller extends BaseController {

    public function get_index() {
        return View::make('index');
    }

}