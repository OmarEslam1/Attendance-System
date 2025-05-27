# Attendance System

A comprehensive attendance management system with RESTful API, built using PHP and MySQL. The system includes features for employee attendance tracking, user management, and messaging between users.

## Features

- **User Authentication**
  - Secure login system with JWT tokens
  - Role-based access control (Manager/Employee)
  - Session management

- **Attendance Management**
  - Check-in/Check-out functionality
  - Attendance history tracking
  - Daily attendance reports
  - Status tracking (Present/Absent)

- **User Management**
  - Create, read, update, and delete users
  - Role assignment
  - User profile management

- **Messaging System**
  - Real-time messaging between users
  - Message history
  - Read/unread status

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/attendance-system.git
   cd attendance-system
   ```

2. Create a MySQL database and import the schema:
   ```bash
   mysql -u your_username -p your_database < database/schema.sql
   ```

3. Configure the database connection:
   - Copy `config/database.example.php` to `config/database.php`
   - Update the database credentials in `config/database.php`

4. Set up the web server:
   - Point your web server's document root to the project's public directory
   - Ensure the web server has write permissions for the uploads directory

5. Install dependencies:
   ```bash
   composer install
   ```

## Project Structure

```
attendance-system/
├── api/                    # API endpoints
│   ├── auth/              # Authentication endpoints
│   ├── users/             # User management endpoints
│   ├── attendance/        # Attendance management endpoints
│   └── messages/          # Messaging system endpoints
├── config/                # Configuration files
├── database/              # Database schema and migrations
├── includes/              # Shared PHP files
│   ├── auth.php          # Authentication middleware
│   ├── database.php      # Database connection
│   └── functions.php     # Helper functions
├── public/               # Publicly accessible files
├── uploads/              # File uploads directory
├── vendor/               # Composer dependencies
├── .htaccess             # Apache configuration
├── api_docs.php          # API documentation
├── composer.json         # Composer configuration
└── README.md            # Project documentation
```

## API Documentation

The API documentation is available in two formats:

1. **Interactive Documentation**
   - Access the API documentation at: `http://your-domain/api_docs.php`
   - Includes detailed endpoint descriptions, request/response examples, and status codes

2. **API Endpoints Overview**

   ### Authentication
   - `POST /api/auth/login` - User login
   
   ### Users
   - `GET /api/users` - Get all users
   - `GET /api/users?id=1` - Get specific user
   - `POST /api/users` - Create new user
   - `PUT /api/users` - Update user
   - `DELETE /api/users?id=1` - Delete user

   ### Attendance
   - `GET /api/attendance` - Get attendance records
   - `POST /api/attendance` - Create attendance record (check-in)
   - `PUT /api/attendance` - Update attendance record (check-out)
   - `DELETE /api/attendance?id=1` - Delete attendance record

   ### Messages
   - `GET /api/messages` - Get messages between users
   - `POST /api/messages` - Send new message
   - `DELETE /api/messages?id=1` - Delete message

For detailed API documentation, including request/response formats and status codes, please refer to the interactive documentation.

## Security

- All API endpoints (except login) require authentication using JWT tokens
- Passwords are hashed using secure algorithms
- Input validation and sanitization implemented
- CORS protection enabled
- Rate limiting implemented for API endpoints

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please open an issue in the GitHub repository or contact the development team.

## Acknowledgments

- [PHP](https://www.php.net/)
- [MySQL](https://www.mysql.com/)
- [Composer](https://getcomposer.org/)
- [JWT](https://jwt.io/) 
