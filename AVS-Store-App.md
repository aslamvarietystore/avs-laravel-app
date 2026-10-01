# AVS Store — Laravel E-Commerce System

## VS Code / Cursor AI Agent Instructions

## 1. Project Overview

Build a professional Laravel-based e-commerce and inventory management system for:

**Business:** Aslam Variety Store (AVS)

The system is primarily designed for wholesale store staff. Staff should be able to quickly search for any product using:

* Item Number
* Barcode

After finding a product, the system must display:

* Product image
* Product name
* Item number
* Barcode
* Category
* China purchase price in CNY/Yuan
* Exchange rate
* Calculated PKR cost
* Wholesale price
* Retail price, if applicable
* Available quantity/stock
* Product description
* Product status

The main goal is:

> Staff should be able to enter/scan an Item Number or Barcode and immediately see the product details and converted PKR price.

---

# 2. Technology Stack

Use the following technologies:

### Backend

* Laravel
* PHP
* MySQL
* Laravel Eloquent ORM
* Laravel Authentication
* Laravel Policies/Gates
* Laravel Validation
* Laravel Queues where required

### Frontend

Prefer:

* Laravel Blade
* Tailwind CSS
* Alpine.js

Use Livewire where interactive functionality is useful.

Do NOT introduce React/Vue unless specifically requested later.

### Development Environment

The project must work well with:

* VS Code
* Cursor
* Laravel Artisan
* Composer
* NPM
* MySQL
* Git

---

# 3. Application Name

Use:

**AVS Store**

Suggested project name:

```text
avs-store
```

Suggested application title:

```text
Aslam Variety Store
```

---

# 4. Main Objectives

The system must provide:

1. Product management
2. Product image management
3. Category management
4. Brand management
5. Barcode management
6. Item-number management
7. Inventory management
8. China Yuan price management
9. CNY → PKR conversion
10. Exchange-rate management
11. Wholesale pricing
12. Retail pricing
13. Fast product search
14. Barcode scanner support
15. Customer management
16. Orders
17. Order items
18. Stock movement
19. User management
20. Dashboard
21. Reports
22. Product import/export
23. Audit logging
24. Responsive mobile/tablet/desktop interface

---

# 5. Important Business Requirement

The most important workflow is:

```text
Staff opens Product Search
        ↓
Enter Item Number
OR
Scan Barcode
        ↓
System finds product
        ↓
Display product details
        ↓
Display China Price (CNY)
        ↓
Get applicable exchange rate
        ↓
Calculate PKR price
        ↓
Display selling/wholesale price
```

Example:

```text
Item Number: AVS-10025

Product:
Butterfly Hair Organizer

China Price:
¥ 8.50

Exchange Rate:
1 CNY = 39.00 PKR

PKR Cost:
8.50 × 39.00
= 331.50 PKR
```

The system must calculate this dynamically.

Do not hard-code the exchange rate.

---

# 6. Currency System

Primary purchase currency:

```text
CNY
```

Display currency:

```text
PKR
```

Create an exchange-rate table.

Suggested fields:

```text
id
currency_from
currency_to
rate
effective_date
is_active
notes
created_by
created_at
updated_at
```

Example:

```text
CNY → PKR
39.00
```

The system should support future currencies such as:

```text
USD
EUR
AED
CNY
PKR
```

but the first version should focus on:

```text
CNY → PKR
```

---

# 7. Product Database

Create a `products` table.

Suggested fields:

```text
id
item_number
barcode
sku
name
slug
category_id
brand_id
description
china_price
exchange_rate
cost_pkr
wholesale_price
retail_price
stock_quantity
low_stock_threshold
unit
weight
status
is_featured
created_by
updated_by
created_at
updated_at
```

Do not rely on the stored `cost_pkr` value alone.

The system should be able to recalculate:

```text
cost_pkr = china_price × exchange_rate
```

Store historical pricing when necessary.

---

# 8. Product Item Number

Every product should have a unique Item Number.

Example:

```text
AVS-000001
AVS-000002
AVS-000003
```

Allow manual item numbers.

Also provide automatic item-number generation.

The Item Number must be:

* Unique
* Searchable
* Indexed
* Easy to read
* Easy to communicate verbally

---

# 9. Barcode

Every product may have a barcode.

Barcode must be:

* Unique
* Indexed
* Searchable

Support common barcode formats.

The application should allow:

```text
Manual barcode entry
```

and:

```text
Barcode scanner input
```

Most USB barcode scanners behave like keyboards.

Therefore the search field should support scanner input without requiring special hardware integration.

---

# 10. Fast Product Search

Create a dedicated screen:

```text
/products/search
```

The search interface should be extremely fast.

Example:

```text
--------------------------------------------
        AVS PRODUCT SEARCH
--------------------------------------------

[ Scan Barcode / Enter Item Number ]

                 [SEARCH]

--------------------------------------------
```

When the scanner enters:

```text
8901234567890
```

the system should immediately search the barcode.

If the user enters:

```text
AVS-000125
```

search by Item Number.

Search priority:

1. Exact barcode
2. Exact item number
3. SKU
4. Partial barcode
5. Partial item number
6. Product name

Exact matches should be returned first.

---

# 11. Product Result Screen

After finding a product display a professional product card:

```text
------------------------------------------------
PRODUCT
------------------------------------------------

[ PRODUCT IMAGE ]

Butterfly Hair Accessories Organizer

Item No:
AVS-000125

Barcode:
8901234567890

Category:
Hair Accessories

China Price:
¥ 8.50

Exchange Rate:
1 CNY = Rs. 39.00

PKR Cost:
Rs. 331.50

Wholesale Price:
Rs. 390

Retail Price:
Rs. 450

Stock:
125 Pieces

------------------------------------------------
```

Make the important prices visually prominent.

---

# 12. Pricing Calculation

Create a centralized pricing service.

Example:

```php
$costPkr = $chinaPrice * $exchangeRate;
```

Do NOT duplicate pricing logic throughout controllers and views.

Create something similar to:

```text
app/Services/PricingService.php
```

Example responsibilities:

```text
getCurrentExchangeRate()
calculatePkrCost()
calculateWholesalePrice()
calculateRetailPrice()
```

---

# 13. Price Rounding

The system should support configurable rounding.

Examples:

```text
331.50
332
335
340
350
```

Create configurable rounding rules.

Example:

```text
Round to nearest 1
Round to nearest 5
Round to nearest 10
Round to nearest 50
```

Default:

```text
No rounding
```

unless configured by administrator.

---

# 14. Pricing History

Do not overwrite important historical pricing information without tracking it.

Create:

```text
product_price_histories
```

Suggested fields:

```text
id
product_id
china_price
exchange_rate
cost_pkr
wholesale_price
retail_price
effective_from
effective_until
created_by
created_at
```

This allows administrators to see previous prices.

---

# 15. Product Categories

Create category management.

Example categories:

```text
Hair Accessories
Imitation Jewellery
Toys
Gift Items
Kids Items
Nails
Nail Accessories
Beauty Accessories
Fans
Other
```

Categories must be editable from admin.

---

# 16. Product Images

Products must support multiple images.

Create:

```text
product_images
```

Suggested fields:

```text
id
product_id
image_path
is_primary
sort_order
created_at
updated_at
```

The admin should be able to:

* Upload image
* Delete image
* Set primary image
* Reorder images

Optimize images before storage where practical.

---

# 17. Product Management

Admin should be able to:

### Create Product

Fields:

```text
Item Number
Barcode
SKU
Product Name
Category
Brand
Description
China Price
Exchange Rate
Wholesale Price
Retail Price
Stock
Low Stock Threshold
Unit
Images
Status
```

### Edit Product

Allow all editable fields to be updated.

### Delete Product

Prefer soft deletion.

Use:

```php
SoftDeletes
```

instead of permanently deleting products.

---

# 18. Inventory Management

Create inventory tracking.

Do not simply update stock without recording why it changed.

Create:

```text
stock_movements
```

Suggested fields:

```text
id
product_id
type
quantity
previous_quantity
new_quantity
reference_type
reference_id
notes
created_by
created_at
```

Movement types:

```text
purchase
sale
adjustment
return
damage
lost
opening_stock
```

Example:

```text
Product:
AVS-000125

Previous:
100

Adjustment:
+25

New:
125
```

---

# 19. Stock Alerts

Allow each product to have:

```text
low_stock_threshold
```

Dashboard should display:

```text
Low Stock Products
Out of Stock Products
```

Example:

```text
Low Stock: 12
Out of Stock: 4
```

---

# 20. Customer Management

Create customers table.

Suggested fields:

```text
id
name
phone
whatsapp
email
company_name
address
city
country
notes
status
created_at
updated_at
```

Since AVS is wholesale-focused, customers may include:

```text
Shopkeepers
Resellers
Online Sellers
Retailers
Distributors
```

---

# 21. Customer Multiple Addresses

A customer may have multiple shipping addresses.

Create:

```text
customer_addresses
```

Fields:

```text
id
customer_id
label
recipient_name
phone
address
city
province
postal_code
country
is_default
created_at
updated_at
```

---

# 22. Orders

Create:

```text
orders
```

Suggested fields:

```text
id
order_number
customer_id
shipping_address_id
subtotal
discount
shipping_amount
total
currency
payment_status
order_status
notes
created_by
created_at
updated_at
```

Order statuses:

```text
pending
confirmed
processing
packed
shipped
delivered
cancelled
returned
```

Payment statuses:

```text
unpaid
partial
paid
refunded
```

---

# 23. Order Items

Create:

```text
order_items
```

Fields:

```text
id
order_id
product_id
item_number
product_name
quantity
unit_price
subtotal
created_at
updated_at
```

Store product name and price snapshots so historical orders do not change when the product is later edited.

---

# 24. Wholesale Shopping Cart

Implement a shopping cart.

Cart should support:

```text
Product
Quantity
Unit Price
Subtotal
Discount
Total
```

Example:

```text
Butterfly Organizer
Quantity: 50
Price: Rs. 390

Subtotal:
Rs. 19,500
```

---

# 25. Admin Dashboard

Create a professional dashboard.

Display:

```text
Total Products
Active Products
Low Stock
Out of Stock
Total Customers
Today's Orders
Pending Orders
Today's Sales
Current CNY Rate
```

Also show:

```text
Recent Orders
Low Stock Products
Recently Added Products
Recent Stock Movements
```

---

# 26. User Roles

Implement role-based access.

Minimum roles:

### Admin

Can:

* Manage users
* Manage products
* Manage prices
* Manage exchange rates
* Manage inventory
* Manage customers
* Manage orders
* View reports
* Configure system

### Staff

Can:

* Search products
* Scan barcode
* View product prices
* Manage customers
* Create orders
* View inventory

Staff should not be able to change exchange rates unless explicitly authorized.

### Viewer

Can:

* Search products
* View product information
* View prices

No editing permissions.

---

# 27. Authentication

Implement Laravel authentication.

Requirements:

* Login
* Logout
* Password reset
* Remember me
* User profile
* Role permissions

Use Laravel's recommended authentication approach for the selected Laravel version.

---

# 28. Search UI

The search box should be the main focus of the staff dashboard.

When staff opens the search screen:

Automatically focus:

```text
Barcode / Item Number
```

Allow:

```text
Ctrl + K
```

to focus search.

After scanning a barcode, product results should appear immediately.

Avoid unnecessary page reloads.

Use Livewire/AJAX where appropriate.

---

# 29. Keyboard-Friendly Workflow

Optimize for shop staff.

Recommended shortcuts:

```text
Ctrl + K
Focus Product Search

Enter
Search

Esc
Clear Search

Ctrl + N
New Product

Ctrl + S
Save Form
```

Do not override browser shortcuts that could cause problems.

---

# 30. Barcode Scanner Workflow

Typical workflow:

```text
Staff clicks search page
        ↓
Scanner scans barcode
        ↓
Barcode appears in input
        ↓
System detects/scans input
        ↓
Search product
        ↓
Display product
```

Provide a configurable scan delay/debounce.

Do not require a camera-based scanner in the first version.

Later support:

```text
Mobile Camera Barcode Scanner
```

as an optional enhancement.

---

# 31. E-Commerce Storefront

Create a public storefront.

Pages:

```text
Home
Products
Categories
Product Details
Cart
Checkout
Customer Login
Customer Registration
My Account
My Orders
Contact
About
```

Product listing should support:

```text
Search
Category filter
Price filter
Availability
Sorting
Pagination
```

---

# 32. Product Detail Page

Display:

```text
Product Images
Product Name
Item Number
Availability
Description
Wholesale Price
Retail Price
Minimum Order Quantity
Quantity Selector
Add to Cart
```

Whether China price and exchange rate are visible publicly should be configurable.

Default:

```text
Do NOT expose internal China purchase cost publicly.
```

Only authorized staff/admin users should see:

```text
China Price
Exchange Rate
PKR Cost
```

---

# 33. Wholesale Pricing

Support minimum order quantities.

Example:

```text
MOQ: 12 pieces
```

If customer orders:

```text
1-11
```

show retail price or prevent checkout according to configuration.

If customer orders:

```text
12+
```

apply wholesale price.

Make MOQ configurable per product.

---

# 34. Product Price Levels

Support:

```text
Cost Price
Wholesale Price
Retail Price
Special Customer Price
```

Future support:

```text
Distributor Price
Reseller Price
VIP Price
```

Avoid hardcoding price types.

Create a flexible pricing structure.

---

# 35. Exchange Rate Management

Admin page:

```text
Settings → Currency → Exchange Rates
```

Example:

```text
CNY → PKR

Rate:
39.00

Effective Date:
2026-09-30

Status:
Active
```

Only one rate should normally be active for the current CNY → PKR conversion.

However, retain previous rates for historical calculations.

---

# 36. Manual Exchange Rate

Do NOT depend entirely on an external API.

Admin must be able to manually update:

```text
1 CNY = X PKR
```

This is important because AVS may use its own purchasing/settlement rate.

Future external exchange-rate API integration can be added later.

---

# 37. Reports

Create reports for:

### Product Reports

* All products
* Products by category
* Products by brand
* Low stock
* Out of stock

### Pricing Reports

* China prices
* PKR costs
* Wholesale prices
* Price history
* Exchange-rate history

### Sales Reports

* Daily sales
* Monthly sales
* Customer sales
* Product sales
* Top-selling products

### Inventory Reports

* Stock movements
* Stock valuation
* Purchases
* Sales
* Adjustments

---

# 38. Export

Allow administrators to export:

```text
Products → Excel/CSV
Customers → Excel/CSV
Orders → Excel/CSV
Inventory → Excel/CSV
Price History → Excel/CSV
```

---

# 39. Import Products

Create CSV/Excel import functionality.

Example CSV:

```text
item_number,barcode,name,category,china_price,wholesale_price,retail_price,stock
AVS-000001,890000001,Hair Clip,Hair Accessories,2.50,150,180,100
```

Before importing:

1. Validate file
2. Validate columns
3. Detect duplicate barcode
4. Detect duplicate item number
5. Show preview
6. Ask user to confirm
7. Import
8. Display import results

Never silently overwrite products.

---

# 40. Database Design

Use proper foreign keys.

Example relationships:

```text
Category
    hasMany Products

Brand
    hasMany Products

Product
    belongsTo Category
    belongsTo Brand
    hasMany ProductImages
    hasMany StockMovements
    hasMany PriceHistories
    hasMany OrderItems

Customer
    hasMany CustomerAddresses
    hasMany Orders

Order
    belongsTo Customer
    belongsTo ShippingAddress
    hasMany OrderItems

OrderItem
    belongsTo Order
    belongsTo Product
```

Use database indexes on:

```text
products.item_number
products.barcode
products.sku
products.name
products.category_id
orders.order_number
customers.phone
```

---

# 41. Validation

Always validate server-side.

Example:

```text
item_number:
required
unique

barcode:
nullable
unique

china_price:
required
numeric
min:0

wholesale_price:
numeric
min:0

retail_price:
numeric
min:0

stock_quantity:
numeric
min:0
```

Never trust client-side validation alone.

---

# 42. Security

Follow Laravel security best practices.

Requirements:

* CSRF protection
* Authentication
* Authorization
* Policies
* Form Requests
* Mass assignment protection
* SQL injection protection through Eloquent/query builder
* XSS protection
* Secure file upload validation
* Rate limiting
* Secure passwords
* Session security

Do not expose:

```text
.env
database credentials
API keys
internal purchase costs
admin-only information
```

---

# 43. Audit Log

Important actions should be logged.

Examples:

```text
Product created
Product updated
Price changed
Exchange rate changed
Stock adjusted
Order cancelled
User created
User role changed
```

Audit log fields:

```text
user_id
action
model_type
model_id
old_values
new_values
ip_address
created_at
```

---

# 44. UI/UX Requirements

Use a clean professional interface.

Brand:

```text
Aslam Variety Store
AVS
```

The interface should work well on:

```text
Desktop
Laptop
Tablet
Mobile
```

Use:

* Responsive layout
* Large search field
* Clear pricing cards
* Clear buttons
* Fast navigation
* Toast notifications
* Confirmation dialogs
* Loading indicators
* Empty states
* Error states

---

# 45. Mobile Staff Search

The product search page must be particularly mobile-friendly.

Example:

```text
┌─────────────────────────────┐
│       ASLAM VARIETY STORE   │
├─────────────────────────────┤
│                             │
│ Scan / Item Number           │
│ [_________________________]  │
│                             │
│       SEARCH                │
│                             │
├─────────────────────────────┤
│ Butterfly Organizer          │
│                             │
│ Item: AVS-000125             │
│ Barcode: 8901234567890       │
│                             │
│ China: ¥8.50                 │
│ Rate: 39.00                  │
│ PKR Cost: Rs.331.50          │
│ Wholesale: Rs.390             │
│                             │
│ Stock: 125                   │
└─────────────────────────────┘
```

---

# 46. API Architecture

Even if the initial application uses Blade/Livewire, structure business logic so an API can be added later.

Potential API endpoints:

```text
GET /api/products/search
GET /api/products/{product}
GET /api/products/barcode/{barcode}
GET /api/products/item/{itemNumber}

POST /api/orders
GET /api/orders/{order}

GET /api/exchange-rates/current
```

Return consistent JSON.

Example:

```json
{
    "success": true,
    "data": {
        "item_number": "AVS-000125",
        "barcode": "8901234567890",
        "name": "Butterfly Hair Accessories Organizer",
        "china_price": 8.50,
        "exchange_rate": 39,
        "cost_pkr": 331.50,
        "wholesale_price": 390,
        "stock": 125
    }
}
```

---

# 47. Service Layer

Do not put all business logic inside controllers.

Recommended structure:

```text
app/
├── Actions/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Services/
│   ├── PricingService.php
│   ├── ProductSearchService.php
│   ├── InventoryService.php
│   ├── OrderService.php
│   └── ExchangeRateService.php
├── Policies/
└── Repositories/
```

Use services for complex business operations.

---

# 48. ProductSearchService

Create a dedicated service:

```text
ProductSearchService
```

Responsibilities:

```text
searchByBarcode()
searchByItemNumber()
searchBySku()
searchByName()
search()
```

Search should prioritize exact matches.

Example:

```text
Barcode exact match
        ↓
Item number exact match
        ↓
SKU exact match
        ↓
Partial match
```

---

# 49. PricingService

Create:

```text
PricingService
```

Responsibilities:

```text
getCurrentCnyRate()
calculateCostPkr()
calculateWholesalePrice()
calculateRetailPrice()
calculateMargin()
```

Example:

```php
$cost = $chinaPrice * $rate;
```

Keep this logic centralized.

---

# 50. InventoryService

Create:

```text
InventoryService
```

Methods:

```text
addStock()
removeStock()
adjustStock()
getStock()
checkAvailability()
```

Every stock modification should create a stock movement.

Use database transactions.

---

# 51. OrderService

Create:

```text
OrderService
```

Responsibilities:

```text
createOrder()
calculateTotals()
reserveStock()
reduceStock()
cancelOrder()
restoreStock()
```

Use transactions when modifying order and inventory.

---

# 52. Database Transactions

Use transactions for operations such as:

```text
Creating orders
Reducing stock
Cancelling orders
Stock adjustments
Price updates where multiple records change
```

Example:

```php
DB::transaction(function () {
    // operation
});
```

---

# 53. Testing

Create automated tests.

Minimum tests:

### Product Tests

```text
Create product
Update product
Delete product
Duplicate item number rejected
Duplicate barcode rejected
```

### Pricing Tests

```text
CNY × exchange rate = PKR
```

Example:

```text
8.50 × 39 = 331.50
```

### Search Tests

```text
Search by barcode
Search by item number
Search exact match
Search partial match
```

### Inventory Tests

```text
Add stock
Remove stock
Prevent negative stock where configured
Create stock movement
```

### Order Tests

```text
Create order
Calculate total
Reduce stock
Cancel order
Restore stock
```

---

# 54. Seed Data

Create seeders for development.

Example categories:

```text
Hair Accessories
Imitation Jewellery
Toys
Gift Items
Kids Items
Beauty Accessories
Fans
Other
```

Create sample products:

```text
AVS-000001
AVS-000002
AVS-000003
```

Use realistic test data.

Do not use production data in seeders.

---

# 55. Dashboard Search

The dashboard should include a global product search.

Example:

```text
Search Product by Item Number / Barcode
```

If exact product is found:

```text
Open Product Quick View
```

This should minimize the number of clicks required by staff.

---

# 56. Quick View

Create a modal for quick product information.

Example:

```text
┌────────────────────────────────────┐
│ Product Quick View                 │
├────────────────────────────────────┤
│ [Image]                            │
│                                    │
│ Butterfly Organizer                │
│ Item: AVS-000125                   │
│ Barcode: 8901234567890             │
│                                    │
│ China Price: ¥8.50                 │
│ CNY Rate: 39.00                     │
│ PKR Cost: Rs.331.50                │
│ Wholesale: Rs.390                  │
│ Stock: 125                          │
│                                    │
│ [View Product] [Add to Order]      │
└────────────────────────────────────┘
```

---

# 57. Product Privacy

Separate internal and public information.

### Internal

Staff/Admin:

```text
China price
Exchange rate
PKR cost
Margin
Supplier information
```

### Public

Customers:

```text
Product name
Images
Description
Wholesale price
Retail price
Stock availability
```

Use authorization policies.

---

# 58. Supplier Management

Prepare the architecture for suppliers.

Future table:

```text
suppliers
```

Fields:

```text
id
name
phone
whatsapp
email
address
country
notes
status
```

Product supplier relationship should support future implementation.

---

# 59. Product SKU vs Item Number vs Barcode

Keep these separate.

### Item Number

AVS internal identification:

```text
AVS-000125
```

### SKU

Inventory/business SKU:

```text
HA-BFLY-125
```

### Barcode

Physical scanning identifier:

```text
8901234567890
```

Do not use one field for all three.

---

# 60. SEO

Public e-commerce pages should support:

```text
SEO title
SEO description
SEO keywords
Open Graph title
Open Graph description
Open Graph image
Canonical URL
```

Product URLs:

```text
/products/butterfly-hair-accessories-organizer
```

Do not expose database IDs unnecessarily.

---

# 61. Performance

Optimize for fast product lookup.

Important indexes:

```text
barcode
item_number
sku
slug
category_id
```

Use eager loading where appropriate.

Avoid N+1 queries.

Use pagination.

Cache the active exchange rate.

Cache categories when appropriate.

---

# 62. Image Optimization

Product images should:

* Validate file type
* Validate size
* Generate optimized versions
* Generate thumbnails
* Store using Laravel filesystem
* Avoid unnecessarily huge images

Recommended product image formats:

```text
WebP
JPEG
PNG
```

---

# 63. Environment Configuration

Use `.env`.

Example:

```env
APP_NAME="Aslam Variety Store"
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=avs_store
DB_USERNAME=root
DB_PASSWORD=

APP_URL=http://localhost
```

Never commit `.env`.

Create:

```text
.env.example
```

---

# 64. Git

Initialize Git.

Recommended branches:

```text
main
develop
feature/*
bugfix/*
```

Use meaningful commits.

Example:

```text
feat: add product management
feat: add barcode search
feat: add CNY to PKR conversion
feat: add inventory tracking
fix: prevent duplicate barcodes
```

---

# 65. Recommended Project Development Order

The AI agent must implement the project incrementally.

### Phase 1 — Foundation

Implement:

```text
Laravel setup
Database
Authentication
User roles
Base layout
Tailwind
Navigation
```

### Phase 2 — Product System

Implement:

```text
Categories
Products
Images
Item numbers
SKU
Barcodes
```

### Phase 3 — Pricing

Implement:

```text
Exchange rates
CNY prices
PKR conversion
PricingService
Price history
```

### Phase 4 — Fast Search

Implement:

```text
Barcode search
Item number search
Product quick view
Mobile search
Keyboard workflow
```

### Phase 5 — Inventory

Implement:

```text
Stock
Stock movements
Low stock
Inventory reports
```

### Phase 6 — Customers

Implement:

```text
Customers
Multiple addresses
Customer search
```

### Phase 7 — Orders

Implement:

```text
Cart
Orders
Order items
Stock deduction
Order statuses
```

### Phase 8 — Public Store

Implement:

```text
Homepage
Product listing
Product details
Cart
Checkout
Customer account
```

### Phase 9 — Reports

Implement:

```text
Sales
Inventory
Products
Pricing
Customers
```

### Phase 10 — Optimization

Implement:

```text
Caching
Indexes
Security
Testing
Performance
SEO
```

---

# 66. Important Agent Rules

The AI coding agent MUST follow these rules.

### Rule 1

Do not build the entire project in one huge step.

Implement one module at a time.

### Rule 2

Before creating a new table, inspect existing migrations and models.

Avoid duplicate tables.

### Rule 3

Before creating a new service, inspect existing services.

Avoid duplicate business logic.

### Rule 4

Use Laravel conventions.

### Rule 5

Use migrations for all database changes.

Never manually modify the production database schema.

### Rule 6

Use Form Requests for complex validation.

### Rule 7

Use Policies/Gates for authorization.

### Rule 8

Use transactions for inventory/order operations.

### Rule 9

Never hard-code exchange rates.

### Rule 10

Never expose China purchase cost to unauthorized users.

### Rule 11

Never delete important inventory/order history permanently.

Prefer soft deletes or historical records.

### Rule 12

Write tests for important business logic.

### Rule 13

Keep controllers thin.

### Rule 14

Keep pricing logic in `PricingService`.

### Rule 15

Keep inventory logic in `InventoryService`.

### Rule 16

Keep product searching in `ProductSearchService`.

---

# 67. Definition of Done

A feature is not considered complete until:

```text
Migration created
Model created
Relationships implemented
Validation implemented
Authorization implemented
Controller/action implemented
UI implemented
Error handling implemented
Success messages implemented
Tests written
```

Run:

```bash
php artisan test
```

before considering the feature complete.

---

# 68. Initial Commands

The AI agent should first verify the installed versions:

```bash
php -v
composer -V
node -v
npm -v
php artisan --version
```

If starting from scratch:

```bash
composer create-project laravel/laravel avs-store
cd avs-store
```

Then configure:

```text
.env
database
application name
```

Install frontend dependencies:

```bash
npm install
npm run build
```

Run migrations:

```bash
php artisan migrate
```

---

# 69. Final Target Workflow

The completed AVS system should allow this workflow:

```text
STAFF LOGIN
     ↓
DASHBOARD
     ↓
PRODUCT SEARCH
     ↓
SCAN BARCODE
     OR
ENTER ITEM NUMBER
     ↓
PRODUCT FOUND
     ↓
PRODUCT DETAILS
     ↓
CHINA PRICE
     ↓
CURRENT CNY → PKR RATE
     ↓
AUTOMATIC PKR COST
     ↓
WHOLESALE PRICE
     ↓
STOCK
     ↓
ADD TO CUSTOMER ORDER
```

Example:

```text
Barcode:
8901234567890

Product:
Butterfly Hair Accessories Organizer

China Price:
¥8.50

CNY → PKR:
39.00

Calculated Cost:
Rs.331.50

Wholesale:
Rs.390

Stock:
125 Pieces
```

The system must make this entire process fast enough for real shop-counter use.

---

# 70. Future Expansion

Design the application so these features can be added later without major restructuring:

```text
Mobile App
REST API
WhatsApp integration
Online payment
Customer WhatsApp notifications
Barcode label printing
Invoice printing
Thermal printer support
Bluetooth barcode scanner
Camera barcode scanner
Supplier management
Purchase orders
Purchase invoices
Multiple warehouses
Multiple branches
Multi-currency
Customer credit/ledger
Employee ledger
Shipping management
Delivery tracking
Advanced analytics
```

Do not implement these features prematurely.

Build the architecture so they can be added cleanly later.

---

# 71. AI Agent Working Instructions

When working on this project:

1. Inspect the existing code before changing it.
2. Explain the implementation plan briefly.
3. Make small, logically grouped changes.
4. Follow Laravel conventions.
5. Reuse existing components.
6. Do not duplicate code.
7. Keep business logic in services.
8. Add migrations before using new database fields.
9. Add tests for important calculations.
10. Run relevant tests after changes.
11. Fix errors before moving to the next feature.
12. Never remove working functionality without explicit instruction.
13. Do not expose internal CNY cost to public customers.
14. Do not hard-code exchange rates.
15. Keep barcode/item-number searching extremely fast.
16. Keep the UI responsive.
17. Use meaningful names.
18. Keep database relationships explicit.
19. Use transactions for financial/inventory operations.
20. Document important architectural decisions.

---

# 72. First Implementation Task

Start with the following modules only:

```text
1. Laravel project setup
2. Authentication
3. User roles
4. Categories
5. Products
6. Product images
7. Barcode
8. Item Number
9. Exchange Rates
10. CNY → PKR calculation
11. Product Search
12. Product Quick View
```

After these modules pass testing, continue with:

```text
Inventory
Customers
Orders
Public Storefront
Reports
```

Do not implement the complete system at once.

The primary acceptance test for the first milestone is:

```text
Given a product:

Item Number:
AVS-000125

Barcode:
8901234567890

China Price:
¥8.50

Current Exchange Rate:
39.00 PKR

When staff searches/scans the product,

the application must immediately display:

Product Name
Item Number
Barcode
China Price
Current Exchange Rate
Calculated PKR Cost
Wholesale Price
Stock
Product Image
```

This workflow is the core feature of the AVS Store system.
