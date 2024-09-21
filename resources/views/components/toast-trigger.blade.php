@if (session('toast'))
    <script>
      let toastData = @json(session('toast'));

      fetch("{{ route('trigger-toast') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({toastData: toastData})
      })
        .then(response => response.text())
        .then(html => {
          // Insert the HTML into the DOM
          let scriptElement = document.createElement('div');
          scriptElement.innerHTML = html;

          // Find and execute the script tag within the response
          let scriptTag = scriptElement.querySelector('script');
          if (scriptTag) {
            eval(scriptTag.innerText);  // Execute the JavaScript inside the <script> tag
          }
        })
        .catch(error => console.error('Error during fetch:', error));
    </script>
@endif