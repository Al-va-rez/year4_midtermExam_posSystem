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

INSERT INTO users (id, username, firstname, lastname, is_cashier, is_suspended, password) VALUES (1, 'super1', 'viron', 'alvarez', 0, 0, '$2y$10$qHlpe0oAbITD/1HaAcnckOzar3.CeFb7BRjfZOF7nlndHPv7SOfV.');