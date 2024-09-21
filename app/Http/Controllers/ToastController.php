<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Actions\ShowToastNotificationAction;
    use Illuminate\Http\Request;
    use JsonException;

    class ToastController extends Controller
    {
        protected ShowToastNotificationAction $showToastNotificationAction;

        public function __construct( ShowToastNotificationAction $showToastNotificationAction )
        {
            $this->showToastNotificationAction = $showToastNotificationAction;
        }

        /**
         * @throws JsonException
         */
        public function triggerToast( Request $request ) : string
        {
            $toastData = $request->input( 'toastData' );

            return $this->showToastNotificationAction->trigger( $toastData );
        }


    }