@props(['timeoutAt', 'ticketStatus'])

@php
    use App\Helpers\AuthHelper;
    $shouldRender = AuthHelper::userHasRole('agent') && $ticketStatus !== 'closed';
@endphp

@if($shouldRender)
    <div
            x-data="countdownTimer('{{ $timeoutAt }}')"
            x-init="init()"
            x-show="showElement"
            class="mt-4 flex justify-center items-center"
    >
        <div class="flex flex-col items-center justify-end h-full">
            <div x-show="!isTimedOut" class="flex items-center space-x-2 mb-2">
                <span class="text-sm text-gray-700 dark:text-gray-300">Times Out in:</span>
                <div class="flex items-center">
                    <template x-for="unit in timeUnits" :key="unit">
                        <template x-if="showUnit(unit)">
                            <div class="flex items-center">
                                <div class="flex flex-col items-center">
                                    <span x-text="timeValues[unit]"
                                          class="text-2xl font-bold text-gray-900 dark:text-white"></span>
                                </div>
                                <span class="text-xl font-bold mx-1 text-gray-700 dark:text-gray-300"
                                      x-show="timeUnits.indexOf(unit) < timeUnits.length - 1">:</span>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
            <div x-show="isTimedOut" class="relative inline-block mt-4 text-center">
        <span class="relative text-xl font-bold text-red-600 dark:text-red-500">
            TIMED OUT
            <span class="absolute inset-0 z-[-1] bg-red-200 dark:bg-red-700 opacity-70 skew-y-[-3deg]"></span>
        </span>
            </div>
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
          },

          showUnit (unit) {
            const index = this.timeUnits.indexOf(unit);
            return this.timeUnits.slice(0, index + 1).some(u => parseInt(this.timeValues[u]) > 0);
          }
        };
      }
    </script>
@endif