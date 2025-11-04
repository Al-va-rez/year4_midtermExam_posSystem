CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    is_cashier BOOLEAN,
    is_suspended BOOLEAN,
    password TEXT NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    img_src TEXT,
    item_name VARCHAR(50) NOT NULL UNIQUE,
    item_price DECIMAL(10, 2) NOT NULL,
    added_by VARCHAR(50) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cashier_username VARCHAR(50) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transaction_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    item_name VARCHAR(50) NOT NULL,
    item_price DECIMAL(10, 2) NOT NULL,
    item_quantity INT NOT NULL,
    item_subtotal DECIMAL(10, 2) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (id, username, firstname, lastname, is_cashier, is_suspended, password) VALUES (1, 'super1', 'viron', 'alvarez', 0, 0, '$2y$10$qHlpe0oAbITD/1HaAcnckOzar3.CeFb7BRjfZOF7nlndHPv7SOfV.');