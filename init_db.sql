CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    category_id INT REFERENCES categories(id),
    name TEXT NOT NULL,
    price NUMERIC(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES products(id),
    quantity int4 NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE TABLE stats (
    id SERIAL PRIMARY KEY,
    category_id INT REFERENCES categories(id),
    total_orders INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

INSERT INTO categories (name) VALUES ('Фрукты'), ('Овощи'), ('Напитки');
INSERT INTO products (category_id, name, price) VALUES
(1, 'Яблоко', 10.00),
(1, 'Апельсин', 12.50),
(2, 'Картофель', 5.00),
(2, 'Морковь', 4.50),
(3, 'Сок', 15.00);
