# Projapoti Kuthir - Biodata Management System

A web-based biodata management system for managing and displaying biodata profiles with images. The system allows users to upload, view, and manage biodata profiles for both men and women.

## Features

- **Biodata Management**
  - Upload biodata profiles with images
  - Separate sections for men and women profiles
  - View all profiles in a gallery format
  - Delete profiles as needed

- **User Interface**
  - Responsive design
  - Modern and clean interface
  - Easy navigation
  - Image gallery view
  - Tab-based filtering (All/Men/Women)

- **Technical Features**
  - PHP backend
  - MySQL database
  - Bootstrap 5 for styling
  - Font Awesome icons
  - Google Fonts integration

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (for local development)

## Installation

1. Clone the repository to your web server directory:
   ```bash
   git clone [repository-url]
   ```

2. Create a MySQL database and import the database schema:
   ```sql
   CREATE DATABASE projapoti_kuthir;
   ```

3. Configure the database connection:
   - Open `db_config.php`
   - Update the database credentials:
     ```php
     $host = 'localhost';
     $username = 'your_username';
     $password = 'your_password';
     $database = 'projapoti_kuthir';
     ```

4. Set up the web server:
   - For XAMPP: Place the project in `htdocs` directory
   - For Apache: Configure virtual host
   - For Nginx: Set up server block

5. Set proper permissions:
   ```bash
   chmod 755 -R /path/to/project
   chmod 777 -R /path/to/project/uploads
   ```

## Directory Structure

```
projapoti_kuthir/
├── index.php              # Main application file
├── db_config.php          # Database configuration
├── display_cards.php      # Card display logic
├── upload_image.php       # Image upload handler
├── save_biodata.php       # Biodata saving logic
├── delete_image.php       # Image deletion handler
├── card_manager.js        # Frontend card management
├── custom.css            # Custom styles
├── style.css             # Main stylesheet
├── responsive.css        # Responsive styles
└── uploads/             # Image upload directory
```

## Usage

1. **Viewing Profiles**
   - Open the website in a browser
   - Use the tabs to filter between All/Men/Women profiles
   - Click on profiles to view details

2. **Adding New Profiles**
   - Click the "Upload Image" button
   - Select an image file
   - Fill in the biodata form
   - Submit to add the profile

3. **Managing Profiles**
   - Use the menu button on each card
   - Select delete to remove a profile

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, email [your-email] or create an issue in the repository.

## Acknowledgments

- Bootstrap 5
- Font Awesome
- Google Fonts
- PHP
- MySQL 