First clone the repo
make a new file .env and copy content of .env_example into .env
connect your database keys into .env file
IN THE TERMINAL RUN THE FOLLOWING COMMANDS
 composer install
 php artisan key:generate 
 php artisan migrate
 php artisan serve




// This is an ecomerce api (In WORKING progress)
👤 1. User & Authentication
Register / Login / Logout
Guest checkout (very important)
User profile (name, email, phone)
Address management (you already started this 👍)
Token-based auth (e.g., Sanctum)

🛍️ 2. Product Management (with variants)
View all products
View single product 
Categories & subcategories
Product variants (size, color, etc.)
Product images
Stock tracking

Create order from cart
Apply shipping address
Calculate totals (subtotal, tax, delivery fee)
Order statuses:
pending
paid
shipped
delivered
cancelled

5. Payment Integration
Paystack (you’re already using this 👌)
Payment verification
Webhooks (VERY important for reliability)
Handle failed payments

YOU CAN REACH ME ON kboahene760@gmail.com