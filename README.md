# SerComp

A web application for automating the reception and processing of service requests for office equipment repair.  
The project is written in native PHP and solves real-world business tasks for a service organization.
[translated this  file to russian](./README_RU.md) 

---
## 🚀 Features
- User registration and personal account  
- Creation and editing of repair requests  
- Real-time tracking of request status  
- Administrator panel for managing users, services, and requests  
- Review and comment system  

---

## 🛠 Technologies
- PHP — core application logic  
- MySQL — data storage  
- HTML/CSS/JS — client-side interface  

---

## 📂 Project Structure
```
sercomp/
├── account/       # User dashboard
├── admin/         # Admin panel
├── auth/          # Authentication
├── db/            # Database connection
├── include/       # Shared templates
├── request/       # Request management
├── review/        # Reviews
└── static/        # Static files
```
---

## ⚙️ Installation and Launch
1. Clone the repository:
     git clone [https://github.com/ahkaz-dev/sercomp.git](https://github.com/ahkaz-dev/sercomp.git)
2. Configure your web server (Apache/Nginx) and PHP (>=7.4).

3. Create a MySQL database and import the queries from **mainsql.txt**.

4. Specify your database connection parameters in **db/connect.php**.

5. Open the project in your browser.

---

## 🔒 Security

- Input validation and sanitization  
- Protection against XSS and SQL injection  
- Access control and user permissions  

---

## 🖼 Screenshots

| Home Page | Services | Reviews |
|-----------------|----------------|----------------|
| ![Home](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/index.png) | ![Services](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/our-service.png) | ![Reviews](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/review.png) |

| My Requests |
|-----------------|
|  ![My Requests](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/my-request.png) |

| Admin Panel | All Requests | Request Details |
|-----------------|----------------|----------------|
| ![Admin Panel](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/admin.png) | ![All Requests](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/all-requests.png) | ![Request](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/request.png) |
```
