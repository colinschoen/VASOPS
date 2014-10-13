<?php
class ClickController extends BaseController {

    public function get_click($id) {
        //Are we even working with a number?
        if (!is_numeric($id)) {
            //Yikes let's get the hell out of here
            App::abort(404, 'Invalid click link');
        }

        //Now to pull our VA record or fail
        $va = User::find($id);
        if (empty($va))
            return Redirect::to('/')->with('topmessage', 'Sorry, that VA URL could not be located. Please try again and open a ticket if you continue experiencing the same issue.');
        //Great a VA exists, let's make sure to add one to their clicks if this IP hasn't added a click within the last minute.
        $existingClick = Click::where('ip', '=', Request::getClientIp())->orderBy('created_at', 'DESC')->first();
        if (!empty($existingClick)) {
            $lastClickByIp = strtotime($existingClick->created_at);
            if (time() - $lastClickByIp > 60) {
                //No click found by the same IP in the last 60 seconds thus we should add this one
                $click = new Click;
                $click->vid = $id;
                $click->ip = Request::getClientIp();
                $click->save();
            }
        }
        else {
            //No click found by that same IP at all. Let's insert into the DB
            $click = new Click;
            $click->vid = $id;
            $click->ip = Request::getClientIp();
            $click->save();
        }
        //Finally redirect the user to the VA URL
        return Redirect::to($va->url);
    }
}