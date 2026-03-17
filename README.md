# Raccoon

A lightweight full-stack CRUD application with a custom PHP MVC framework on the backend and a Vue 3 single-page application on the frontend. Manages a catalog of items with create, read, update, and delete operations.

## Tech Stack

### Backend
- **Language:** PHP 8+
- **Architecture:** Custom MVC framework (no external framework dependencies)
- **Database:** SQLite
- **Server:** PHP built-in server

### Frontend
- **Framework:** Vue 3 with Vue Router
- **HTTP Client:** Axios
- **UI:** Bootstrap 5
- **Build Tool:** Vite

## Features

- Custom PHP router with GET/POST method support
- JSON API responses with proper CORS handling
- Model layer with validation and CRUD operations
- Database abstraction via PDO
- Vue 3 SPA with create, edit, and index views for items
- Item properties: name, description, brand, color, price, availability, checked status

## API Endpoints

| Method | Endpoint             | Description       |
|--------|----------------------|-------------------|
| GET    | `/api/items`         | List all items    |
| GET    | `/api/item?id=`      | Get single item   |
| POST   | `/api/items/create`  | Create an item    |
| POST   | `/api/items/update`  | Update an item    |
| GET    | `/api/items/delete?id=` | Delete an item |

## Setup

1. **Clone the repository**
   ```bash
   git clone git@github.com:joel767443/Raccoon.git
   cd Raccoon
   ```

2. **Set up the database**

   The backend will auto-create the SQLite database at `src/Database/raccoon.sqlite` on first run (and seed it with sample items).

   To reset the database, delete `src/Database/raccoon.sqlite` and start the server again.

3. **Configure the backend**
   ```bash
   cp config.sample.php config.php
   ```
   (Optional) Edit `config.php` to change `sqlitePath`.

4. **Start the PHP server**
   ```bash
   php -S localhost:8001
   ```

5. **Set up the frontend** (in a new terminal)
   ```bash
   cd front-end
   npm install
   cp src/config.example.js src/config.js
   ```
   Set `apiBaseUrl` in `src/config.js` to `http://localhost:8001`.

6. **Start the frontend dev server**
   ```bash
   npm run dev
   ```

## Project Structure

```
Raccoon/
├── index.php              # Entry point and route definitions
├── autoload.php           # PSR-4 autoloader
├── config.sample.php      # Database config template
├── src/
│   ├── Application.php    # App bootstrap
│   ├── Router.php         # Custom router
│   ├── Database/
│   │   ├── Database.php   # PDO database wrapper
│   │   ├── main.sql       # Legacy MySQL schema/seed (optional reference)
│   │   └── main.sqlite.sql # SQLite schema and seed data
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ItemsController.php
│   │   ├── Requests/
│   │   │   └── Request.php
│   │   └── Responses/
│   │       └── Response.php
│   └── Models/
│       ├── Model.php      # Base model
│       └── Item.php       # Item model
└── front-end/
    ├── src/
    │   ├── App.vue
    │   ├── main.js
    │   ├── router/index.js
    │   └── views/items/
    │       ├── IndexView.vue
    │       ├── CreateView.vue
    │       └── EditView.vue
    └── package.json
```
