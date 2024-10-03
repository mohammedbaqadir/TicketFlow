![TicketFlow Logo](/public/images/logo-banner-dark-removebg.png "TicketFlow Logo")


<div align="center">


[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=Plastic&logo=laravel&logoColor=white)](https://laravel.com)
![Livewire](https://img.shields.io/badge/livewire-%234e56a6.svg?style=Plastic&logo=livewire&logoColor=white)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=Plastic&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
![Alpine.js](https://img.shields.io/badge/alpinejs-white.svg?style=Plastic&logo=alpinedotjs&logoColor=%238BC0D0)
![Vite](https://img.shields.io/badge/vite-%23646CFF.svg?style=Plastic&logo=vite&logoColor=white)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=Plastic)](https://opensource.org/licenses/MIT)

Streamline your IT support with AI-powered ticket management 🚀

[Live Demo (coming soon)](#) |
[Documentation (coming soon)](#) |
[Report Bug](https://github.com/mohammedbaqadir/ticketflow/issues) |
[Request Feature](https://github.com/mohammedbaqadir/ticketflow/issues)

</div>

## 🌟 Key Features

[//]: # "Placeholder for demo GIF: A short animation showcasing the ticket creation, AI prioritization, and resolution process"

- 🧠 **AI-Driven Prioritization**: Harness the power of Gemini API for smart ticket sorting
- 🔍 **Lightning-Fast Search**: Find tickets in milliseconds with Meilisearch
- ⚡ **Optimized Performance**: Handle heavy loads with ease using caching and job queues
- 🔐 **Rock-Solid Security**: Multi-level rate limiting and role-based access keep your data safe
- 🎨 **Sleek UI**: Intuitive interfaces built with TailwindCSS and AlpineJS
- 🎥 **Live Video Support**: Connect instantly via Jitsi Meet integration

## 🛠️ Tech Stack

<div align="center">
Laravel 11 - FilamentPHP 3.2 - Livewire 3.5 - AlpineJS 3.4 - TailwindCSS 3.4 - Pest 3.2

</div>

## 🚀 **Installation Guide**

### **Live Demo via Codespace**

Run the application directly in GitHub Codespaces with zero setup on your local machine.

#### **Steps:**

1. **Open in GitHub Codespaces:**
    - Navigate to the repository on GitHub.
    - Click on the **"Code"** button and select **"Open with Codespaces"**.
    - If no Codespace exists, create one.

2. **Wait for the environment to build:**
    - The environment will automatically install the required PHP, Node.js, and MySQL dependencies using the
      `devcontainer.json` and `Dockerfile`.

3. **Run App Setup Command:**
    - After the environment is ready, run the custom `app:setup` command to set up the database, cache, and Meilisearch.
    - Inside the Codespace terminal:
      ```sh
      php artisan app:setup
      ```

4. **Start the Application:**
    - Once setup is complete, start the services:
      ```sh
      php artisan app:up
      ```

5. **Access the App:**
    - Visit `http://localhost:8000` to see TicketFlow in action inside your Codespace. 🎉

---

### **Local Installation**

For running the TicketFlow application on your local machine, follow these steps.

#### **Prerequisites:**

Before you begin, ensure you have the following installed:

- PHP >= 8.2 🐘
- Composer 🎼
- Node.js and npm 📦
- MySQL 🐬
- Meilisearch (if using it for search functionality)

#### **Steps:**

1. **Clone the repository:**

   ```sh
   git clone https://github.com/mohammedbaqadir/ticketflow.git
   cd ticketflow
   ```

2. **Install PHP dependencies:**

   ```sh
   composer install
   ```

3. **Install JavaScript dependencies:**

   ```sh
   npm install
   ```

4. **Set up your environment:**

   ```sh
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database in `.env`:**

    - Update your `.env` file to reflect your local MySQL settings (DB credentials).

6. **Run migrations:**

   ```sh
   php artisan migrate
   ```

7. **Build assets and start the server:**

   ```sh
   npm run build
   php artisan serve
   ```

   Now, visit `http://localhost:8000` to see TicketFlow in action. 🎉

---

## 📚 Configuration Deep Dive

### 🔍 Meilisearch

1. Install Meilisearch
2. Update `.env`:
   ```
   SCOUT_DRIVER=meilisearch
   MEILISEARCH_HOST=http://localhost:7700
   MEILISEARCH_KEY=your_master_key
   ```

### 🧠 Gemini API

1. Get your API key from Google
2. Add to `.env`:
   ```
   GEMINI_API_KEY=your_gemini_api_key
   ```

### 🎥 Jitsi Meet

1. Set up Jitsi Meet
2. Add to `.env`:
   ```
   JITSI_VPAAS_MAGIC_COOKIE=your_magic_cookie
   ```

## 🎭 User Roles

👤 **Employee**: Create and track personal tickets
👨‍💼 **Agent**: Manage all tickets, self-assign
👑 **Admin**: Full access, including user management

## 🎬 Usage Scenarios

### Creating a Ticket

1. 🔑 Log in as Employee
2. 📝 Click "Create Ticket"
3. 🖊️ Fill details and submit

### Managing Tickets

1. 🔑 Log in as Agent/Admin
2. 📊 View ticket dashboard
3. ✅ Assign, update, resolve

## 🧪 Testing

Run tests with style:

```sh
php artisan test
```

Watch those green checkmarks fly! ✅✅✅

## 🗺️ Roadmap

I'm constantly working to improve TicketFlow and add exciting new features. Here's what's on the horizon:

- 📅 **Ticket Timelines**: Capture and display important events throughout a ticket's lifecycle, providing a clear
  historical view of each issue's progression.

- 📊 **Kanban Board Integration**: Implement a Kanban-style board for both employees and agents, offering a visual and
  intuitive way to manage tickets and workflow.

- ✅ **Enhanced Form Validation**: Add robust front-end validation to all forms, improving user experience and reducing
  errors before data submission.

- 🛡️ **Content Security Policy**: Implement a comprehensive Content Security Policy to further bolster the application's
  security posture and protect against various types of attacks.

## 📜 License

Distributed under the MIT License. See [LICENSE](LICENSE.md) for more information.

## 💖 Acknowledgments

- 🙌 Laravel community
- ☕ Caffeine suppliers