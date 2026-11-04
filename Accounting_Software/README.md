# Laundry Management System

## Overview

The Laundry System is a comprehensive application designed to streamline and manage the operations of a laundry business. This system provides an intuitive interface for both administrators and employees, ensuring efficient management of customers, orders, expenses, and employee information. Key features include real-time data filtering, role management, and detailed statistics.

## Features

### Authentication
- **Login Page**: Secure login for admins and employees.

### Dashboard
- **Statistics**: Real-time statistics on orders, customers, expenses, and other key metrics.

### Customer Management
- **Customer Page**: Real-time filtering and management of customer information using Livewire.

### Order Management
- **Order Page**: Real-time filtering and management of orders using Livewire.
- **Create Orders**: Easily create new orders.
- **Edit Orders**: Modify existing orders with updated information.

### Employee Management
- **Admin and Employees Page**: Edit personal and professional information of admins and employees.

### Financial Management
- **Expenses Page**: Track and manage business expenses.
- **Salary Page**: Manage and process employee salaries.

### Role Management
- **Role Management**: Assign and manage roles for different users in the system to control access and permissions.

### Website Settings
- **Website Settings Management**: Configure and manage various settings for the website.

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/ahmdsadik/laundry-sys
   ```
2. Navigate to the project directory:

    ```bash
    cd laundry-sys
    ```

3. Install the dependencies:

    ```bash
    composer install && npm install
    ```

4. Create a copy of the `.env.example` file and rename it to `.env`:

    ```bash
    cp .env.example .env
    ```

5. Generate an application key:

    ```bash
    php artisan key:generate
    ```

6. Migrate the database:

    ```bash
    php artisan migrate
    ```

7. Seed the database:

    ```bash
    php artisan db:seed
    ```

8. Start the development server:

    ```bash
    php artisan serve
    ```

## Usage


- Visit the application and log in to manage the laundry.
- Dashboard link is [http://localhost:8000/login](http://localhost:8000/dashboard/login).

- Login credentials for a Super Admin are :
    - PhoneNumber: `01234567800`
    - Password: `01234567800`
