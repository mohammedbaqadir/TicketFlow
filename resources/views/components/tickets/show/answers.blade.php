@props(['ticket'])
<div class='grid grid-cols-1 gap-6 p-6 bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-gray-700 dark:to-gray-500 rounded-lg shadow-lg mt-8'>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
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
        <a href="#"
           id="startMeetingBtn"
           class="relative inline-flex items-center justify-start px-4 sm:px-6 py-2 sm:py-3 overflow-hidden font-medium text-sm sm:text-base transition-all bg-white dark:bg-gray-800 rounded shadow-md hover:shadow-lg group">
            <span class="w-32 sm:w-48 h-32 sm:h-48 rounded rotate-[-40deg] bg-purple-500 dark:bg-purple-400 absolute
            bottom-0 left-0 -translate-x-full ease-out duration-500 transition-all translate-y-full mb-9 ml-9 group-hover:ml-0 group-hover:mb-32 group-hover:translate-x-0"></span>
            <span class="relative w-full text-left text-purple-700 dark:text-purple-200 transition-colors duration-300 ease-in-out group-hover:text-white">
                {{ $ticket->meeting_room ? 'Join' : 'Start' }} Video Chat
            </span>
        </a>
    </div>

    <div id="loadingIndicator" class="hidden mt-4 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        <p class="mt-2 text-gray-700 dark:text-gray-300">Initializing video chat...</p>
    </div>
    <div id="jitsiContainer" class="hidden w-full h-[600px] mt-4"></div>

    @foreach($ticket->answers as $answer)
        <x-tickets.show.answer :answer="$answer" />
    @endforeach
</div>

@php
    $magicCookie = config('services.jitsi.vpaas_magic_cookie');
@endphp

@push('scripts')
    <script src="https://8x8.vc/{{ $magicCookie }}/external_api.js" async></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        let api;
        const startMeetingBtn = document.getElementById('startMeetingBtn');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const jitsiContainer = document.getElementById('jitsiContainer');

        function showError (message) {
          alert(message);
          startMeetingBtn.disabled = false;
          startMeetingBtn.innerHTML = 'Live Video Chat <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>';
          loadingIndicator.style.display = 'none';
        }

        function initJitsiMeet (roomName, ticketId, assigneeName, requestorName) {
          const domain = '8x8.vc';
          const options = {
            roomName: roomName,
            width: '100%',
            height: '100%',
            parentNode: jitsiContainer,
            userInfo: {
              email: '{{ Auth::user()->email }}',
              displayName: '{{ Auth::user()->name }}'
            },
            configOverwrite: {
              prejoinPageEnabled: false,
              disableDeepLinking: true
            },
            interfaceConfigOverwrite: {
              TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
                'e2ee'
              ],
              SHOW_JITSI_WATERMARK: false,
              SHOW_WATERMARK_FOR_GUESTS: false,
              SHOW_BRAND_WATERMARK: false,
              BRAND_WATERMARK_LINK: '',
              SHOW_POWERED_BY: false,
              SHOW_PROMOTIONAL_CLOSE_PAGE: false,
              MOBILE_APP_PROMO: false,
            },
          };

          loadingIndicator.style.display = 'block';

          api = new JitsiMeetExternalAPI(domain, options);

          api.addEventListener('videoConferenceJoined', () => {
            loadingIndicator.style.display = 'none';
            jitsiContainer.style.display = 'block';
          });

          api.addEventListener('participantJoined', function (event) {
            if (event.displayName === assigneeName || event.displayName === requestorName) {
              api.executeCommand('displayName', event.displayName + ' (Ticket ' + ticketId + ')');
            }
          });

          api.addEventListener('videoConferenceLeft', () => {
            cleanupJitsiMeet();
          });
        }

        function cleanupJitsiMeet () {
          if (api) {
            api.dispose();
            api = null;
            jitsiContainer.style.display = 'none';

            // Reset the button to its initial state
            startMeetingBtn.disabled = false;
            startMeetingBtn.innerHTML = '<span class="mr-2">{{ $ticket->meeting_room ? 'Join' : 'Start' }} Video Chat</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>';

            loadingIndicator.style.display = 'none';
            startMeetingBtn.style.display = 'inline-flex';
          }
        }

        startMeetingBtn.addEventListener('click', function () {
          this.disabled = true;
          this.innerHTML = '<span class="mr-2">Initializing...</span><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

          fetch(`/tickets/{{ $ticket->id }}/meeting`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
          })
            .then(response => {
              if (!response.ok) {
                throw new Error('Network response was not ok');
              }
              return response.json();
            })
            .then(data => {
              this.style.display = 'none';
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
@endpush