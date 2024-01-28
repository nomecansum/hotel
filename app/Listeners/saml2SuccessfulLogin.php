<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Auth;
use App\User;

class saml2SuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        

        $messageId = $event->getAuth()->getLastMessageId();
    
        // your own code preventing reuse of a $messageId to stop replay attacks
        $samlUser = $event->getSaml2User();
        //dd($samlUser); 

        $email=$samlUser->getUserId();
        
        //A ver si existe
        $u=User::where(['email' => $email])->first();

        if($u!=null){
             // Login a user.
            Auth::login($u);
            //Y ahora toda la parafernalia que le acompaña a la autenticación
            event(new SuccessfulLogin($u));
            
            abort(redirect('/index'));

        } else {
            abort(redirect('/login')->withErrors(["email"=>"ERROR: Usuario no registrado, debe darse de alta primero en spotlinker"]));
        }

    }
}
