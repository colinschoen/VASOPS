<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//-------------------Public Routes---------------------------
//Index - Root
Route::get('/', array('as' => 'index', 'uses' => 'IndexController@get_index'));
//Public Ticket Hash Look up
Route::get('/ticket/hash/{hash}', array('uses' => 'IndexController@get_tickethash'));
//Forgot Password Reset Page
Route::get('/passwordreset/{hash}', array('uses' => 'IndexController@get_passwordreset'));

//AJAX
Route::post('/ajax/registration', array('as' => 'ajaxRegistration', 'uses' => 'AjaxController@post_registration', 'before' => 'csrf'));
Route::post('/ajax/login', array('as' => 'ajaxLogin', 'uses' => 'AjaxController@post_login'));
Route::post('/ajax/logout', array('as' => 'ajaxLogout', 'uses' => 'AjaxController@post_logout', 'before' => 'auth'));
Route::post('/ajax/vaedit', array('as' => 'ajaxVAEdit', 'uses' => 'AjaxController@post_vaedit', 'before' => 'auth|csrf'));
Route::post('/ajax/newticket', array('as' => 'ajaxNewTicket', 'uses' => 'AjaxController@post_newticket', 'before' =>'auth|csrf'));
Route::post('/ajax/closeticket', array('as' => 'ajaxCloseTicket', 'uses' => 'AjaxController@post_closeticket', 'before' =>'auth'));
Route::post('/ajax/reopenticket', array('as' => 'ajaxReopenTicket', 'uses' => 'AjaxController@post_reopenticket', 'before' =>'auth'));
Route::post('/ajax/replyticket', array('as' => 'ajaxReplyTicket', 'uses' => 'AjaxController@post_replyticket', 'before' =>'auth|csrf'));
Route::post('/ajax/deletebanner', array('as' => 'ajaxDeleteBanner', 'uses' => 'AjaxController@post_deletebanner', 'before' => 'auth|csrf'));
Route::post('/ajax/checkimagelinkback', array('as' => 'ajaxCheckImageLinkBack', 'uses' => 'AjaxController@post_checkimagelinkback'));
Route::post('/ajax/getvasbycategory', array('as' => 'ajaxGetVasByCategory', 'uses' => 'AjaxController@post_getvasbycategory'));
Route::post('/ajax/searchvas', array('as' => 'ajaxSearchVAs', 'uses' => 'AjaxController@post_searchvas'));
Route::post('/ajax/newguestticket', array('as' => 'ajaxnewguestticket', 'uses' => 'AjaxController@post_newguestticket', 'before' => 'csrf'));
Route::post('/ajax/guestfindticket', array('as' => 'ajaxguestfindticket', 'uses' => 'AjaxController@post_guestfindticket', 'before' => 'csrf'));
Route::Post('/ajax/guest/submitticket', array('as' => 'ajaxguestsubmitreply', 'uses' => 'AjaxController@post_guestsubmitticket', 'before' => 'csrf'));
Route::post('/ajax/guest/closeticket', array('as' => 'ajaxguestcloseticket', 'uses' => 'AjaxController@post_guestcloseticket', 'before' => 'csrf'));
Route::post('/ajax/guest/reopenticket', array('as' => 'ajaxguestreopenticket', 'uses' => 'AjaxController@post_guestreopenticket', 'before' => 'csrf'));
Route::post('/ajax/forgotpass', array('as' => 'ajaxforgotpass', 'uses' => 'AjaxController@post_forgotpass', 'before' => 'csrf'));

//Pages
    //GET
    Route::get('/va', array('as' => 'va', 'uses' => 'VaController@get_va', 'before' => 'auth'));
    Route::get('/click/{id}', array('as' => 'click', 'uses' => 'ClickController@get_click'));;
    Route::get('maintenance', array('as' => 'maintenance', 'uses' => 'IndexController@get_maintenance'));
    //POST
    Route::post('/va/uploadbanner', array('as' => 'uploadbanner', 'uses' => 'VaController@post_uploadbanner', 'before' => 'auth|csrf'));
//-----------------------------------------------------------------

//-
//--
//-----
//----------
//-----------------
//---------------------
//-------------------------
//---------------------------
//----------------------
//-----------------
//----------
//-----
//--
//-

//----------------Console Access Routes (Level 0)-------------------------


Route::get('console', array('as' => 'console', 'uses' => 'ConsoleController@get_index', 'before' => 'consoleauth'));
Route::get('console/login', array('as' => 'consolelogin', 'uses' => 'ConsoleController@get_login'));
Route::post('console/login', array('as' => 'postconsolelogin', 'uses' => 'ConsoleController@post_login'));
Route::any('console/validateLogin', array('as' => 'consolevalidatelogin', 'uses' => 'ConsoleController@post_validatelogin'));
Route::get('console/logout', array('as' => 'consolelogout', 'uses' => 'ConsoleController@get_logout', 'before' => 'consoleauth'));
Route::get('console/va/{id}', array('as' => 'consoleva', 'uses' => 'ConsoleController@get_va', 'before' => 'consoleauth'));
Route::any('console/va/{id}/status/{status}', array('as' => 'consolevaupdatestatus', 'uses' => 'ConsoleController@get_vaupdatestatus', 'before' => 'consoleauth'));
Route::get('console/va/{id}/linkbackstatus/{status}', array('as' => 'consolevaupdatelinkbackstatus', 'uses' => 'ConsoleController@get_vaupdatelinkbackstatus', 'before' => 'consoleauth'));
Route::get('console/helpdesk/{filter}', array('as' => 'consolehelpdesk', 'uses' => 'ConsoleController@get_helpdesk', 'before' => 'consoleauth'));
Route::get('console/helpdesk/view/{id}', array('as' => 'consolehelpdeskview', 'uses' => 'ConsoleController@get_helpdeskview', 'before' => 'consoleauth'));
Route::post('console/helpdesk/reply/{id}', array('as' => 'consolehelpdeskreply', 'uses' => 'ConsoleController@post_helpdeskreply', 'before' => 'consoleauth|csrf'));
Route::get('console/helpdesk/close/{id}', array('as' => 'consolehelpdeskclose', 'uses' => 'ConsoleController@get_helpdeskclose', 'before' => 'consoleauth'));
Route::get('console/helpdesk/open/{id}', array('as' => 'consolehelpdeskopen', 'uses' => 'ConsoleController@get_helpdeskopen', 'before' => 'consoleauth'));
Route::any('console/helpdesk/assign/{ticketid}/{cid?}', array('as' => 'consolehelpdeskassign', 'uses' => 'ConsoleController@get_post_helpdeskassign', 'before' => 'consoleauth'));
Route::get('console/emailtemplates', array('as' => 'consoleemailtemplates', 'uses' => 'ConsoleController@get_emailtemplates', 'before' => 'consoleauth'));
Route::post('console/emailtemplate/new', array('as' => 'consoleemailtemplatenew', 'uses' => 'ConsoleController@post_emailtemplatenew', 'before' => 'consoleauth|csrf'));
Route::get('console/emailtemplates/edit/{id}', array('as' => 'consoleemailtemplateedit', 'uses' => 'ConsoleController@get_emailtemplateedit', 'before' => 'consoleauth'));
Route::post('console/emailtemplates/edit/{id}', array('as' => 'consoleemailtemplatesubmitedit', 'uses' => 'ConsoleController@post_emailtemplateedit', 'before' => 'consoleauth|csrf'));
Route::post('console/uploadbanner', array('as' => 'consoleuploadbanner', 'uses' => 'ConsoleController@post_uploadbanner', 'before' => 'consoleauth|csrf'));
Route::post('console/removebanner', array('as' => 'consoleremovebanner', 'uses' => 'ConsoleController@post_removebanner', 'before' => 'consoleauth|csrf'));
Route::get('console/assignments', array('as' => 'consoleassignments', 'uses' => 'ConsoleController@get_assignments', 'before' => 'consoleauth'));
Route::get('console/assignments/complete/{assignment}/{va}', array('as' => 'consoleassignmentscomplete', 'uses' => 'ConsoleController@get_assignmentscomplete', 'before' => 'consoleauth'));
Route::get('console/assignments/inprogress/{assignment}/{va}', array('as' => 'consoleassignmentsinprogress', 'uses' => 'ConsoleController@get_assignmentsinprogress', 'before' => 'consoleauth'));
Route::post('console/va/email/{id}', array('as' => 'consoleemailva', 'uses' => 'ConsoleController@post_emailva', 'before' => 'consoleauth|csrf'));
Route::get('console/stats', array('as' => 'consolestats', 'uses' => 'ConsoleController@get_stats', 'before' => 'consoleauth'));
Route::get('console/profile', array('as' => 'consoleprofile', 'uses' => 'ConsoleController@get_profile', 'before' => 'consoleauth'));
Route::post('console/profile', array('as' => 'consoleprofilesave', 'uses' => 'ConsoleController@post_profilesave', 'before' => 'consoleauth|csrf'));
Route::post('console/hiddencategories/save', array('as' => 'consolevahiddencategoriessave', 'uses' => 'ConsoleController@post_vahiddencategoriessave', 'before' => 'consoleauth|csrf'));
Route::post('console/publiccategories/save', array('as' => 'consolevapubliccatgoriessave', 'uses' => 'ConsoleController@post_vapubliccategoriessave', 'consoleauth|csrf'));
    //------AJAX---------
    Route::post('console/ajax/vaedit', array('as' => 'consoleajaxvaedit', 'uses' => 'ConsoleController@post_vaedit', 'before' => 'consoleauth|csrf'));
    Route::post('console/ajax/createauditlog', array('as' => 'consoleajaxcreateauditlog', 'uses' => 'ConsoleController@post_createauditlog', 'before' => 'consoleauth|csrf'));
    Route::post('console/ajax/findlinkback', array('as' => 'consoleajaxfindlinkback', 'uses' => 'ConsoleController@post_findlinkback', 'before' => 'consoleauth|csrf'));
    Route::post('console/ajax/search', array('as' => 'consoleajaxsearch', 'uses' => 'ConsoleController@post_ajaxsearch', 'before' => 'consoleauth|csrf'));
    Route::post('console/ajax/emailtemplate/delete', array('as' => 'consoleemailtemplatedelete', 'uses' => 'ConsoleController@post_emailtemplatedelete', 'before' => 'consoleauth|csrf'));
    Route::post('console/ajax/flagvaawaitingresponse', array('as' => 'consoleflagvaawaitingresponse', 'uses' => 'ConsoleController@post_flagvaawaitingresponse', 'before' => 'consoleauth|csrf'));
    Route::post('console/ajax/flagvaawaitingresponsedelete', array('as' => 'consoleflagvaawaitingresponsedelete', 'uses' => 'ConsoleController@post_flagvaawaitingresponsedelete', 'before' => 'consoleauth|csrf'));
    //-------------------
//-----------------------------------------------------------------------

//---------------Console Elevated Access Routes (Level 1)----------------
Route::get('console/broadcasts', array('as' => 'consolebroadcasts', 'uses' => 'ConsoleController@get_broadcasts', 'before' => 'consoleauth1'));
Route::post('console/broadcasts/new', array('as' => 'consolebroadcastsnew', 'uses' => 'ConsoleController@post_broadcastsnew', 'before' => 'consoleauth1'));
Route::get('console/broadcasts/remove/{id}', array('as' => 'consolebroadcastsremove', 'uses' => 'ConsoleController@get_broadcastsremove', 'before' => 'consoleauth1'));
Route::get('console/broadcasts/vis/{id}', array('as' => 'consolebroadcastsvis', 'uses' => 'ConsoleController@get_broadcastsvis', 'before' => 'consoleauth1'));
Route::get('console/categories', array('as' => 'consolecategories', 'uses' => 'ConsoleController@get_categories', 'before' => 'consoleauth1'));
Route::post('console/categories/new', array('as' => 'consolecategoriesnew', 'uses' => 'ConsoleController@post_categoriesnew', 'before' => 'consoleauth1|csrf'));
Route::get('console/categories/edit/{id}', array('as' => 'consolecategoriesedit', 'uses' => 'ConsoleController@get_categoriesedit', 'before' => 'consoleauth1'));
Route::post('console/categories/edit/{id}', array('uses' => 'ConsoleController@post_categoriesedit', 'before' => 'consoleauth1|csrf'));
Route::get('console/helpdesk/delete/{id}', array('as' => 'consolehelpdeskdelete', 'uses' => 'ConsoleController@get_helpdeskdelete', 'before' => 'consoleauth1'));
Route::get('console/auditmanagers', array('as' => 'consoleauditmanagers', 'uses' => 'ConsoleController@get_auditmanagers', 'before' => 'consoleauth1'));
Route::post('console/auditmanagers/add', array('as' => 'consoleaddauditmanager', 'uses' => 'ConsoleController@post_auditmanagersadd', 'before' => 'consoleauth1|csrf'));
Route::post('console/auditmanagers/edit', array('as' => 'consoleeditauditmanager', 'uses' => 'ConsoleController@post_auditmanageredit', 'before' => 'consoleauth1|csrf'));
Route::get('console/auditmanagers/restore/{id}', array('as' => 'consolerestoreauditmanager', 'uses' => 'ConsoleController@get_auditmanagerrestore', 'before' => 'consoleauth1'));
Route::get('console/assignauditors', array('as' => 'consoleassignauditors', 'uses' => 'ConsoleController@get_assignauditors', 'before' => 'consoleauth1'));
Route::post('console/assignauditors', array('as' => 'consoleassignauditorsnew', 'uses' => 'ConsoleController@post_assignauditors', 'before' => 'consoleauth1|csrf'));
Route::get('console/assignments/delete/{id}', array('as' => 'consoleassignmentsdelete', 'uses' => 'ConsoleController@get_assignmentsdelete', 'before' => 'consoleauth1'));
Route::get('console/admin/import', array('as' => 'consoleadminimport', 'uses' => 'ConsoleController@get_adminimport', 'before' => 'consoleauth1'));
Route::get('console/admin/bannerimport', array('as' => 'consoleadminbannerimport', 'uses' => 'ConsoleController@get_adminbannerimport', 'before' => 'consoleauth1'));
Route::get('console/emailtemplates/edit/system/{id}', array('as' => 'consoleemailtemplateeditsystem', 'uses' => 'ConsoleController@get_systememailtemplatedit', 'before' => 'consoleauth1'));
Route::post('console/emailtemplates/system/edit', array('as' => 'consolesystememailtemplatesave', 'uses' => 'ConsoleController@post_systememailtemplatesave', 'before' => 'consoleauth1|csrf'));
Route::get('console/email', array('as' => 'consoleemail', 'uses' => 'ConsoleController@get_email', 'before' => 'consoleauth1'));
Route::post('console/email', array('as' => 'consoleemailsend', 'uses' => 'ConsoleController@post_email', 'before' => 'consoleauth1|csrf'));
Route::get('console/transferownership', array('as' => 'consoletransferownership', 'uses' => 'ConsoleController@get_transferownership', 'before' => 'consoleauth1'));
Route::get('console/news', array("as" => 'consolenews', 'uses' => 'ConsoleController@get_news', 'before' => 'consoleauth1'));
Route::post('console/news', array("as" => 'consolecreatenews', 'uses' => 'ConsoleController@post_news', 'before' => 'consoleauth1|csrf'));
Route::get('console/news/remove/{id}', array('as' => 'consolenewsremove', 'uses' => 'ConsoleController@get_newsremove', 'before' => 'consoleauth1'));
    //------AJAX---------
    Route::post('console/categories/deletechild', array('as' => 'consolecategoriesdeletechild', 'uses' => 'ConsoleController@post_categoriesdeletechild', 'before' => 'consoleauth1|csrf'));
    Route::post('console/categories/deleteparent', array('as' => 'consolecategoriesdeleteparent', 'uses' => 'ConsoleController@post_categoriesdeleteparent', 'before' => 'consoleauth1|csrf'));
    Route::post('console/transferownership', array('as' => 'consoleposttransferownership', 'uses' => 'ConsoleController@post_transferownership', 'before' => 'consoleauth1|csrf'));
    //-------------------
//----------------------------------------------------------------------