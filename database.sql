

CREATE DATABASE IF NOT EXISTS `food_order_db`;
USE `food_order_db`;

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `food_items`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `image_name` VARCHAR(255) DEFAULT 'default_cat.jpg',
  `featured` ENUM('Yes', 'No') DEFAULT 'Yes',
  `active` ENUM('Yes', 'No') DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `food_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image_name` VARCHAR(255) DEFAULT 'default_food.jpg',
  `category_id` INT NOT NULL,
  `featured` ENUM('Yes', 'No') DEFAULT 'No',
  `active` ENUM('Yes', 'No') DEFAULT 'Yes',
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `delivery_address` TEXT NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Cash on Delivery',
  `status` ENUM('Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled') DEFAULT 'Pending',
  `order_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `food_id` INT NOT NULL,
  `food_title` VARCHAR(150) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `qty` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`) VALUES
(1, 'System Administrator', 'admin@gmail.com', '0192023a7bbd73250516f069df18b500', '9800000000', 'Kathmandu, Nepal', 'admin'),
(2, 'Ram Bahadur', 'ram@gmail.com', '6ad14ba9986e3615423dfca256d04e3f', '9841234567', 'New Baneshwor, Kathmandu', 'customer');

INSERT INTO `categories` (`id`, `title`, `image_name`, `featured`, `active`) VALUES
(1, 'MoMo Special', 'https://images.unsplash.com/photo-1625220194771-7ebdea0b70b9?w=500&auto=format&fit=crop&q=80', 'Yes', 'Yes'),
(2, 'Pizzas', 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=500&auto=format&fit=crop&q=80', 'Yes', 'Yes'),
(3, 'Noodles & Chowmein', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=500&auto=format&fit=crop&q=80', 'Yes', 'Yes'),
(4, 'Beverages & Drinks', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&auto=format&fit=crop&q=80', 'Yes', 'Yes'),
(5, 'Burgers & Snacks', 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&auto=format&fit=crop&q=80', 'No', 'Yes');

INSERT INTO `food_items` (`id`, `title`, `description`, `price`, `image_name`, `category_id`, `featured`, `active`) VALUES
(1, 'Steam Chicken MoMo', 'Juicy minced chicken wrapped in delicate dough with Nepali tomato achar.', 240.00, 'https://images.unsplash.com/photo-1625220194771-7ebdea0b70b9?w=500&auto=format&fit=crop&q=80', 1, 'Yes', 'Yes'),
(2, 'C-MoMo (Chilli MoMo)', 'Crispy fried dumplings tossed in spicy tomato chilli gravy with bell peppers.', 280.00, 'https://images.unsplash.com/photo-1541696432-82c6da8ce7bf?w=500&auto=format&fit=crop&q=80', 1, 'Yes', 'Yes'),
(3, 'Buff Jhol MoMo', 'Authentic Kathmandu style dumplings served in piping hot tangy sesame-peanut soup.', 220.00, 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=500&auto=format&fit=crop&q=80', 1, 'Yes', 'Yes'),
(4, 'Loaded Chicken Pizza (Medium)', 'Topped with tender grilled chicken, mozzarella, mushrooms, capsicum and herbs.', 550.00, 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=500&auto=format&fit=crop&q=80', 2, 'Yes', 'Yes'),
(5, 'Double Cheese Margherita', 'Classic tomato sauce, fresh basil, double layer of melted mozzarella cheese.', 450.00, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=500&auto=format&fit=crop&q=80', 2, 'No', 'Yes'),
(6, 'Special Chicken Chowmein', 'Stir-fried noodles with sliced chicken breast, crunchy fresh vegetables & spices.', 210.00, 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=500&auto=format&fit=crop&q=80', 3, 'Yes', 'Yes'),
(7, 'Classic Cheese Burger', 'Juicy chicken patty, slice of cheddar cheese, fresh lettuce, tomato & house sauce.', 290.00, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&auto=format&fit=crop&q=80', 5, 'No', 'Yes'),
(8, 'Sweet Mango Lassi', 'Creamy homemade yogurt drink blended with sweet tropical mango pulp.', 120.00, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&auto=format&fit=crop&q=80', 4, 'No', 'Yes');

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `customer_name`, `customer_phone`, `customer_email`, `delivery_address`, `payment_method`, `status`, `order_date`) VALUES
(1, 2, 790.00, 'Ram Bahadur', '9841234567', 'ram@gmail.com', 'New Baneshwor, Kathmandu', 'Cash on Delivery', 'Delivered', NOW() - INTERVAL 2 DAY),
(2, 2, 480.00, 'Ram Bahadur', '9841234567', 'ram@gmail.com', 'New Baneshwor, Kathmandu', 'eSewa Mobile Wallet', 'Preparing', NOW() - INTERVAL 2 HOUR);

INSERT INTO `order_items` (`id`, `order_id`, `food_id`, `food_title`, `price`, `qty`, `total`) VALUES
(1, 1, 1, 'Steam Chicken MoMo', 240.00, 1, 240.00),
(2, 1, 4, 'Loaded Chicken Pizza (Medium)', 550.00, 1, 550.00),
(3, 2, 1, 'Steam Chicken MoMo', 240.00, 2, 480.00);
