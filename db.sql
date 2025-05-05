-- Create database
CREATE DATABASE Shopease;
USE Shopease;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    register_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    rating DECIMAL(3,1) DEFAULT 4.0,
    reviews INT DEFAULT 0
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL,
    review TEXT,
    review_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample products
INSERT INTO products (name, price, image, description, category, rating, reviews) VALUES
('Beef', 750, 'image/beef.jpeg', 'Beef is a rich source of high-quality protein and various vitamins and minerals. As such, it can be an excellent component of a healthy diet.', 'Meat', 4.5, 128),
('Chicken', 260, 'image/chicken.webp', 'Chicken meat is a versatile, lean protein source, rich in nutrients, commonly used in cuisines worldwide for its flavor and texture.', 'Meat', 4.2, 95),
('Mutton', 1100, 'image/mutton.jpeg', 'Mutton is the meat of a mature sheep, known for its rich flavor, tenderness, and use in various traditional dishes.', 'Meat', 4.1, 75),
('Deshi Duck', 560, 'image/duck.webp', 'Deshi duck meat is rich, flavorful, and lean, known for its tenderness, distinctive taste, and high nutritional value.', 'Meat', 4.4, 89);