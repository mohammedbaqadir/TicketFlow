<?php
    declare( strict_types = 1 );

    namespace App\Actions;


    class ShowToastNotificationAction
    {
        public function trigger( array $details ) : string
        {
            $message = $details['message'];
            $description = $details['description'] ?? '';
            $type = $details['type'] ?? 'default';
            $position = $details['position'] ?? 'top-right';
            $html = $details['html'] ?? '';

            // Return a script that will trigger the toast
            return "
            <script>
                window.toast('{$message}', {
                    description: '{$description}',
                    type: '{$type}',
                    position: '{$position}',
                    html: '{$html}'
                });
            </script>
        ";
        }

    }