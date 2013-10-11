<?php
class IndexController extends BaseController {

/**
* Make the index view
*/
public function get_index()
{
return View::make('index');
}

}