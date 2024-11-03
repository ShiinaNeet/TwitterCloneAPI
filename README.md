Social Media API
This API provides core functionality for a social media platform. Users can register, log in, create posts, like and comment on posts, and perform other essential actions. Admin users have additional privileges, such as creating and managing user accounts.

Features
User Authentication: Registration, login, logout, and token refresh.
Post Management: CRUD operations on posts and access to popular posts.
Commenting: CRUD operations on comments for specific posts.
Likes: Like functionality for comments.
User Management: Update user information and manage accounts.
Admin Management: Manage active and disabled users and create users.

## API Routes

### Authentication
| Method | Endpoint       | Description                  |
|--------|-----------------|------------------------------|
| POST   | `api/login`    | Log in a user                |
| POST   | `api/logout`   | Log out the current user     |
| POST   | `api/register` | Register a new user          |
| POST   | `api/refresh`  | Refresh the access token     |

### Admin
| Method | Endpoint                    | Description                     |
|--------|------------------------------|---------------------------------|
| GET    | `api/admin/active-users`    | Get all active users            |
| POST   | `api/admin/create-user`     | Create a new user as an admin   |
| PUT    | `api/admin/disable-user`    | Disable a user                  |
| GET    | `api/admin/disabled-users`  | Get all disabled users          |
| PUT    | `api/admin/update-user`     | Update a user's information     |

### Posts
| Method      | Endpoint                   | Description                   |
|-------------|----------------------------|-------------------------------|
| GET         | `api/post`                 | List all posts                |
| POST        | `api/post`                 | Create a new post             |
| GET         | `api/post/{post}`          | Get a specific post by ID     |
| PUT/PATCH   | `api/post/{post}`          | Update a specific post        |
| DELETE      | `api/post/{post}`          | Delete a specific post        |
| POST        | `api/post/{id}/disable`    | Disable a specific post       |
| GET         | `api/posts/popular`        | Get a list of popular posts   |

### Comments
| Method      | Endpoint                                  | Description                    |
|-------------|-------------------------------------------|--------------------------------|
| GET         | `api/posts/{post}/comments`               | List comments for a specific post |
| POST        | `api/posts/{post}/comments`               | Add a comment to a specific post |
| GET         | `api/posts/{post}/comments/{comment}`     | View a specific comment        |
| PUT/PATCH   | `api/posts/{post}/comments/{comment}`     | Update a specific comment      |
| DELETE      | `api/posts/{post}/comments/{comment}`     | Delete a specific comment      |
| POST        | `api/comment/like`                        | Like a comment                 |

### User Account Management
| Method  | Endpoint            | Description               |
|---------|----------------------|---------------------------|
| GET     | `api/user`          | Get current user information |
| PUT     | `api/user/update`   | Update user information   |
| DELETE  | `api/user/delete`   | Delete user account       |
| DELETE  | `api/user/disable`  | Disable user account      |

Getting Started
Prerequisites
Laravel 9
PHP >= 8.0
MySQL or PostgreSQL
Installation
bash
Copy code
# Clone the repository
```git clone https://github.com/ShiinaNeet/TwitterCloneAPI.git```
# Navigate to the project directory
```cd TwitterCloneAPI```
# Install dependencies
```composer install```
# Set up your .env file and generate the application key
```cp .env.example .env```
```php artisan key:generate```
# Run migrations
```php artisan migrate```
# Start the server
```php artisan serve```

Access the API at ```http://localhost:8000/api```.

Contributing
Feel free to open an issue or submit a pull request. Your contributions are welcome!

License
This project is licensed under the MIT License.
