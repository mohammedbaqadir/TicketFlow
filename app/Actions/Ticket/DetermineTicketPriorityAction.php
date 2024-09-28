<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Actions\API\GenerateGeminiTextAction;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Validation\ValidationException;

    class DetermineTicketPriorityAction
    {
        protected GenerateGeminiTextAction $generateGeminiTextAction;

        public function __construct( GenerateGeminiTextAction $generateGeminiTextAction )
        {
            $this->generateGeminiTextAction = $generateGeminiTextAction;
        }

        public function execute( Ticket $ticket ) : array
        {
            $priority = $this->determinePriority( $ticket->title, $ticket->description );
            $timeoutAt = now()->addHours( $this->determineTimeout( $priority ) );

            return [
                'priority' => $priority,
                'timeout_at' => $timeoutAt,
            ];
        }


        private function determinePriority( string $title, string $description ) : string
        {
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
            $priority = 'low';

            try {
                $response = $this->generateGeminiTextAction->execute( $prompt );
                $priority = strtolower( preg_replace( '/[^a-zA-Z]/', '', $response ) );
            } catch (ValidationException $e) {
                Log::warning( 'Rate limit exceeded for Gemini API: ' . $e->getMessage() );
            } catch (\Exception $e) {
                Log::error( 'API error while determining priority: ' . $e->getMessage() );
            }

            return $priority;
        }

        private function determineTimeout( string $priority ) : int
        {
            return config( "ticket.priority_timeout.{$priority}", 8 );
        }

    }