<?php
class ConsoleController extends BaseController {

    public function get_login()

    {
        return View::make('console.login');
    }

    public function post_login() {;
        $cid = Input::get('cid');
        $password = Input::get('password');

        $validator = Validator::make(
            array(
                'Cid' => $cid,
                'Password' => $password,
            ),
            array(
                'Cid' => 'required|integer',
                'Password' => 'required',
            ),
            array (
                'Cid.required' => 'Please enter your VATSIM CID.',
                'Cid.integer' => 'Invalid CID.',
                'Password.required' => 'You must enter a password.',
            )
        );

        if ($validator->fails())
        {
            // The given data did not pass validation
            $messages = $validator->messages();
            $errorStr = '';
            $count = count($messages);
            $i = 0;
            foreach ($messages->all(':message') as $message)
            {
                $i++;
                $errorStr .= '<span>' . $message . '</span>';
                if ($i != $count) {
                    $errorStr .= '<br /><hr />';
                }
            }
            return Redirect::route('consolelogin')->with('message', $errorStr)->with('cid', $cid);
        }
        else {

            $consoleuser = ConsoleUser::where('cid', '=', $cid)->first();
            if (!empty($consoleuser)) {
                //User with the provided CID exists. Now let's run the password
                if (Hash::check($password, $consoleuser->password)) {
                    //Credentials were valid. Let's login the user
                    Auth::consoleuser()->loginUsingId($cid);
                    //We will now redirect them to the console index
                    return Redirect::route('console');
                }
                else {
                    //Bad Password
                    return Redirect::route('consolelogin')->with('message', 'Invalid Password. ')->with('cid', $cid);
                }
            }
            else {
                //Bad CID
                return Redirect::route('consolelogin')->with('message', 'Invalid CID. ')->with('cid', $cid);

            }

        }

    }

    public function get_index() {
        return View::make('console.index');
    }
}