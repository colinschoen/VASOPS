<?php
class AjaxController extends BaseController {

    public function post_logout() {
        Auth::user()->logout();
    }

    public function post_login() {
        $postStr = Input::get('data');
        parse_str($postStr, $post);
        $email = $post['inputEmail'];
        $password = $post['inputPassword'];
        $user = User::where('email', '=', $email)->first();
        if (!empty($user)) {
           if (Hash::check($password, $user->password)) {
               Auth::user()->loginUsingId($user->cid);
               //Update the database with their last IP
               $user->ip = Request::getClientIp();
               $user->save();
               $fname = User::getFirstName(Auth::user()->get()->cid);
               Session::put('fname', $fname);
               echo $fname;
            }
            else {
                echo "/errorBadPassword";
            }
        }
        else {
            echo "/errorBadEmail";
        }
    }

    public function post_vaedit() {
        //Pull our AJAX data
        $postStr = Input::get('data');
        //Parse the serialized string
        parse_str($postStr, $post);
        //      Laravel gets mad and throws an error if category isn't defined so let's go ahead and just define it but leave it empty.
        if (empty($post['inputCategory'])) {
            $post['inputCategory'] = '';
        }

        //      Format our URL field with the http before if it isn't there already (Must evaluate with ===)
        if (strpos($post['inputUrl'],'http://') === FALSE AND strpos($post['inputUrl'],'https://') === FALSE) {
            $post['inputUrl'] = 'http://' . $post['inputUrl'];
        }

        //      Start our validator
        $validator = Validator::make(
            array(
                'Va Name' => $post['inputVaName'],
                'Url' => $post['inputUrl'],
                'Description' => $post['inputDescription'],
                'Vatsim Image Page Link' => $post['inputVatsimImagePageLink'],
                'Country' => $post['inputCountry'],
                'Name' => $post['inputName'],
                'Email' => $post['inputEmail'],
                'Password' => $post['inputPassword'],
                'Password_confirmation' => $post['inputPassword_confirmation'],
                'Category' => $post['inputCategory'],
            ),
            array(
                'Va Name' => 'required',
                'Url' => 'required|url',
                'Description' => 'required|max:200',
                'Vatsim Image Page Link' => 'required|url',
                'Country' => 'required',
                'Name' => 'required',
                'Email' => 'required|email',
                'Password' => 'min:6|confirmed',
                'Password_confirmation' => 'min:6',
                'Category' => 'required|max:' . Setting::fetch('max_categories'),
            ),
            array (
                'Category.max' => 'You have selected more than :max categories.',
            )
        );
        $check = User::where('email', '=', $post['inputEmail'])->where('cid', '!=', Auth::user()->get()->cid)->count();
        if ($validator->fails())
        {
            // The given data did not pass validation
            $messages = $validator->messages();
            $errorStr = '';
            foreach ($messages->all('<li>:message</li>') as $message)
            {
                $errorStr .= '<div class="alert alert-error">' . $message . '</div>';
            }
            echo $errorStr;

        }

        elseif ($check > 0) {
            $errorStr = '<div class="alert alert-error">That email address is already taken by another virtual airline.</div>';
            echo $errorStr;
        }

        else {

            if (Category::isHiddenCategory($post['inputCategory'])) {
                echo '<div class="alert alert-error">You are not authorized to use that category.</div>';
                die;
            }

            //Pull our current VA record
            $vas = User::find(Auth::user()->get()->cid);
            //Map our fields
            //Hash our password if one is inputted
            if (!empty($post['inputPassword'])) {
                $vas->password = Hash::make($post['inputPassword']);
            }
            $vas->vaname = $post['inputVaName'];
            $vas->url = $post['inputUrl'];
            $vas->description = $post['inputDescription'];
            $vas->vatsimimagepagelink = $post['inputVatsimImagePageLink'];
            $vas->country = $post['inputCountry'];
            $vas->name = $post['inputName'];
            $vas->email = $post['inputEmail'];
            //We need to check and see if any hidden categories are currently included in the categories string and add them to our new string if so
            $currentCategories = explode(',', $vas->categories);
            array_pop($currentCategories);
            //Get a list of hidden categories
            $appendlist = '';
            foreach ($currentCategories as $currentCategory) {
                if (Category::isHiddenCategory($currentCategory)) {
                    $appendlist .= $currentCategory . ',';
                }
            }
            $vas->categories = implode (",", $post['inputCategory']) . ',';
            $vas->categories .= $appendlist;
            //Save our data
            $vas->save();


        }

    }

    public function post_registration()
    {
        $post = array();
        $post['inputCid'] = Input::get('inputCid');
        $post['inputVaName'] = Input::get('inputVaName');
        $post['inputVaStartDate'] = Input::get('inputVaStartDate');
        $post['inputUrl'] = Input::get('inputUrl');
        $post['inputDescription'] = Input::get('inputDescription');
        $post['inputVatsimImagePageLink'] = Input::get('inputVatsimImagePageLink');
        $post['inputCountry'] = Input::get('inputCountry');
        $post['inputName'] = Input::get('inputName');
        $post['inputEmail'] = Input::get('inputEmail');
        $post['inputPassword'] = Input::get('inputPassword');
        $post['inputPassword_confirmation'] = Input::get('inputPassword_confirmation');
        $post['inputCategory'] = Input::get('inputCategory');
        $post['inputCopyVARoster'] = Input::get('inputCopyVARoster');

//      Laravel gets mad and throws an error if category isn't defined so let's go ahead and just define it but leave it empty.
        if (empty($post['inputCategory'])) {
            $post['inputCategory'] = '';
        }
//      Format our URL field with the http before if it isn't there already
        if (!strpos($post['inputUrl'],'http://') AND !strpos($post['inputUrl'],'https://')) {
            $post['inputUrl'] = 'http://' . $post['inputUrl'];
        }

//      Start our validator
        if (Input::hasFile('inputUploadVARoster')) {
            $validator = Validator::make(
                array(
                    'Cid' => $post['inputCid'],
                    'Va Name' => $post['inputVaName'],
                    'Va Start Date' => $post['inputVaStartDate'],
                    'Url' => $post['inputUrl'],
                    'Description' => $post['inputDescription'],
                    'Vatsim Image Page Link' => $post['inputVatsimImagePageLink'],
                    'Country' => $post['inputCountry'],
                    'Name' => $post['inputName'],
                    'Email' => $post['inputEmail'],
                    'Password' => $post['inputPassword'],
                    'Password_confirmation' => $post['inputPassword_confirmation'],
                    'Category' => $post['inputCategory'],
                ),
                array(
                    'Cid' => 'required|integer|unique:vas,cid',
                    'Va Name' => 'required',
                    'Va Start Date' => 'required|date',
                    'Url' => 'required|url',
                    'Description' => 'required|max:200',
                    'Vatsim Image Page Link' => 'required|url',
                    'Country' => 'required',
                    'Name' => 'required',
                    'Email' => 'required|email|unique:vas,email',
                    'Password' => 'required|min:6|confirmed',
                    'Password_confirmation' => 'required|min:6',
                    'Category' => 'required|max:' . Setting::fetch('max_categories'),
                ),
                array (
                    'Category.max' => 'You have selected more than :max categories.',
                    'Cid.unique' => 'There is already an account with an active virtual airline with that CID.',
                )
            );
        }
        else {
            $validator = Validator::make(
                array(
                    'Cid' => $post['inputCid'],
                    'Va Name' => $post['inputVaName'],
                    'Va Start Date' => $post['inputVaStartDate'],
                    'Url' => $post['inputUrl'],
                    'Description' => $post['inputDescription'],
                    'Vatsim Image Page Link' => $post['inputVatsimImagePageLink'],
                    'Country' => $post['inputCountry'],
                    'Name' => $post['inputName'],
                    'Email' => $post['inputEmail'],
                    'Password' => $post['inputPassword'],
                    'Password_confirmation' => $post['inputPassword_confirmation'],
                    'Category' => $post['inputCategory'],
                    'Roster' => $post['inputCopyVARoster'],
                ),
                array(
                    'Cid' => 'required|integer|unique:vas,cid',
                    'Va Name' => 'required',
                    'Va Start Date' => 'required|date',
                    'Url' => 'required|url',
                    'Description' => 'required|max:200',
                    'Vatsim Image Page Link' => 'required|url',
                    'Country' => 'required',
                    'Name' => 'required',
                    'Email' => 'required|email|unique:vas,email',
                    'Password' => 'required|min:6|confirmed',
                    'Password_confirmation' => 'required|min:6',
                    'Category' => 'required|max:' . Setting::fetch('max_categories'),
                    'Roster' => 'required',
                ),
                array (
                    'Category.max' => 'You have selected more than :max categories.',
                    'Cid.unique' => 'There is already an account with an active virtual airline with that CID.',
                    'Roster.required' => 'Please either upload a file containing your roster or copy and paste the roster.'
                )
            );
        }

        if ($validator->fails())
        {
            // The given data did not pass validation
            $messages = $validator->messages();
            $errorStr = '';
            foreach ($messages->all('<li>:message</li>') as $message)
            {
                $errorStr .= '<div class="alert alert-error">'. $message . '</div>';
            }
            echo $errorStr;

        }
        else {


            //Check to see if the member submitted a file, if so let's get that uploaded and moved to the appropriate place
            if (Input::hasFile('inputUploadVARoster')) {
                $file = Input::file('inputUploadVARoster');
                //Create an array of acceptable mime types
                $mimetypes = array('text/plain', 'application/msword', 'application/zip', 'text/rtf', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                //Create an array of acceptable file extensions
                $fileextentions = array('txt', 'rtf', 'doc', 'docx', 'csv', 'xls', 'xlsx');
                if (!in_array($file->getMimeType(), $mimetypes)) {
                    echo '<div class="alert alert-error">The roster file you have uploaded does not appear to be an acceptable file type.</div>';
                    die;
                }
                if (!in_array($file->getClientOriginalExtension(), $fileextentions)) {
                    echo '<div class="alert alert-error">The roster file you have uploaded does not appear to have a valid file extension.</div>';
                    die;
                }
                //Alright looks like this is an acceptable file.
                $destination_path = public_path() . Setting::fetch('roster_directory');
                $fileName = sha1($post['inputCid'] . time()) . $post['inputCid'] . '.' . $file->getClientOriginalExtension();
                $file->move($destination_path, $fileName);
            }
            if (Category::isHiddenCategory($post['inputCategory'])) {
                echo '<div class="alert alert-error">You are not authorized to use that category.</div>';
                die;
            }
            //Submit Data
            //Create an instance of our model
            $vas = new User;
            //Map our fields
            $vas->cid = $post['inputCid'];
            //Hash our password
            $vas->password = Hash::make($post['inputPassword']);
            $vas->vaname = $post['inputVaName'];
            $vas->startdate = $post['inputVaStartDate'];
            $vas->url = $post['inputUrl'];
            $vas->description = $post['inputDescription'];
            $vas->vatsimimagepagelink = $post['inputVatsimImagePageLink'];
            $vas->country = $post['inputCountry'];
            $vas->name = $post['inputName'];
            $vas->email = $post['inputEmail'];
            $vas->categories = implode (",", $post['inputCategory']) . ',';
            //All VAs must be approved first so the default status will be 0 for unapproved or not active.
            $vas->status = '0';
            if (!Input::hasFile('inputUploadVARoster'))
                $vas->rosterdata = nl2br($post['inputCopyVARoster']);
            else
                $vas->rosterfile = $fileName;
            //Save our data
            $vas->save();

            //Great, now let's send our welcome email to the member
            $template = SystemEmailTemplate::find('registration_received');
            $subject = $template->subject;
            $email = $post['inputEmail'];
            $content = EmailTemplate::replaceContent($template->content, $post['inputCid']);
            //Send our email
            $data = array('name' => $post['inputName'], 'email' => $email, 'subject' => $subject);
            //Alright. Time to do some email sending.
            Mail::send('email.default', array("content" => $content), function($message) use ($data) {
                $message->to($data['email'], $data['name'])->subject($data['subject']);
            });
        }

    }

    public function post_associateRegistration()
    {
        $post = array();
        $post['inputCid'] = Input::get('inputCid');
        $post['inputVaName'] = Input::get('inputVaName');
        $post['inputUrl'] = Input::get('inputUrl');
        $post['inputDescription'] = Input::get('inputDescription');
        $post['inputVatsimImagePageLink'] = Input::get('inputVatsimImagePageLink');
        $post['inputCountry'] = Input::get('inputCountry');
        $post['inputName'] = Input::get('inputName');
        $post['inputEmail'] = Input::get('inputEmail');
        $post['inputPassword'] = Input::get('inputPassword');
        $post['inputPassword_confirmation'] = Input::get('inputPassword_confirmation');

//      Format our URL field with the http before if it isn't there already
        if (!strpos($post['inputUrl'],'http://') AND !strpos($post['inputUrl'],'https://')) {
            $post['inputUrl'] = 'http://' . $post['inputUrl'];
        }

        $validator = Validator::make(
            array(
                'Cid' => $post['inputCid'],
                'Va Name' => $post['inputVaName'],
                'Url' => $post['inputUrl'],
                'Description' => $post['inputDescription'],
                'Vatsim Image Page Link' => $post['inputVatsimImagePageLink'],
                'Country' => $post['inputCountry'],
                'Name' => $post['inputName'],
                'Email' => $post['inputEmail'],
                'Password' => $post['inputPassword'],
                'Password_confirmation' => $post['inputPassword_confirmation'],
            ),
            array(
                'Cid' => 'required|integer|unique:vas,cid',
                'Va Name' => 'required',
                'Url' => 'required|url',
                'Description' => 'required|max:200',
                'Vatsim Image Page Link' => 'required|url',
                'Country' => 'required',
                'Name' => 'required',
                'Email' => 'required|email|unique:vas,email',
                'Password' => 'required|min:6|confirmed',
                'Password_confirmation' => 'required|min:6',
            ),
            array (
                'Cid.unique' => 'There is already an account with an active virtual airline with that CID.',
            )
        );

        if ($validator->fails())
        {
            // The given data did not pass validation
            $messages = $validator->messages();
            $errorStr = '';
            foreach ($messages->all('<li>:message</li>') as $message)
            {
                $errorStr .= '<div class="alert alert-error">'. $message . '</div>';
            }
            echo $errorStr;

        }
        else {

            //Submit Data
            //Create an instance of our model
            $vas = new User;
            //Map our fields
            $vas->cid = $post['inputCid'];
            //This is an associate
            $vas->associate = 1;
            //Hash our password
            $vas->password = Hash::make($post['inputPassword']);
            $vas->vaname = $post['inputVaName'];
            $vas->url = $post['inputUrl'];
            $vas->description = $post['inputDescription'];
            $vas->vatsimimagepagelink = $post['inputVatsimImagePageLink'];
            $vas->country = $post['inputCountry'];
            $vas->name = $post['inputName'];
            $vas->email = $post['inputEmail'];
            //All VAs must be approved first so the default status will be 0 for unapproved or not active.
            $vas->status = '0';
            //Save our data
//            $vas->save();
//TODO ^^^^^^^
            die();
            //Great, now let's send our welcome email to the member
            $template = SystemEmailTemplate::find('registration_received');
            $subject = $template->subject;
            $email = $post['inputEmail'];
            $content = EmailTemplate::replaceContent($template->content, $post['inputCid']);
            //Send our email
            $data = array('name' => $post['inputName'], 'email' => $email, 'subject' => $subject);
            //Alright. Time to do some email sending.
            Mail::send('email.default', array("content" => $content), function($message) use ($data) {
                $message->to($data['email'], $data['name'])->subject($data['subject']);
            });
        }

    }



    public function post_closeticket() {
        //Pull our AJAX data
        $ticketid = Input::get('data');
        //Pull our ticket
        $ticket = Ticket::where('id', '=', $ticketid)->where('vid', '=', Auth::user()->get()->cid)->first();
        //Does this user own this ticket?
        if (count($ticket) == 1) {
            //Set our ticket status to 0 or closed
            $ticket->status = 0;
            //Save our update
            $ticket->save();
            echo "1";
        }
    }

    public function post_reopenticket() {
        //Pull our AJAX data
        $ticketid = Input::get('data');
        //Pull our ticket
        $ticket = Ticket::where('id', '=', $ticketid)->where('vid', '=', Auth::user()->get()->cid)->first();
        //Does this user own this ticket?
        if (count($ticket) == 1) {
            //Set our ticket status to 1 or open
            $ticket->status = 1;
            //Save our update
            $ticket->save();
            echo "1";
        }
    }

    public function post_newticket() {
        //Pull our AJAX Post Data
        $postStr = Input::get('data');
        //Parse the serialized string
        parse_str($postStr, $post);

        //      Start our validator
        $validator = Validator::make(
            array(
                'Subject' => $post['inputTicketSubject'],
                'Description' => $post['inputTicketContent'],
            ),
            array(
                'Subject' => 'required',
                'Description' => 'required',
            ),
            array (
                'Subject.required' => 'Please enter a subject. ',
                'Description.required' => 'Please enter a description.',
            )
        );

        if ($validator->fails())
        {
            // The given data did not pass validation
            $messages = $validator->messages();
            $errorStr = '';
            foreach ($messages->all('<li>:message</li>') as $message)
            {
                $errorStr .= '<div class="alert alert-error">'. $message . '</div>';
            }
            echo $errorStr;

        }

        else {
            $ticket = new Ticket;
            $ticket->subject = $post['inputTicketSubject'];
            $ticket->description = $post['inputTicketContent'];
            $ticket->vid = Auth::user()->get()->cid;
            //Our ticket status will start as open.
            $ticket->status = '1';
            $ticket->save();
            $id = $ticket->id;
            //Get a list of auditors who have subscribed to ticket email notifications and send them an email
            $auditors = ConsoleUser::where('ticketnotifications', '=', '1')->where('access', '>', -1)->get();
            foreach ($auditors as $auditor) {
                $email = $auditor->email;
                if (empty($email))
                    continue;
                $name = User::getFullName($ticket->vid);
                $body = "Hello " . $auditor->name . ",<br /><br />A new ticket was created by " . $name . "<br /><br />" . $post['inputTicketContent'] . "<br /><br /><br /> <strong>Do not reply to this email. If you wish to reply to this ticket, please do so through your account online.</strong>";
                $data = array('subject' => 'VASOPS New Ticket: ' . $post['inputTicketSubject'], 'name' => $auditor->name, 'email' => $auditor->email);
                Mail::send('email.default', array("content" => $body), function($message) use ($data) {
                    $message->to($data['email'], $data['name'])->subject($data['subject']);
                });
            }
            echo $id;
        }

    }

    public function post_replyticket() {
        $postStr = Input::get('data');
        parse_str($postStr, $post);

        $validator = Validator::make(
           array (
               'Reply' => $post['inputReplyTicket'],
               'tid' => $post['tid'],
           ),
           array (
              'Reply' => 'required',
              'tid' => 'required|integer',
           ),
           array (
               'Reply.required' => 'Please enter a reply.',
               'tid.required' => 'The system cannot find a ticket to reply to.',
               'tid.integer' => 'The system has been sent an invalid ticket id to reply to.',
           )
        );

        //Verify some smart ass didn't try to change the hidden input field (tid) or reply to a closed ticket.
        $count = Ticket::where('id', '=', $post['tid'])->where('vid', '=', Auth::user()->get()->cid)->where('status', '=', '1')->count();
        if ($count == 0) {
            //Damn them.
            echo '<div class="alert alert-error"><li>Seriously? Nice try.</li></div>';
        }


        if ($validator->fails()) {
            $messages = $validator->messages();
            $errorStr = '';
            foreach ($messages->all('<li>:message</li>') as $message)
            {
                $errorStr .= '<div class="alert alert-error">'. $message . '</div>';
            }
            echo $errorStr;
        }

        else {
            $reply = new Reply;
            $reply->tid = $post['tid'];
            $reply->author = Auth::user()->get()->cid;
            $reply->content = $post['inputReplyTicket'];
            //Save our ticket update
            $reply->save();
            //Find or reply id
            $reply = Reply::orderBy('updated_at', 'DESC')->first();

            //Finally we need to update the updated_at field of our master ticket table
            $ticket = Ticket::find($post['tid']);
            $ticket->updated_at = $reply->updated_at;
            //Save our ticket update
            $ticket->save();
            //Check to see if there is an assigned auditor. If so send them an email notification
            if (!empty($ticket->assigned)) {
                $data = array();
                $auditor = ConsoleUser::find($ticket->assigned);
                if (!empty($auditor)) {
                    $data['auditor'] = $auditor;
                    $data['subject'] = "VATSIM VA New Ticket Update";
                    if (!empty($auditor->email)) {
                        $body = "Hello " . ConsoleUser::getName($ticket->assigned) . ",<br /><br />There has been an update to your assigned ticket " . $ticket->subject . " by VA Administrator " . User::getFullName($ticket->vid) . ". <br /><br />" . $reply->content . "<br /><br /><br /> <strong>Do not reply to this email. If you wish to reply to this ticket, please do so through the auditor console.</strong>";
                        Mail::send('email.default', array("content" => $body), function($message) use ($data) {
                            $message->to($data['auditor']->email, $data['auditor']->name)->subject($data['subject']);
                        });
                    }
                }
            }
            //Return 1 to inform the client this was successful.
            echo '1';
        }

    }

    public function post_checkimagelinkback() {
            $response = User::testLinkBack(Auth::user()->get()->cid);
            if ($response) {
                if (Auth::user()->get()->status == 1) {
                    //We will echo 1! to tell the client to replace the module icon to a check instead of the red X because there VA is now approved
                    echo '1!';
                }
                else {
                    echo '1';
                }
        }
        else {
            //The content was not found return 0 to the client.
             echo '0';
        }
    }

    public function post_getvasbycategory() {
        $id = Input::get('data');
        $category = Category::findOrFail($id);
        $vas = User::where('categories', 'like','%' . $id. ',%')->where('status', '=', '1')->orderBy('vaname', 'ASC')->get();
        if (count($vas) == 0) {
            echo '<h4>No Virtual Airlines Found.</h4>';
        }
        else {
            $maxwidth = Setting::fetch('banner_maxwidth');
            $output = '';
            foreach ($vas as $va) {
                //There is the potential that another number is before the category id and it got put in with this so let's double check this is just for this category
                $categories = explode(',', $va->categories);
                array_pop($categories);
                if (!in_array($id, $categories))
                    continue;
                $va->description = html_entity_decode($va->description);
                $va->vaname = html_entity_decode($va->vaname);
                $va->url = html_entity_decode($va->url);
                $banner_directory = $banner_directory = rtrim(Setting::fetch('banner_directory'), '/').'/';
                $banner = '';
                if ($va->banner) {
                    $banner = User::getBannerUrl($va->cid, $banner_directory);
                    $output .= '<div class="bannerbg"><a target="_blank" href="' . URL::to('/click') . '/' .  $va->cid . '"><img style="max-width:' . $maxwidth . ';" class="img-polaroid" src="' . $banner . '" alt="Banner" /></a></div><div class="well"><a target="_blank" href="' . URL::to('/click') . '/' .  $va->cid . '"><h4>' . $va->vaname . '</h4></a><blockquote style="margin-top: 4px;">'. $va->description . '</blockquote></div>';

                }
                else {
                    $output .= '<div class="well"><a target="_blank" href="' . URL::to('/click') . '/' .  $va->cid . '"><h4>' . $va->vaname . '</h4></a><blockquote style="margin-top: 4px;">'. $va->description . '</blockquote></div>';
                }
            }
            echo $output;
        }

    }

    public function post_deletebanner() {
        $user = User::find(Auth::user()->get()->cid);
        //Update the banner field to nothing, but don't remove the actual image on the server. If they decide to upload another one, it will overwrite it.
        $user->banner = "";
        $user->save();
        //Finally echo 1
        echo 1;
    }

    public function post_searchvas() {
        $query = Input::get('query');
        $vas = User::where('status', '=', 1)->where(function($q) use ($query)
            {
                $q->where('vaname', 'like','%' . $query . '%')->orWhere('description','like', '%' . $query . '%')->orWhere('url', 'like' ,'%' . $query . '%')->orWhere('url', 'like','%'. $query . '%');
            })
            ->orderBy('vaname', 'ASC')->get();
        if (count($vas) == 0 || empty($query) || $query == " ") {
            echo '<h4>No Virtual Airlines Found.</h4>';
        }
        else {
            $maxwidth = Setting::fetch('banner_maxwidth');
            $banner_directory = rtrim(Setting::fetch('banner_directory'), '/').'/';
            $output = '';
            foreach ($vas as $va) {
                $va->description = html_entity_decode($va->description);
                $va->vaname = html_entity_decode($va->vaname);
                $va->url = html_entity_decode($va->url);
                $banner = '';
                if ($va->banner) {
                    $banner = User::getBannerUrl($va->cid, $banner_directory);
                    $output .= '<div class="bannerbg"><a target="_blank" href="' . URL::to('/click') . '/' .  $va->cid . '"><img style="max-width:' . $maxwidth . ';" class="img-polaroid" src="' . $banner . '" alt="Banner" /></a></div><div class="well"><a target="_blank" href="' . URL::to('/click') . '/' .  $va->cid . '"><h4>' . $va->vaname . '</h4></a><blockquote style="margin-top: 4px;">'. $va->description . '</blockquote></div>';

                }
                else {
                    $output .= '<div class="well"><a target="_blank" href="' . URL::to('/click') . '/' .  $va->cid . '"><h4>' . $va->vaname . '</h4></a><blockquote style="margin-top: 4px;">'. $va->description . '</blockquote></div>';
                }
            }
            echo $output;
        }
    }

    public function post_newguestticket() {

        $postStr = Input::get('data');
        parse_str($postStr, $post);
        $post['supportInputHuman'] = strtolower($post['supportInputHuman']);
        //Create our validator
        $validator = Validator::make(array(
            'name' => $post['supportInputName'],
            'email' => $post['supportInputEmail'],
            'subject' => $post['supportInputSubject'],
            'description' => $post['supportInputDescription'],
            'human' => $post['supportInputHuman'],
        ),
        array(
            'name' => 'required|max:50',
            'email' => 'required|email|max:100',
            'subject' => 'required|max:100',
            'description' => 'required|max:4000',
            'human' => 'required|in:vatsim',
        ),
        array(
            'human.required' => 'Please fill in the anti-spam question. (Hint It ends with SIM and starts with VAT.).',
            'human.in' => 'The antispam answer is not correct. (Hint: It ends with SIM and starts with VAT.)',
        ));
        //Let's make sure that our user is not logged in
        if ($validator->fails() || Auth::user()->check()) {
            $messages = $validator->messages();
            $errorStr = '';
            //If we are logged in let's append an error to that message.
            if (Auth::user()->check())
                $errorStr .= '<div class="alert alert-error">Please use the built in ticket help desk in your VA console. You can log in at the top right of this page.</div>';
            foreach ($messages->all('<li>:message</li>') as $message)
            {
                $errorStr .= '<div class="alert alert-error">'. $message . '</div>';
            }
            echo $errorStr;
        }
        else {
            //All clear.
            $ticket = new Ticket();
            //We set this value to -1 since it is not associated with a specific VID
            $ticket->vid = -1;
            $ticket->subject = $post['supportInputSubject'];
            $ticket->description = $post['supportInputDescription'];
            //The status is open by default
            $ticket->status = 1;
            $ticket->name = $post['supportInputName'];
            $ticket->email = $post['supportInputEmail'];
            $ticket->hash = sha1($post['supportInputName']. time());
            //Save our ticket
            $ticket->save();
            //Create a URL for the user to access their ticket at
            $url = URL::to('/') . '/ticket/hash/' . $ticket->hash;
            //Now email the user the ticket # and a direct link to access updates (using the hash)
            $body = "Hello " . $post['supportInputName'] . ",<br /><br />The VATSIM VASOPs department has received your ticket. Please use the following link to view and update your ticket.<br /><br /> <a href='" . $url . "'>" . $url . "</a><br /><br />You may also view your ticket at <a href='" . Url::to('/') . "/#moduleSupport'>" . Url::to('/') . "/#moduleSupport</a> by entering the following details.<br /><br /><strong>Ticket ID: " . $ticket->id . "</strong><br /><strong>Email: " . $post['supportInputEmail'] . "</strong><br /><br /><br /> <strong>Do not reply to this email. If you wish to reply to this ticket, please do so through your account online.</strong>";
            $data = array('subject' => 'VASOPS Ticket #' . $ticket->id . ": " . $post['supportInputSubject'], 'email' => $post['supportInputEmail'], 'name' => $post['supportInputName']);
            Mail::send('email.default', array("content" => $body), function($message) use ($data) {
                $message->to($data['email'], $data['name'])->subject($data['subject']);
            });
            //Get a list of auditors who have subscribed to ticket email notifications and send them an email
            $auditors = ConsoleUser::where('ticketnotifications', '=', '1')->where('access', '>', -1)->get();
            foreach ($auditors as $auditor) {
                $email = $auditor->email;
                if (empty($email))
                    continue;
                $body = "Hello " . $auditor->name . ",<br /><br />A new ticket was created by " . $post['supportInputName'] . "<br /><br />" . $post['supportInputDescription'] . "<br /><br /><br /> <strong>Do not reply to this email. If you wish to reply to this ticket, please do so through your account online.</strong>";
                $data = array('subject' => 'VASOPS New Ticket: ' . $post['supportInputSubject'], 'name' => $auditor->name, 'email' => $auditor->email);
                Mail::send('email.default', array("content" => $body), function($message) use ($data) {
                    $message->to($data['email'], $data['name'])->subject($data['subject']);
                });
            }
            //That will be all. Let's send 1 to our client to let me know that everything went well.
            echo 1;
        }
    }

    public function post_guestfindticket() {
        //Get our values
        $email = Input::get('email');
        $ticketid = Input::get('ticketid');
        //Start our validator
        $validator = Validator::make(array(
            'email' => $email,
            'ticketid' => $ticketid,
        ),
        array(
            'email' => 'required|email',
            'ticketid' => 'required|integer',
        ));

        if ($validator->fails()) {
            $messages = $validator->messages();
            $errorStr = '';
            //If we are logged in let's append an error to that message.
            if (Auth::user()->check())
                $errorStr .= '<div class="alert alert-error">Please use the built in ticket help desk in your VA console. You can log in at the top right of this page.</div>';
            foreach ($messages->all('<li>:message</li>') as $message)
            {
                $errorStr .= '<div class="alert alert-error">'. $message . '</div>';
            }
            echo $errorStr;
        }
        else {
            //All clear
            //Actually not quite. Let's make sure that the email and ticket # are valid and that this is marked as a guest ticket
            $ticket = Ticket::where('id', '=', $ticketid)->where('email', '=', $email)->where('vid', '=', -1)->first();
            if (empty($ticket->id)) {
                echo '<div class="alert alert-error">We were unable to locate a ticket with the details provided. Please try again and check your email for the confirmation message sent to you when the ticket was initially created</div>';
            }
            else {
                //Alright now actually all clear.
                //Prepare the status
                $status = "";
                $statusnum = 1;
                if ($ticket->status == 1) {
                    $status = '<span style="margin-right: 10px;" class="badge badge-success">OPEN</span>';
                    $statusnum = 1;
                }
                else if ($ticket->status == 0) {
                    $status = '<span style="margin-right: 10px;" class="badge badge-info">CLOSED</span>';
                    $statusnum = 0;
                }
                $response = $statusnum . '<div id="ticketreceived" data-tickethash="' . $ticket->hash . '" data-ticketid="' . $ticket->id . '" class="well"><div style="margin-top: 20px; margin-bottom: 40px;" class="row-fluid"><div style="border-bottom: 1px solid #e5e5e5;" class="span10 offset2"><h4>' . $status . ' ' . $ticket->subject . '</h4></div><div class="span2"><strong>' . $ticket->name . '</strong><br /><small>' . $ticket->created_at . '</small></div><div style="padding-top: 3px; padding-left: 3px; margin-left: 0px; border-left: 1px solid #e5e5e5;" class="span8">' . $ticket->description . '</div></div></div>';
                //Get our replies
                $replies = $ticket->replies()->get();
                foreach ($replies as $reply) {
                    //Figure out if this is a staff reply
                    if ($reply->staff == 0)
                        $author = '<strong>' . $ticket->name . '</strong>';
                    else {
                        //Alright. It is a staff reply. Let's fetch the name of the auditor
                        $author = '<span class="label label-important"><i class="fa fa-bookmark fa-fw"></i>' . ConsoleUser::getName($reply->author) . '</span>';
                    }
                    $response .= '<div style="margin-top: 15px; margin-bottom: 15px;"><hr style="border-bottom: 0;" /></div><div class="well"><div style="margin-top: 20px; margin-bottom: 40px;" class="row-fluid"><div class="span2">' . $author . '<br /><small>' . $reply->created_at . '</small></div><div style="padding-top: 3px; padding-left: 3px; margin-left: 0px; border-left: 1px solid #e5e5e5;" class="span8">' . $reply->content . '</div></div></div>';
                }
                echo $response;
            }

        }
    }

    public function post_guestsubmitticket() {
        //Get our data
        $content = Input::get('content');
        //Ticked ID
        $ticketid = Input::get('ticketid');
        //Hash
        $hash = Input::get('hash');
        //We never trust the client. Let's make sure our trust is not broken :P
        $ticket = Ticket::where('id', '=', $ticketid)->where('hash', '=', $hash)->first();
        if (!empty($ticket)) {
            //Phew alright, we trust the client now, (maybe) :)
            $reply = new TicketReply;
            $reply->tid = $ticketid;
            //Make it clear that this is not from a VA account
            $reply->author = '-1';
            $reply->content = $content;
            $reply->staff = 0;
            //Save our reply
            $reply->save();
            //Check to see if there is an auditor assigned to this ticket. If there is then let's send them an email notification advising them that there is a ticket update.
            if (!empty($ticket->assigned)) {
                $auditor = ConsoleUser::where('cid', '=', $ticket->assigned)->first();
                if (!empty($auditor->email)) {
                    $data = array('email' => $auditor->email, 'name' => $auditor->name, 'subject' => 'VASOPS Ticket #' . $ticket->id . ": " . $ticket->subject);
                    $body = "Hello " . $auditor->name . ",<br /><br />There has been an update to your assigned ticket " . $ticket->subject . " by " . $ticket->name . ". <br /><br />" . $content . "<br /><br /><br /> <strong>Do not reply to this email. If you wish to reply to this ticket, please do so through the auditor console.</strong>";
                    Mail::send('email.default', array("content" => $body), function($message) use ($data) {
                        $message->to($data['email'], $data['name'])->subject($data['subject']);
                    });
                }
            }
            //Return the new content to append to the existing div
            $response = '<div style="margin-top: 15px; margin-bottom: 15px;"><hr style="border-bottom: 0;" /></div><div class="well"><div style="margin-top: 20px; margin-bottom: 40px;" class="row-fluid"><div class="span2"><strong>' . $ticket->name . '</strong><br /><small>' . $reply->created_at . '</small></div><div style="padding-top: 3px; padding-left: 3px; margin-left: 0px; border-left: 1px solid #e5e5e5;" class="span8">' . $reply->content . '</div></div></div>';
            echo $response;
        }
    }

    public function post_guestcloseticket() {
        //Get our data
        $ticketid = Input::get('ticketid');
        $hash = Input::get('hash');
        //Fetch our ticket
        $ticket = Ticket::where('id', '=', $ticketid)->where('hash', '=', $hash)->first();
        if (!empty($ticket)) {
            //Close our ticket
            $ticket->status = 0;
            //Update our ticket
            $ticket->save();
            //Hell, let's just pull new replies in case something was updated
            $status = '<span style="margin-right: 10px;" class="badge badge-info">CLOSED</span>';
            $response = '<div id="ticketreceived" data-tickethash="' . $ticket->hash . '" data-ticketid="' . $ticket->id . '" class="well"><div style="margin-top: 20px; margin-bottom: 40px;" class="row-fluid"><div style="border-bottom: 1px solid #e5e5e5;" class="span10 offset2"><h4>' . $status . ' ' . $ticket->subject . '</h4></div><div class="span2"><strong>' . $ticket->name . '</strong><br /><small>' . $ticket->created_at . '</small></div><div style="padding-top: 3px; padding-left: 3px; margin-left: 0px; border-left: 1px solid #e5e5e5;" class="span8">' . $ticket->description . '</div></div></div>';
            //Get our replies
            $replies = $ticket->replies()->get();
            foreach ($replies as $reply) {
                //Figure out if this is a staff reply
                if ($reply->staff == 0)
                    $author = '<strong>' . $ticket->name . '</strong>';
                else {
                    //Alright. It is a staff reply. Let's fetch the name of the auditor
                    $author = '<span class="label label-important"><i class="fa fa-bookmark fa-fw"></i>' . ConsoleUser::getName($reply->author) . '</span>';
                }
                $response .= '<div style="margin-top: 15px; margin-bottom: 15px;"><hr style="border-bottom: 0;" /></div><div class="well"><div style="margin-top: 20px; margin-bottom: 40px;" class="row-fluid"><div class="span2">' . $author . '<br /><small>' . $reply->created_at . '</small></div><div style="padding-top: 3px; padding-left: 3px; margin-left: 0px; border-left: 1px solid #e5e5e5;" class="span8">' . $reply->content . '</div></div></div>';
            }
            echo $response;
        }
    }

    public function post_guestreopenticket() {
        //Get our data
        $ticketid = Input::get('ticketid');
        $hash = Input::get('hash');
        //Fetch our ticket
        $ticket = Ticket::where('id', '=', $ticketid)->where('hash', '=', $hash)->first();
        if (!empty($ticket)) {
            //Reopen our ticket
            $ticket->status = 1;
            //Update our ticket
            $ticket->save();
            //Hell, let's just pull new replies in case something was updated
            $status = '<span style="margin-right: 10px;" class="badge badge-success">OPEN</span>';
            $response = '<div id="ticketreceived" data-tickethash="' . $ticket->hash . '" data-ticketid="' . $ticket->id . '" class="well"><div style="margin-top: 20px; margin-bottom: 40px;" class="row-fluid"><div style="border-bottom: 1px solid #e5e5e5;" class="span10 offset2"><h4>' . $status . ' ' . $ticket->subject . '</h4></div><div class="span2"><strong>' . $ticket->name . '</strong><br /><small>' . $ticket->created_at . '</small></div><div style="padding-top: 3px; padding-left: 3px; margin-left: 0px; border-left: 1px solid #e5e5e5;" class="span8">' . $ticket->description . '</div></div></div>';
            //Get our replies
            $replies = $ticket->replies()->get();
            foreach ($replies as $reply) {
                //Figure out if this is a staff reply
                if ($reply->staff == 0)
                    $author = '<strong>' . $ticket->name . '</strong>';
                else {
                    //Alright. It is a staff reply. Let's fetch the name of the auditor
                    $author = '<span class="label label-important"><i class="fa fa-bookmark fa-fw"></i>' . ConsoleUser::getName($reply->author) . '</span>';
                }
                $response .= '<div style="margin-top: 15px; margin-bottom: 15px;"><hr style="border-bottom: 0;" /></div><div class="well"><div style="margin-top: 20px; margin-bottom: 40px;" class="row-fluid"><div class="span2">' . $author . '<br /><small>' . $reply->created_at . '</small></div><div style="padding-top: 3px; padding-left: 3px; margin-left: 0px; border-left: 1px solid #e5e5e5;" class="span8">' . $reply->content . '</div></div></div>';
            }
            echo $response;
        }
    }

    public function post_forgotpass() {
        $email = Input::get('email');
        $user = User::where('email', '=', $email)->first();
        if (empty($user) || empty($email))
            echo -1;
        else {
            //Generate a forgot password token
            $user->fptoken = sha1($user->name . time());
            //Update our user
            $user->save();
            //Email the user with a link to reset their password
            if (!empty($user->email)) {
                $data = array('email' => $user->email, 'name' => $user->name, 'subject' => 'VASOPS Password Reset');
                $body = "Hello " . $user->name . ",<br /><br />You have requested a password reset. Included below is a link to reset your password.<br /><br /><a href=\"". URL::to('/') . "/passwordreset/" . $user->fptoken . "\">". URL::to('/') . "/passwordreset/" . $user->fptoken . "</a><br /><br /><br /> <strong>Do not reply to this email. If you have any questions please open a ticket.</strong>";
                Mail::send('email.default', array("content" => $body), function($message) use ($data) {
                    $message->to($data['email'], $data['name'])->subject($data['subject']);
                });
                //Great, that should do it. Let's just echo 1 so the client knows that everything went well
                echo 1;
            }
        }
    }


}
