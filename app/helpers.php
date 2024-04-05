<?php

    use GeminiAPI\Laravel\Facades\Gemini;
    use Illuminate\Support\Facades\Log;

    if ( !function_exists( 'userHasRole' ) ) {
        /**
         * Check if the authenticated user has a given role.
         *
         * @param  string  $role
         * @return bool
         */
        function userHasRole( string $role ) : bool
        {
            $user = auth()->user();

            return $user !== null && $user->role === $role;
        }

    }

    if ( !function_exists( 'determineTimeout' ) ) {
        /**
         * Determine the timeout based on the priority.
         *
         * @param  string  $priority
         * @return int
         */
        function determineTimeout( string $priority ) : int
        {
            return match ( $priority ) {
                'high' => 2,
                'medium' => 4,
                'low' => 8,
                default => 8,
            };
        }
    }

    if ( !function_exists( 'determinePriority' ) ) {
        /**
         * Determine the priority based on the title and description.
         *
         * @param  string  $title
         * @param  string  $description
         * @return string
         */
        function determinePriority( string $title, string $description ) : string{
            $prompt = "
    **Input:**
        * Title: $title 
        * Description: $description  
    **Context:**
        This prompt simulates an **IT ticketing system** where an employee has submitted a ticket requesting assistance.     
    **Instructions:**
        1. **Analyze Impact:**
            - Identify keywords in the title and description that indicate the severity of the issue's impact on the employee's ability to perform their job functions.
        
            **High Impact Keywords:**
                * System outage (server crash, network failure, application down)
                * Data loss or corruption
                * Financial impact (payment processing errors, billing issues)
                * Complete halt of critical business processes
                * Security breaches or potential security vulnerabilities
            
            **Medium Impact Keywords:**
                * Degraded performance (slow loading times, lag, intermittent functionality)
                * Limited access to critical data or systems
                * Inability to complete specific tasks or workflows
                * Disruptions to communication or collaboration
                * Hardware or software malfunctions impacting productivity
            
            **Low Impact Keywords:**
                * Minor formatting or display issues
                * Password reset requests
                * User interface glitches or inconveniences
                * Non-critical permission or access requests
                * Information requests or clarifications
        
        2. **Apply Priority Rules:**
            - If the title or description mentions high impact keywords, assign \"High\" priority.
            - If the title or description mentions medium impact keywords, assign \"Medium\" priority.
            - If no high or medium impact keywords are found, assign \"Low\" priority.
        
        3. **Consider Context:**
            - Use common sense to interpret the overall impact of the issue, even if specific keywords aren't present.
            - For example, a seemingly minor formatting issue might be critical for a presentation.
        
        **Output:**
        
        **As the IT ticketing system,** assign a one-word priority label (High, Medium, or Low) based on the analyzed impact on the employee's ability to work.";

            try {
                $response = Gemini::generateText( $prompt );
                return strtolower( $response );
            } catch (Exception $e) {
                Log::error( 'API error while determining priority: ' . $e->getMessage() );
                return 'low'; // Set a default priority here
            }
        }
}