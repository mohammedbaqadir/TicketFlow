<?php
    declare( strict_types = 1 );

    namespace App\Actions\Ticket;

    use App\Actions\AI\PromptLLMAction;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\Log;

    class DetermineTicketPriorityAction
    {
        protected PromptLLMAction $prompt_LLM_action;

        public function __construct( PromptLLMAction $prompt_LLM_action )
        {
            $this->prompt_LLM_action = $prompt_LLM_action;
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
You are an IT ticket prioritization system. Your only job is to analyze the ticket and output a single word priority level.

**Priority Classification Rules:**
1. HIGH Priority:
   - System outages or failures
   - Security incidents
   - Data loss/corruption
   - Complete work stoppage
   - Financial system disruptions

2. MEDIUM Priority:
   - Performance degradation
   - Limited system access
   - Workflow disruptions
   - Hardware/software malfunctions
   - Team-level impacts

3. LOW Priority:
   - Individual user issues
   - UI/display problems
   - Password resets
   - Information requests
   - Non-urgent improvements

**Required Output:**
Respond with exactly one word: high, medium, or low

Example correct responses:
high
medium
low

Example incorrect responses:
Priority: high
The priority is medium
low priority";
            $priority = 'low';

            try {
                $response = $this->prompt_LLM_action->execute( $prompt );
                $priority = strtolower( preg_replace( '/[^a-zA-Z]/', '', $response['text'] ?? '' ) );
            } catch (\Exception $e) {
                Log::error( 'API error while determining priority: ' . $e->getMessage() );
            }

            return $priority;
        }

        private function determineTimeout( string $priority ) : int
        {
            return config( "enums.priority_timeout.{$priority}", 8 );
        }

    }