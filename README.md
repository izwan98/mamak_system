#  Mamak Checkout System

This Laravel application implements a flexible checkout system for CapBay Mamak with configurable promotions. The system supports various promotion types including buy-one-get-one-free, bulk discounts, and combo promotions.

## Getting Started

These instructions will guide you through setting up and running the project on your local machine after unzipping the files.

### Prerequisites

Make sure you have the following software installed on your computer:

- PHP 8.0 or higher
- Composer
- MySQL/MariaDB
- Node.js and NPM

### Installation Steps

1. **Open your terminal and navigate to the project folder**:
   After unzipping, open your terminal and navigate to the project folder:
   ```bash
   cd path/to/capbay-mamak
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Create a copy of the environment file**:
   ```bash
   cp .env.example .env
   ```

4. **Configure the database**:
   - Open the `.env` file with a text editor
   - Set your database credentials:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=capbay_mamak
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```
   - Make sure to create the `capbay_mamak` database in your MySQL server first

5. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

6. **Run the database migrations and seed with sample data**:
   ```bash
   php artisan migrate --seed
   ```

7. **Install frontend dependencies**:
   ```bash
   npm install
   ```

8. **Compile frontend assets**:
   ```bash
   npm run dev
   ```

9. **Start the development server**:
   ```bash
   php artisan serve
   ```

10. **Access the application**:
    Open your browser and go to: `http://localhost:8000`

## System Architecture & Special Conditions

### Core Components

1. **Models**:
   - `Product` - Represents menu items with code, name, and price
   - `Promotion` - Defines different types of discounts and special offers
   - `PromotionRule` - Stores flexible conditions and actions for promotions
   - `Order` & `OrderItem` - Track completed transactions
   - `AppliedPromotion` - Records which promotions were used in each order

2. **Controllers**:
   - `ProductController` - Manages product CRUD operations
   - `PromotionController` - Handles promotion management
   - `CheckoutController` - Processes checkout functionality

3. **Services**:
   - `Checkout` - Simple interface matching the requirements
   - `CheckoutService` - Complex implementation handling promotion logic

4. **Views**:
   - Responsive Tailwind CSS interface
   - Product and promotion management forms
   - Interactive checkout with real-time calculations

### Special Business Rules & Constraints

1. **Enhanced Promotion Types**:
   - Added **Combo Promotion** type beyond the requirements
   - Combo promotions allow special pricing when specific products are purchased together

2. **Promotion Usage Limitations**:
   - **Buy X Get Y Free** promotions can only be applied ONCE per transaction
   - **Bulk Discount** promotions can only be applied ONCE per transaction
   - Only **Combo Promotions** can be applied multiple times in a single transaction

3. **Data Integrity Protection**:
   - **Promotion Rules Cannot Be Edited**: Once a promotion is created, its rules cannot be modified to maintain consistency. To change rules, create a new promotion.
   - **Promotions Used in Orders Cannot Be Deleted**: If a promotion has been applied to any order, it cannot be deleted. The system will mark it as inactive instead.
   - **Products Used in Orders Cannot Be Deleted**: Products that have been ordered cannot be removed from the system to maintain historical data integrity.

4. **Promotion Calculation Logic**:
   - System automatically selects the most beneficial promotion for the customer
   - Ensures the total price is never negative
   - Maintains clear, accurate transaction records

These special constraints were implemented to ensure data integrity, prevent inconsistencies in historical records, and provide a realistic business model that follows standard e-commerce practices.

## Usage Guide

### Product Management

1. Navigate to the Products section
2. Add new products with code, name, and price
3. Edit or delete existing products as needed
   - **Note**: Products that have been included in orders cannot be deleted

### Promotion Management

1. Navigate to the Promotions section
2. Create new promotions with different types:
   - **Buy X Get Y Free**: Buy a specific quantity of a product and get some free
   - **Bulk Discount**: Buy a minimum quantity of a product to get a lower price per unit
   - **Combo Promotion**: Buy specific products together for a special price
3. Set start/end dates for promotions or leave them active indefinitely
   - **Note**: Promotion rules cannot be edited after creation for data integrity
   - **Note**: Promotions used in orders cannot be deleted, only deactivated

### Checkout System

1. Navigate to the Checkout section
2. Add products to your cart
3. The system automatically applies relevant promotions:
   - Buy X Get Y Free & Bulk Discount promotions are applied only once per transaction
   - Combo promotions can be applied multiple times
4. View the calculated subtotal, discount, and total
5. Complete the checkout to create an order

### Test Checkout

1. Navigate to the Test Checkout section
2. Run predefined test cases from the requirements
3. Create custom test scenarios using comma-separated product codes

## Default Data

The system comes with a few sample products and promotions:

### Products
- B001 | Kopi | RM2.50
- F001 | Roti Kosong | RM1.50
- B002 | Teh Tarik | RM2.00

### Promotions
- Buy 1 Get 1 Free on Kopi
- Buy 2+ Roti Kosong for RM1.20 each
- Buy 1 Get 1 Free on Teh Tarik

## Troubleshooting

### Common Issues

1. **Database connection error**:
   - Check that MySQL is running
   - Verify your database credentials in the `.env` file
   - Make sure the database exists

2. **Missing dependencies**:
   - Run `composer install` to install PHP dependencies
   - Run `npm install` to install JavaScript dependencies

3. **Permission issues**:
   - Ensure the `storage` and `bootstrap/cache` directories are writable:
     ```bash
     chmod -R 775 storage bootstrap/cache
     ```

## Test Requirements

The system passes the test cases specified in the original requirements:

- List: B001,F001,B002,B001,F001 - Expected total: RM6.9
- List: B002,B002,F001 - Expected total: RM3.5
- List: B001,B001,B002 - Expected total: RM4.5

## System Overview

### Database Structure

The application uses several tables:
- `products` - Stores product information (code, name, price)
- `promotions` - Stores promotion information
- `promotion_rules` - Stores the rules for each promotion
- `orders` - Stores order information
- `order_items` - Stores items in each order
- `applied_promotions` - Tracks which promotions were applied to an order

### Checkout Implementation

The main checkout implementation follows the interface specified in the requirements:

```php
$checkout = new Checkout($pricingRules);
$checkout->scan($item);
$checkout->scan($item);
$price = $checkout->total;
```

The `Checkout` class can be found in `app/Services/Checkout.php`. It provides a simple interface that delegates to the more complex `CheckoutService` class which handles the promotion rules and calculations.

### Test Cases

The system has been tested with the following cases as specified in the requirements:

1. B001,F001,B002,B001,F001 = RM6.9
2. B002,B002,F001 = RM3.5
3. B001,B001,B002 = RM4.5

You can test these cases using the Test Checkout page in the application.

## Usage

### Managing Products

1. Navigate to Products page to add, edit, or delete products
2. Each product has a code, name, and price

### Managing Promotions

1. Navigate to Promotions page to add, edit, or manage promotions
2. You can create three types of promotions:
   - **Buy X Get Y Free**: Buy a certain quantity of a product and get some free
   - **Bulk Discount**: Buy a minimum quantity of a product to get a lower price per unit
   - **Combo Promotion**: Buy specific products together for a special price

### Using Checkout

1. Navigate to Checkout page
2. Add products to your cart by clicking "Add to Cart" button
3. See the calculated total with applied promotions in real-time
4. Click "Checkout" to complete the order
5. You can also use the manual checkout form to test specific item combinations

### Testing the Checkout

1. Navigate to Test Checkout page
2. You can run predefined test cases or create your own
3. See the calculated total for each test case

The tests include validation of the required test cases from the specifications.
