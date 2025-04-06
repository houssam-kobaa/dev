-- =============================================
-- PIZZA DELIVERY DATABASE SCHEMA (MySQL)
-- =============================================

-- Clear existing tables (if any)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS addresses;
DROP TABLE IF EXISTS customers;

-- =============================================
-- 1. CUSTOMERS TABLE
-- =============================================
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) UNIQUE,
    join_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    loyalty_points INT DEFAULT 0,
    CONSTRAINT chk_phone CHECK (phone REGEXP '^[0-9]{3}-[0-9]{3}-[0-9]{4}$')
) ENGINE=InnoDB;

-- =============================================
-- 2. ADDRESSES TABLE
-- =============================================
CREATE TABLE addresses (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    street VARCHAR(100) NOT NULL,
    city VARCHAR(50) NOT NULL,
    state CHAR(2) NOT NULL,
    zip_code VARCHAR(10) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    delivery_instructions TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    CONSTRAINT chk_zip CHECK (zip_code REGEXP '^[0-9]{5}(-[0-9]{4})?$')
) ENGINE=InnoDB;

-- =============================================
-- 3. MENU ITEMS TABLE
-- =============================================
CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('Pizza', 'Side', 'Drink', 'Dessert') NOT NULL,
    size ENUM('Small', 'Medium', 'Large', 'One Size') DEFAULT 'One Size',
    price DECIMAL(6,2) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    preparation_time INT COMMENT 'Time in minutes',
    UNIQUE KEY (name, size),
    CONSTRAINT chk_price CHECK (price > 0)
) ENGINE=InnoDB;

-- =============================================
-- 4. ORDERS TABLE
-- =============================================
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    address_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    delivery_date DATETIME,
    status ENUM('Received', 'Preparing', 'Baking', 'Out for Delivery', 'Delivered', 'Cancelled') DEFAULT 'Received',
    payment_type ENUM('Cash', 'Credit Card', 'Online Payment'),
    total_amount DECIMAL(8,2),
    driver_notes TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (address_id) REFERENCES addresses(address_id),
    INDEX (status),
    INDEX (order_date)
) ENGINE=InnoDB;

-- =============================================
-- 5. ORDER ITEMS TABLE (Junction Table)
-- =============================================
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    special_requests TEXT,
    item_price DECIMAL(6,2) NOT NULL COMMENT 'Price at time of order',
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id),
    CONSTRAINT chk_quantity CHECK (quantity > 0)
) ENGINE=InnoDB;

-- =============================================
-- TRIGGER: Calculate order total
-- =============================================
DELIMITER //
CREATE TRIGGER update_order_total
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders o
    SET total_amount = (
        SELECT SUM(quantity * item_price)
        FROM order_items
        WHERE order_id = NEW.order_id
    )
    WHERE o.order_id = NEW.order_id;
END//
DELIMITER ;