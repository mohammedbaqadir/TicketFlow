@props(['timeoutAt', 'ticketStatus', 'compact' => false])

@php
    use App\Helpers\AuthHelper;
    $shouldRender = AuthHelper::userHasRole('agent') && $ticketStatus !== 'resolved';
    $compactClasses = $compact ? 'w-8 h-8 text-sm' : 'text-base sm:text-lg lg:text-xl';
@endphp

@if($shouldRender)
    <div
            x-cloak
            x-data="countdownTimer('{{ $timeoutAt }}')"
            x-init="init()"
            x-show="showElement"
            class="mt-4 flex justify-center items-center"
    >
        <div x-show="!isTimedOut"
             class="flex flex-row bg-white/40 dark:bg-gray-900/40 backdrop-blur-lg rounded-xl overflow-hidden p-2 sm:p-4 shadow-xl {{ $compact ? 'space-x-1' : 'space-x-2 sm:space-x-4' }}">
            <!-- Days -->
            <div class="flex flex-col items-center {{ $compactClasses }}" x-show="timeValues.days > 0">
                <div class="flex items-center justify-center text-gray-900 dark:text-gray-200">
                    <div x-text="timeValues.days" class="relative"></div>
                </div>
                <div class="text-center text-gray-900 dark:text-gray-200">
                    <span>{{ $compact ? 'D' : 'Days' }}</span>
                </div>
            </div>

            <!-- Separator -->
            <div class="text-gray-900 dark:text-gray-200 {{ $compactClasses }}" x-show="timeValues.days > 0">:</div>

            <!-- Hours -->
            <div class="flex flex-col items-center {{ $compactClasses }}"
                 x-show="timeValues.hours > 0 || timeValues.days > 0">
                <div class="flex items-center justify-center text-gray-900 dark:text-gray-200">
                    <div x-text="timeValues.hours" class="relative"></div>
                </div>
                <div class="text-center text-gray-900 dark:text-gray-200">
                    <span>{{ $compact ? 'H' : 'Hours' }}</span>
                </div>
            </div>

            <!-- Separator -->
            <div class="text-gray-900 dark:text-gray-200 {{ $compactClasses }}"
                 x-show="timeValues.hours > 0 || timeValues.days > 0">:
            </div>

            <!-- Minutes -->
            <div class="flex flex-col items-center {{ $compactClasses }}">
                <div class="flex items-center justify-center text-gray-900 dark:text-gray-200">
                    <div x-text="timeValues.minutes" class="relative"></div>
                </div>
                <div class="text-center text-gray-900 dark:text-gray-200">
                    <span>{{ $compact ? 'M' : 'Minutes' }}</span>
                </div>
            </div>

            <!-- Separator -->
            <div class="text-gray-900 dark:text-gray-200 {{ $compactClasses }}">:</div>

            <!-- Seconds -->
            <div class="flex flex-col items-center {{ $compactClasses }}">
                <div class="flex items-center justify-center text-gray-900 dark:text-gray-200">
                    <div x-text="timeValues.seconds" class="relative"></div>
                </div>
                <div class="text-center text-gray-900 dark:text-gray-200">
                    <span>{{ $compact ? 'S' : 'Seconds' }}</span>
                </div>
            </div>
        </div>

        <!-- Timed Out Message -->
        <div x-show="isTimedOut"
             class="flex items-center justify-center bg-white/40 dark:bg-gray-900/40 backdrop-blur-lg rounded-xl overflow-hidden p-2 sm:p-4 shadow-xl space-x-2">

            <x-heroicon-c-clock class="w-5 h-5 text-red-600 dark:text-red-500" />
            <span class="text-l font-bold text-red-600 dark:text-red-500">
        TIMED OUT
    </span>
        </div>

    </div>

    <script>
      function countdownTimer (timeoutAt) {
        return {
          timeUnits: ['days', 'hours', 'minutes', 'seconds'],
          timeValues: {},
          isTimedOut: false,
          showElement: true,

          init () {
            this.calculateCountdown();
            setInterval(() => this.calculateCountdown(), 1000);
          },

          calculateCountdown () {
            const now = new Date().getTime();
            const timeout = new Date(timeoutAt).getTime();
            const difference = timeout - now;

            if (difference <= 0) {
              this.isTimedOut = true;
              return;
            }

            const seconds = Math.floor(difference / 1000);
            this.timeValues = {
              days: Math.floor(seconds / 86400),
              hours: Math.floor((seconds % 86400) / 3600),
              minutes: Math.floor((seconds % 3600) / 60),
              seconds: seconds % 60
            };

            this.timeUnits.forEach(unit => {
              this.timeValues[unit] = this.padZero(this.timeValues[unit]);
            });
          },

          padZero (value) {
            return value < 10 ? `0${value}` : value.toString();
          }
        };
      }
    </script>
@endif