### **ShuleMIS - School Management System**  

ShuleMIS is a **Laravel-based** School Management System that simplifies school operations, including **student registration, class management, parent-student relationships**, and more. It also integrates **Livewire** for interactive UI components.

---

## **Features**
- 🏫 **School Management** – Manage schools, students, teachers, and staff.
- 📝 **Student Registration** – Allow students to register and get assigned to classes.
- 🎓 **Class Management** – Assign students to classes and manage schedules.
- 👨‍👩‍👧 **Parent & Student Accounts** – Parents can monitor their children’s academic progress.
- 📢 **Announcements & Notifications** – Send announcements to students and staff.
- 💳 **Fee Payment System** – Manage student fees and payments (coming soon).

---

## **Tech Stack**
- **Laravel 8+** – Backend framework  
- **MySQL / SQLite** – Database  
- **Livewire** – Interactive frontend components  
- **Bootstrap & Font Awesome** – UI styling  
- **Docker (optional)** – For containerized deployment  
- **JWT Authentication** – Secure API access  

---

## **Installation & Setup**  

### **1. Clone the Repository**  
```bash
git clone https://gitlab.com/phabianmalonja3/shulemis.git
cd shulemis
```

### **2. Install Dependencies**  
```bash
composer install
```

### **3. Set Up Environment**  
```bash
cp .env.example .env
```
Update your `.env` file with the correct database credentials:  
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shulemis_db
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

### **4. Generate App Key**  
```bash
php artisan key:generate
```

### **5. Run Migrations & Seed Database**  
```bash
php artisan migrate --seed
```

### **6. Run the Application**  
```bash
php artisan serve
```
Access the app at: **http://127.0.0.1:8000**  

---

## **Livewire Components**
ShuleMIS integrates **Livewire** for interactive UI elements, such as:
- **Dynamic Forms** – Live validation for student registration.
- **Real-time Updates** – Update student and class details without page refresh.
- **Interactive Dashboards** – Display school statistics dynamically.

---

## **Deployment**
### **Using Docker**  
If you are using Docker, start the app with:  
```bash
docker-compose up -d
```
This will run Laravel, MySQL, and other dependencies inside a container.

---

## **Security & Authentication**
ShuleMIS uses **JWT Authentication** for API security. To generate a JWT token for a user, send a **POST** request to `/api/login` with valid credentials.

---

## **Contributing**  
Want to improve ShuleMIS? Feel free to submit a pull request. Please ensure:  
- Your code follows Laravel best practices.  
- Your changes are tested before submission.  

---

## **Contact**
If you encounter issues, have feature requests, or want to contribute, reach out via:  
📧 Email: [phabianmalonja3@gmail.com](mailto:phabianmalonja3@gmail.com)

