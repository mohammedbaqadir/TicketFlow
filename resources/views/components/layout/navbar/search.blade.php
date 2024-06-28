<div x-data="search()" x-cloak class="relative" >
    <x-layout.navbar.search.button />
    <x-layout.navbar.search.modal />
</div>


<script>
  function search () {
    return {
      isOpen: false,
      query: '',
      results: [],
      isLoading: false,
      isDarkMode: false,
      init () {
        this.isDarkMode = document.documentElement.classList.contains('dark');
        this.$watch('isOpen', (value) => {
          document.body.style.overflow = value ? 'hidden' : '';
        });
        this.$on('open-search', this.open);
      },
      open () {
        this.isOpen = true;
      },
      close () {
        this.isOpen = false;
        this.query = '';
        this.results = [];
      },
      search () {
        if (this.query.length === 0) {
          this.results = [];
          return;
        }

        this.isLoading = true;
        fetch(`/search?query=${encodeURIComponent(this.query)}`)
          .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
          })
          .then(data => {
            this.results = data;
          })
          .catch(error => {
            console.error('Error fetching search results:', error);
            this.results = [];
          })
          .finally(() => {
            this.isLoading = false;
          });
      },
      formatDate (dateString) {
        return new Date(dateString).toLocaleDateString();
      }
    };
  }
</script>