<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Auth;
use Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            if ($request->wantsJson()){ //Peticion de API
                return response()->json([
                    "response" => "ERROR",
                    "message" => "Method [".\Request::getMethod()."]  not allowed for [".\Request::getRequestUri()."]",
                    "TS" => \Carbon\Carbon::now()->format('Y-m-d h:i:s'),
                    "e"=> $exception
                ]);
            }else{
                $mensaje_largo="Method [".\Request::getMethod()."] not allowed for [".\Request::getRequestUri()."]";
                return response()->view('errors/method',compact('mensaje_largo'));
                //return Response::view('errors.index');

            }
        }
		if((Auth::user()) && (!$exception instanceof ValidationException) && (!$exception instanceof TokenMismatchException) && (!$exception instanceof \Illuminate\Session\TokenMismatchException) && (!config('app.debug'))) //que no sea de validacion de datos
		{
			try {
	    		//mandamos un email con datos
	    		enviar_email_error($request, config('mail.from.address'), config('mail.error'), config('mail.error'), "Error en QrClean PROD - catch", "emails.mail_error_catch",  $exception); //error
    
	    	}
	    	catch(\Exception $e){
	    		Log::error("Error al enviar el email con el catch");
	    	}
		}
       
        return parent::render($request, $exception);
    }

}
