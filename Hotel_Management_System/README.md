
# Hotel Management System

## Project Description
This is a **Hotel Management System** built with **PHP, MySQL, and HTML CSS**.  
It allows users to view available rooms, book rooms, and manage bookings. Admins can manage rooms, users, and bookings, as well as reset passwords.

Features include:  
- User registration and login  
- Room listing with images and prices  
- Room booking system  
- Admin dashboard to add/edit/delete rooms  
- Password reset functionality via email  
- Responsive design  
---

## Table of Contents
- [Project Description](#project-description)  
- [Installation and Running](#installation-and-running)  
- [How to Use](#how-to-use)  
- [Credits](#credits)  
- [License](#license)  
- [Badges](#badges)  
- [How to Contribute](#how-to-contribute)  
- [Tests](#tests)  

---

## How to Install and Run the Project
1. **Download the project** from GitHub.  
2. **Install XAMPP or any local server** (Apache + MySQL).  
3. Place the project folder inside `htdocs` (e.g., `C:\xampp\htdocs\Hotel_Management_System`).  
4. **Import the database:**  
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`)  
   - Create a new database (e.g., `hotel_db`)  
   - Import the provided `hotel_db.sql` file into this database.  
5. **Update database credentials:**  
   - Open `includes/db.php`  
   - Set your MySQL username, password, and database name  
6. Start **Apache** and **MySQL** from XAMPP control panel.  
7. Open the project in your browser:  
   ```
   http://localhost/Hotel_Management_System/
   ```

---

## How to Use the Project
### For Users
1. Sign up or login using an existing account.  
2. Browse available rooms and their details.  
3. Book a room by clicking the "Book Now" button.  
4. Reset your password if forgotten using the "Forgot Password?" link.

### For Admin
1. Login with admin credentials.  
2. Access the **Admin Dashboard**.  
3. Manage rooms (add/edit/delete rooms).  
4. Manage users and bookings.  
5. Reset or change passwords for users if necessary.  

### For Receiptionist
1. Login with receiptionist credentials.  
2. Access the **Receiptionist Dashboard**.  
3. Manage Bookings & Check-in_Check-out.  
4. Manage payments.  
5. Reset or change passwords for users if necessary. 
---

## Credits
- Developed by **[Rakibul Hasan Shakil]**  
- Uses **PHP**, **MySQL**, **HTML**, **CSS**, **JavaScript**.  

---

## License
This project is licensed under the **MIT License**. See `LICENSE` for more information.  

---

## Badges
![PHP](https://img.shields.io/badge/PHP-7.4-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

---

## How to Contribute
1. Fork the repository.  
2. Create a new branch (`git checkout -b feature-name`).  
3. Make your changes and commit (`git commit -m "Add feature"`).  
4. Push to the branch (`git push origin feature-name`).  
5. Open a Pull Request.  

---

## Tests
1. Ensure XAMPP is running.  
2. Test user registration, login, room booking, and password reset.  
3. Test admin dashboard functionalities.  
4. Check responsiveness of the pages on desktop.  
