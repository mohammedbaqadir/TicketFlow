<div x-data="search()" x-cloak x-on:open-search.window="open" class="relative">
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
      errorMessage: '',
      currentPage: 1,
      lastPage: 1,
      totalResults: 0,
      perPage: 15,
      init () {
        this.isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

        this.$watch('isOpen', (value) => {
          document.body.style.overflow = value ? 'hidden' : '';
        });
      },
      open () {
        this.isOpen = true;
      },
      close () {
        this.isOpen = false;
        this.query = '';
        this.results = [];
        this.errorMessage = '';
        this.currentPage = 1;
      },
      search () {
        if (this.query.trim().length < 3) {
          this.results = [];
          this.errorMessage = 'Please enter at least 3 characters to search';
          return;
        }

        this.isLoading = true;
        fetch(`/search?query=${encodeURIComponent(this.query)}&page=${this.currentPage}&per_page=${this.perPage}`)
          .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
          })
          .then(data => {
            this.results = data.data;
            this.currentPage = data.current_page;
            this.lastPage = data.last_page;
            this.totalResults = data.total;
            this.errorMessage = this.results.length === 0 ? 'No results found for "' + this.query + '"' : '';
          })
          .catch(error => {
            console.error('Error fetching search results:', error);
            this.results = [];
            this.errorMessage = 'Error fetching search results. Please try again.';
          })
          .finally(() => {
            this.isLoading = false;
          });
      },
      loadMore () {
        if (this.currentPage < this.lastPage) {
          this.currentPage++;
          this.search();
        }
      },
      formatDate (dateString) {
        return new Date(dateString).toLocaleDateString();
      }
    };
  }
</script>