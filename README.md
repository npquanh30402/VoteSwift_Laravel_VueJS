# How to Run the Project

This guide will help you set up and run this project on your local environment. Please follow these steps in order to
ensure the application runs smoothly.

### Showcase
- [Showcase (Link to Google Drive)](https://drive.google.com/drive/folders/1szGuApSjNBgnvLrRwdUEykvKj3YAgWkz?usp=drive_link)

### Prerequisites

Before you begin, make sure you have the following tools installed on your system:

- **PHP:** [Download and install PHP](https://www.php.net/)
- **Composer:** [Download and install Composer](https://getcomposer.org/)
- **Node.js (includes npm):** [Download and install Node.js](https://nodejs.org/)
- **Git:** [Download and install Git](https://git-scm.com/)

### Installation

Follow these steps to get your development environment running:

1. **Clone the repository**
   Use Git to clone the repository to your local machine:
   ```bash
   git clone https://github.com/your-username/my-project.git
   cd my-project
   ```

2. **Install PHP dependencies**
   Run Composer to install the necessary PHP dependencies:
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   Use npm to install the required Node.js packages:
   ```bash
   npm install
   ```

### Configuration

1. **Configure PHP**
   Edit your `php.ini` file to enable necessary extensions for the project. You can usually find this file in your PHP
   installation directory. Uncomment (remove the `;` prefix) the following lines in the `php.ini` file:

```bash
   extension=gd
   extension=mysqli
   extension=pdo_mysql
   extension=pdo_sqlite
   extension=sqlite3
   extension=zip
   ```

2. **Environment Setup**
   Copy the `.env.example` file to create a `.env` file. This file will contain all the environment-specific settings:
   ```bash
   cp .env.example .env

### Additional Configuration

Before running the application, you need to perform some additional setup steps:

1. Generate Application Key:
   Run the following command to generate a key for your application:

    ```bash
        php artisan key:generate
    ```

2. Create Symbolic Link for Storage:
   Create a symbolic link from public/storage to storage/app/public using the following command:

    ```bash
        php artisan storage:link
    ```

3. Run Database Migrations:
   Execute database migrations to create necessary database tables:

    ```bash
        php artisan migrate
    ```

4. Build the frontend assets:
   To build the frontend assets for the application, use the following command:

    ```bash
        npm run build
    ```

### Running the Application

To start the application, you will need to execute several commands. Open your terminal and navigate to your project
directory, then:

1. Start the Laravel server
   This will start the PHP development server:
   ```bash
   php artisan serve
   ```

2. Start the WebSocket server
   This command launches the WebSocket server for real-time event broadcasting:
   ```bash
   php artisan websockets:serve
   ```

3. Start the queue worker
   This will begin processing jobs on your queue:
   ```bash
   php artisan queue:work
   ```

### Additional Information

Ensure all services are running correctly and check the application in your web browser by navigating to the localhost
address provided by the php artisan serve command.
