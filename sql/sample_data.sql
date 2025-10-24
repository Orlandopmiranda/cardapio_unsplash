USE cardapio_unsplash;

INSERT INTO categories (name) VALUES
('Massas'), ('Carnes'), ('Saladas'), ('Sobremesas'), ('Bebidas');

INSERT INTO dishes (category_id, name, description, price, image_url) VALUES
(1, 'Spaghetti à Bolonhesa', 'Espaguete com molho bolonhesa e parmesão ralado.', 34.90, 'https://source.unsplash.com/400x300/?spaghetti,pasta'),
(1, 'Fettuccine Alfredo', 'Fettuccine cremoso ao molho Alfredo com queijo.', 36.50, 'https://source.unsplash.com/400x300/?fettuccine,pasta'),
(2, 'Picanha Grelhada', 'Picanha fatiada acompanhada de batatas rústicas.', 59.90, 'https://source.unsplash.com/400x300/?steak,meat'),
(2, 'Frango Assado', 'Frango temperado e assado lentamente.', 29.90, 'https://source.unsplash.com/400x300/?roast-chicken'),
(3, 'Salada Caesar', 'Alface, croutons, parmesão e molho caesar.', 22.00, 'https://source.unsplash.com/400x300/?salad,caesar'),
(3, 'Salada Caprese', 'Tomate, mussarela de búfala e manjericão.', 24.50, 'https://source.unsplash.com/400x300/?caprese,salad'),
(4, 'Tiramisu', 'Sobremesa italiana clássica com café e mascarpone.', 18.00, 'https://source.unsplash.com/400x300/?tiramisu,dessert'),
(4, 'Pudim de Leite', 'Pudim cremoso tradicional.', 12.00, 'https://source.unsplash.com/400x300/?pudding,dessert'),
(5, 'Café Expresso', 'Café expresso curto e intenso.', 6.50, 'https://source.unsplash.com/400x300/?espresso,coffee'),
(5, 'Suco Natural', 'Suco do dia, feito com frutas frescas.', 8.00, 'https://source.unsplash.com/400x300/?fresh-juice');


-- Admin user
INSERT INTO users (username, password) VALUES ('admin', '$2b$10$QNPwOzQzmu34OZ5uxei11uMbUqr2yhg0GqzHkBNz3XRYA3KKsydwy');
