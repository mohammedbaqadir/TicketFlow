@props(['ticket'])
<div class='grid grid-cols-1 gap-6 p-6 bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-gray-700 dark:to-gray-500 rounded-lg shadow-lg mt-8'>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6 mb-6">

        <div x-data="{ showToast: false, message: '' }"
             @toast-show.window="showToast = true; message = $event.detail.message; setTimeout(() => showToast = false, 5000)">
            <div x-show="showToast" class="toast-notification">
                <p x-text="message"></p>
            </div>

            <a href="#"
               id="startMeetingBtn"
               class="group relative inline-flex items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-purple-600 to-blue-500 p-0.5 text-sm font-medium text-gray-900 hover:text-white focus:outline-none focus:ring-4 focus:ring-purple-200 group-hover:from-purple-600 group-hover:to-blue-500 dark:text-white dark:focus:ring-purple-800">
                <span class="relative rounded-md bg-white px-5 py-2.5 transition-all duration-75 ease-in group-hover:bg-opacity-0 dark:bg-gray-900">
                    <span class="relative inline-flex items-center">
                        <svg class="mr-2 h-4 w-4"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        {{ $ticket->meeting_room ? 'Join' : 'Start' }} Video Chat
                    </span>
                </span>
            </a>

            <div id="loadingIndicator" class="hidden mt-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                <p class="mt-2 text-gray-700 dark:text-gray-300">Initializing video chat...</p>
            </div>
            <div id="jitsiContainer" class="hidden w-full h-[600px] mt-4"></div>
        </div>

        @can('answer', $ticket)
            <a href="{{route('tickets.answers.create', $ticket)}}"
               class="order-first relative inline-flex items-center justify-start py-2 sm:py-3 pl-3 sm:pl-4 pr-8 sm:pr-12 overflow-hidden
                   font-semibold text-sm sm:text-base
                   text-purple-700 dark:text-purple-200 transition-all duration-150 ease-in-out rounded hover:pl-6 sm:hover:pl-10 hover:pr-5 sm:hover:pr-6
                   bg-white dark:bg-gray-800 shadow-md hover:shadow-lg group">
                <span class="absolute bottom-0 left-0 w-full h-1 transition-all duration-150 ease-in-out
                      bg-gradient-to-r from-blue-400 to-purple-400 dark:from-blue-300 dark:to-purple-300
                      group-hover:h-full"></span>
                <span class="absolute right-0 pr-3 sm:pr-4 duration-200 ease-out group-hover:translate-x-12">
                    <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 text-purple-500 dark:text-purple-300" />
                </span>
                <span class="absolute left-0 pl-2 -translate-x-12 group-hover:translate-x-0 ease-out duration-200">
                    <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 dark:text-purple-400 group-hover:text-white" />
                </span>
                <span class="relative w-full text-left transition-colors duration-200 ease-in-out
                      group-hover:text-white">Answer</span>
            </a>
        @endcan

    </div>

    <div class="pt-4">
        @foreach($ticket->answers as $answer)
            <x-tickets.show.answer :answer="$answer" />
        @endforeach
    </div>
</div>
@php
    $magicCookie = config('services.jitsi.vpaas_magic_cookie');

@endphp

@pushonce('scripts')
    <script src="https://8x8.vc/{{ $magicCookie }}/external_api.js" async></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        let api;
        const startMeetingBtn = document.getElementById('startMeetingBtn');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const jitsiContainer = document.getElementById('jitsiContainer');

        function toggleVisibility (element, isVisible) {
          element.style.display = isVisible ? 'block' : 'none';
        }

        function updateButtonText (button, text) {
          button.innerHTML = `<span class="mr-2">${text}</span>`;
        }

        function showError (message) {
          alert(message);
          toggleVisibility(loadingIndicator, false);
          updateButtonText(startMeetingBtn, 'Live Video Chat');
          startMeetingBtn.disabled = false;
        }

        function initJitsiMeet (roomName, ticketId, assigneeName, requestorName) {
          const domain = '8x8.vc';
          const options = {
            roomName,
            width: '100%',
            height: '100%',
            parentNode: jitsiContainer,
            userInfo: {
              email: '{{ Auth::user()->email }}',
              displayName: '{{ Auth::user()->name }}',
            },
            configOverwrite: {
              prejoinPageEnabled: false,
              disableDeepLinking: true,
            },
            interfaceConfigOverwrite: {
              TOOLBAR_BUTTONS: ['microphone', 'camera', 'desktop', 'fullscreen', 'hangup', 'chat'],
              SHOW_JITSI_WATERMARK: false,
              MOBILE_APP_PROMO: false,
            },
          };

          toggleVisibility(loadingIndicator, true);
          api = new JitsiMeetExternalAPI(domain, options);

          api.addEventListener('videoConferenceJoined', (event) => onConferenceJoined(event, ticketId));
          api.addEventListener('participantJoined', (event) => onParticipantJoined(event, assigneeName, requestorName, ticketId));
          api.addEventListener('videoConferenceLeft', cleanupJitsiMeet);
        }

        function onConferenceJoined (event, ticketId) {
          toggleVisibility(loadingIndicator, false);
          toggleVisibility(jitsiContainer, true);

          axios.post('/meeting/joined', {
            ticketId,
            meetingLink: window.location.href,
            username: event.displayName, // Current user who joined the meeting
          }, {
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
            }
          })
            .then(response => {
              const {waitingFor} = response.data;

              // Ensure the toast is shown only to the user who hasn't joined yet
              if (waitingFor && waitingFor === '{{ Auth::user()->name }}') {
                // Show the toast notification to the user who is waiting
                window.toast(`${waitingFor} is waiting for you in the meeting room`, {
                  description: 'Please join the meeting now.',
                  type: 'info',
                });
              }
            })
            .catch(error => console.error('Error:', error));
        }

        function onParticipantJoined (event, assigneeName, requestorName, ticketId) {
          if (event.displayName === assigneeName || event.displayName === requestorName) {
            api.executeCommand('displayName', `${event.displayName} (Ticket ${ticketId})`);
          }
        }

        function cleanupJitsiMeet () {
          if (api) {
            api.dispose();
            api = null;

            toggleVisibility(jitsiContainer, false);
            toggleVisibility(loadingIndicator, false);

            startMeetingBtn.disabled = false;
            updateButtonText(startMeetingBtn, 'Join/Start Video Chat');
          }
        }

        startMeetingBtn.addEventListener('click', function () {
          this.disabled = true;
          updateButtonText(this, 'Initializing...');

          fetch(`/tickets/{{ $ticket->id }}/meeting`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
          })
            .then(response => response.ok ? response.json() : Promise.reject('Failed to initialize video chat.'))
            .then(data => {
              toggleVisibility(this, false);
              initJitsiMeet(data.roomName, data.ticketId, data.assigneeName, data.requestorName);
            })
            .catch(error => {
              console.error('Error:', error);
              showError('Failed to initialize video chat. Please try again.');
            });
        });

        window.addEventListener('beforeunload', cleanupJitsiMeet);
      });
    </script>

@endpushonce