-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2023 at 10:20 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodclover`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `dish_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('unconfirmed','confirmed') DEFAULT 'unconfirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `allergies` set('No Allergies','Celery','Gluten','Eggs','Fish','Lupin','Milk','Molluscs','Mustard','Peanuts','Sesame','Soybeans','Sulfites','Sulphites','Tree nuts') DEFAULT NULL,
  `age` date DEFAULT NULL,
  `usertype` varchar(255) NOT NULL DEFAULT 'customer',
  `two_step_verification` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `delivery_date` date NOT NULL,
  `delivery_frequency` enum('daily','weekly','monthly') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `invoice_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `delivery_status` enum('pending','completed','not made') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`delivery_id`, `supplier_id`, `ingredient_id`, `delivery_date`, `delivery_frequency`, `price`, `invoice_number`, `quantity`, `delivery_status`) VALUES
(1, 1, 1, '2023-04-17', 'weekly', '20.00', 'INV-001', 20, 'not made'),
(2, 2, 2, '2023-04-16', 'weekly', '15.00', 'INV-002', 20, 'completed'),
(3, 3, 3, '2023-04-03', 'weekly', '30.00', 'INV-003', 14, 'completed'),
(4, 4, 4, '2023-04-01', 'weekly', '25.00', 'INV-004', 20, 'completed'),
(5, 5, 5, '2023-04-02', 'monthly', '50.00', 'INV-005', 10, 'completed'),
(6, 1, 6, '2023-04-03', 'weekly', '25.00', 'INV-006', 14, 'completed'),
(7, 4, 7, '2023-04-01', 'monthly', '18.00', 'INV-007', 20, 'not made'),
(8, 2, 8, '2023-04-02', 'monthly', '22.00', 'INV-008', 15, 'completed'),
(9, 1, 9, '2023-04-03', 'weekly', '30.00', 'INV-009', 14, 'completed'),
(10, 4, 10, '2023-04-01', 'weekly', '18.00', 'INV-010', 20, 'completed'),
(11, 2, 11, '2023-04-02', 'monthly', '22.00', 'INV-011', 15, 'completed'),
(12, 3, 12, '2023-04-03', 'daily', '35.00', 'INV-012', 14, 'completed'),
(13, 1, 13, '2023-04-01', 'daily', '40.00', 'INV-013', 20, 'completed'),
(14, 4, 14, '2023-04-02', 'daily', '30.00', 'INV-014', 15, 'completed'),
(15, 5, 15, '2023-04-03', 'weekly', '45.00', 'INV-015', 14, 'not made'),
(16, 3, 16, '2023-04-01', 'monthly', '25.00', 'INV-016', 20, 'not made'),
(17, 1, 17, '2023-04-09', 'weekly', '20.00', 'INV-017', 20, 'completed'),
(18, 2, 18, '2023-04-08', 'weekly', '15.00', 'INV-018', 15, 'completed'),
(19, 4, 19, '2023-04-07', 'weekly', '30.00', 'INV-019', 14, 'completed'),
(20, 5, 20, '2023-04-09', 'monthly', '25.00', 'INV-020', 20, 'not made'),
(21, 3, 21, '2023-04-08', 'monthly', '50.00', 'INV-021', 10, 'not made'),
(22, 1, 22, '2023-05-01', 'weekly', '25.00', 'INV-022', 14, 'not made'),
(23, 4, 23, '2023-04-09', 'monthly', '18.00', 'INV-023', 20, 'completed'),
(24, 2, 24, '2023-04-08', 'monthly', '22.00', 'INV-024', 15, 'completed'),
(25, 5, 25, '2023-04-07', 'weekly', '30.00', 'INV-025', 14, 'completed');

--
-- Triggers `delivery`
--
DELIMITER $$
CREATE TRIGGER `delivery_invoice_trigger` BEFORE INSERT ON `delivery` FOR EACH ROW BEGIN
  DECLARE next_invoice_num INT;
  SET @prefix = 'INV-';
  SET @max_invoice_num = (SELECT MAX(SUBSTR(invoice_number, LENGTH(@prefix) + 1)) FROM delivery WHERE SUBSTR(invoice_number, 1, LENGTH(@prefix)) = @prefix);
  IF @max_invoice_num IS NULL THEN
    SET next_invoice_num = 1;
  ELSE
    SET next_invoice_num = @max_invoice_num + 1;
  END IF;
  SET NEW.invoice_number = CONCAT(@prefix, LPAD(next_invoice_num, 3, '0'));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_inventory` AFTER UPDATE ON `delivery` FOR EACH ROW BEGIN
    IF OLD.delivery_status = 'pending' AND NEW.delivery_status = 'completed' THEN
        UPDATE ingredients
        SET inventory_stock = inventory_stock + NEW.quantity
        WHERE ingredient_id = NEW.ingredient_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dishes`
--

CREATE TABLE `dishes` (
  `dish_id` int(11) NOT NULL,
  `dish_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `kitchen_section` varchar(255) DEFAULT NULL,
  `allergies` set('No Allergies','Celery','Gluten','Eggs','Fish','Lupin','Milk','Molluscs','Mustard','Peanuts','Sesame','Soybeans','Sulfites','Sulphites','Tree nuts') DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` text DEFAULT NULL,
  `spicy_level` varchar(255) DEFAULT NULL,
  `average_orders_per_day` int(11) NOT NULL DEFAULT 0,
  `category_order` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL DEFAULT '',
  `estimated_prep_time` int(11) NOT NULL DEFAULT 0,
  `estimated_eating_time` int(11) NOT NULL DEFAULT 0,
  `estimated_total_time` int(11) NOT NULL DEFAULT 0,
  `course_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dishes`
--

INSERT INTO `dishes` (`dish_id`, `dish_name`, `description`, `kitchen_section`, `allergies`, `price`, `category`, `spicy_level`, `average_orders_per_day`, `category_order`, `image_path`, `estimated_prep_time`, `estimated_eating_time`, `estimated_total_time`, `course_order`) VALUES
(1, 'Samosas', 'Our samosas are a popular and delicious appetizer that is perfect for sharing or as a light meal.  Served with a tangy tamarind chutney, our samosas are a flavorful and satisfying treat that will leave you wanting more.\r\n\r\n', 'Kitchen', 'Gluten,Eggs', '6.99', 'Starters', NULL, 48, 1, '../../assets/images/Samosa.jpg', 10, 15, 25, 1),
(2, 'Paneer tikka', 'Paneer tikka is a popular vegetarian dish from India. It consists of marinated pieces of paneer (cottage cheese) cooked in a tandoor oven. The dish is known for its smoky flavor and soft texture.', 'Kitchen', 'Milk', '7.99', 'Starters', NULL, 48, 1, '../../assets/images/Paneer_tikka.jpg', 10, 15, 25, 1),
(3, 'Aloo tikki', 'Aloo tikki: A popular Indian snack made from mashed potatoes and spices, shaped into patties and fried until crispy on the outside and soft on the inside. Served with chutneys and toppings.', 'Kitchen', 'Gluten,Milk,Mustard', '6.99', 'Starters', NULL, 60, 1, '../../assets/images/Aloo_tikki.jpg', 10, 15, 25, 1),
(4, 'Papdi chaat', 'Papdi Chaat is a popular Indian street food that consists of crispy fried dough wafers topped with potatoes, chickpeas, yogurt, and chutney. It\'s a sweet and tangy snack that\'s perfect for sharing.', 'Kitchen', 'Gluten,Peanuts,Soybeans', '8.99', 'Starters', NULL, 24, 1, '../../assets/images/Papdi_chaat.jpg', 10, 15, 25, 1),
(5, 'Chana masala', 'Chana masala is a vegetarian Indian dish made with chickpeas cooked in a tomato-based sauce, with a blend of spices. It is typically served with rice or naan bread and makes for a flavorful and hearty meal.', 'Kitchen', 'Gluten,Mustard,Sulfites', '12.99', 'Vegetarian dishes', NULL, 48, 2, '../../assets/images/Chana_masala.jpg', 15, 20, 35, 2),
(6, 'Saag paneer', 'Saag paneer is a classic Indian dish made with spinach and cubes of paneer cheese, cooked in a flavorful blend of spices and cream. It\'s a vegetarian option that\'s rich, creamy, and packed with nutrients.', 'Kitchen', 'Milk', '14.99', 'Vegetarian dishes', NULL, 48, 2, '../../assets/images/Saag_paneer.jpg', 15, 20, 35, 2),
(7, 'Baingan bharta', 'Baingan Bharta is a traditional Indian vegetarian dish made with roasted eggplants, onions, tomatoes, and spices. It has a smoky flavor and creamy texture that melts in your mouth, making it a favorite among vegetarians.', 'Kitchen', 'No Allergies', '11.99', 'Vegetarian dishes', NULL, 36, 2, '../../assets/images/Baingan_bharta.jpg', 15, 20, 35, 2),
(8, 'Malai kofta', 'Malai kofta is a classic North Indian dish made of paneer and potato balls simmered in a rich and creamy tomato-based gravy. It is mildly spiced and has a slightly sweet taste. It is often served with naan or rice.', 'Kitchen', 'Gluten,Eggs,Milk,Soybeans,Tree nuts', '13.99', 'Vegetarian dishes', NULL, 48, 2, '../../assets/images/Malai_kofta.jpg', 15, 20, 35, 2),
(9, 'Chicken tikka masala', 'Chicken tikka masala is a popular Indian dish made with marinated chicken cooked in a creamy tomato-based sauce. It is typically flavored with a blend of spices, such as cumin, coriander, and garam masala.', 'Kitchen', 'Celery,Gluten,Mustard,Tree nuts', '16.99', 'Non-vegetarian dishes', NULL, 72, 3, '../../assets/images/Chicken_tikka_masala.jpg', 15, 20, 35, 2),
(10, 'Rogan josh', 'Rogan Josh is a popular Kashmiri dish made with tender lamb, braised with a rich and aromatic sauce of yogurt, onions, and spices such as cinnamon, and cloves. The dish is known for its deep red color and creamy texture.', 'Kitchen', 'Gluten,Milk,Sulfites', '19.99', 'Non-vegetarian dishes', NULL, 48, 3, '../../assets/images/Rogan_josh.jpg', 15, 20, 35, 2),
(11, 'Fish curry', 'Fish curry is a popular Indian dish made with fish cooked in a flavorful and aromatic gravy. The gravy typically contains a mix of spices, onions, tomatoes, and coconut milk. It is served with rice or bread and is a delicious meal.', 'Kitchen', 'Gluten,Fish', '18.99', 'Non-vegetarian dishes', NULL, 60, 3, '../../assets/images/fish_curry.jpg', 15, 20, 35, 2),
(12, 'Butter chicken', 'Butter chicken is a popular Indian dish made with tender chicken pieces cooked in a creamy tomato-based sauce, flavored with aromatic spices. It is usually served with rice or naan bread.', 'Kitchen', 'Gluten,Milk', '17.99', 'Non-vegetarian dishes', NULL, 60, 3, '../../assets/images/Butter_chicken.jpg', 15, 20, 35, 2),
(13, 'Naan', 'Naan is a leavened, oven-baked flatbread that is a staple of Indian cuisine. It is typically made with flour, yeast, salt, and water, and can be flavored with various ingredients like garlic, butter, or herbs. ', 'Kitchen', 'Gluten', '2.99', 'Sides', NULL, 120, 4, '../../assets/images/naan.jpg', 5, 10, 15, 3),
(14, 'Jeera rice', 'Jeera Rice is a popular Indian dish made by cooking basmati rice with cumin seeds and other spices. The dish is simple yet flavorful and makes a perfect accompaniment to various curries and gravies. ', 'Kitchen', 'No Allergies', '4.99', 'Sides', NULL, 90, 4, '../../assets/images/Jeera_rice.jpg', 5, 10, 15, 3),
(15, 'Sweet lassi', 'A sweet yogurt-based drink.', 'Bar', 'Milk,Tree nuts', '3.99', 'Non-Alcoholic drink', NULL, 60, 5, '../../assets/images/Sweet_lassi.jfif', 5, 10, 15, 3),
(16, 'Masala chai', 'A spiced tea with milk and sugar.', 'Bar', 'Milk,Soybeans,Tree nuts', '2.99', 'Non-Alcoholic drink', NULL, 60, 5, '../../assets/images/Masala_chai.jpg', 2, 0, 2, 4),
(17, 'Nimbu pani', 'A refreshing lemonade.', 'Bar', 'No Allergies', '1.99', 'Non-Alcoholic drink', NULL, 60, 5, '../../assets/images/Nimbu_pani.jfif', 2, 0, 2, 4),
(18, 'Thums Up', 'An Indian cola with a bold flavor.', 'Bar', 'No Allergies', '2.49', 'Non-Alcoholic drink', NULL, 60, 5, '../../assets/images/Thums_Up.jpg', 2, 0, 2, 4),
(19, 'Old Monk rum', 'A dark rum from India', 'Bar', 'No Allergies', '8.99', 'Alcoholic drink', NULL, 30, 6, '../../assets/images/Old_Monk_rum.jpg', 2, 0, 2, 4),
(20, 'Kingfisher Ultra beer', 'A light beer from India', 'Bar', 'Sulfites', '6.99', 'Alcoholic drink', NULL, 30, 6, '../../assets/images/Kingfisher_Ultra_beer.jpg', 2, 0, 2, 4),
(21, 'Mango margarita', 'A sweet and tangy margarita made with mango puree', 'Bar', 'Gluten,Sulfites,Tree nuts', '9.99', 'Alcoholic drink', NULL, 12, 6, '../../assets/images/Mango_margarita.jpg', 2, 0, 2, 4),
(22, 'Indian summer', 'An cocktail made with gin, lemon juice, and soda water', 'Bar', 'No Allergies', '11.99', 'Alcoholic drink', NULL, 18, 6, '../../assets/images/Indian_summer.jpg', 2, 0, 2, 4);

--
-- Triggers `dishes`
--
DELIMITER $$
CREATE TRIGGER `set_category_order` BEFORE INSERT ON `dishes` FOR EACH ROW BEGIN
  IF NEW.category = 'Starters' THEN
    SET NEW.category_order = 1;
  ELSEIF NEW.category = 'Vegetarian dishes' THEN
    SET NEW.category_order = 2;
  ELSEIF NEW.category = 'Non-vegetarian dishes' THEN
    SET NEW.category_order = 3;
  ELSEIF NEW.category = 'Sides' THEN
    SET NEW.category_order = 4;
  ELSEIF NEW.category = 'Alcoholic drink' THEN
    SET NEW.category_order = 5;
  ELSEIF NEW.category = 'Non-Alcoholic drink' THEN
    SET NEW.category_order = 6;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dish_ingredients`
--

CREATE TABLE `dish_ingredients` (
  `dish_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `quantity_needed_per_order` decimal(10,3) NOT NULL DEFAULT 0.000,
  `measurement_per_order` varchar(255) NOT NULL DEFAULT '',
  `quantity_needed_per_day` decimal(10,2) NOT NULL,
  `measurement_per_day` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dish_ingredients`
--

INSERT INTO `dish_ingredients` (`dish_id`, `ingredient_id`, `quantity_needed_per_order`, `measurement_per_order`, `quantity_needed_per_day`, `measurement_per_day`) VALUES
(1, 1, '0.190', 'kg', '1.50', 'kg'),
(1, 2, '0.060', 'kg', '0.50', 'kg'),
(1, 3, '0.130', 'kg', '1.00', 'kg'),
(1, 4, '0.130', 'L', '1.00', 'L'),
(2, 4, '0.130', 'L', '1.00', 'L'),
(2, 5, '0.190', 'kg', '1.50', 'kg'),
(2, 6, '0.060', 'kg', '0.50', 'kg'),
(2, 7, '0.130', 'kg', '1.00', 'kg'),
(3, 1, '0.200', 'kg', '2.00', 'kg'),
(3, 2, '0.050', 'kg ', '0.50', 'kg'),
(3, 3, '0.050', 'kg', '0.50', 'kg'),
(3, 4, '0.100', 'L', '1.00', 'L'),
(4, 4, '0.250', 'L', '1.00', 'L'),
(4, 7, '0.250', 'kg', '1.00', 'kg'),
(4, 8, '0.250', 'kg', '1.00', 'kg'),
(4, 9, '0.250', 'L', '1.00', 'L'),
(4, 10, '0.005', 'kg', '0.05', 'kg'),
(4, 11, '0.005', 'kg', '0.05', 'kg'),
(4, 12, '0.005', 'kg', '0.05', 'kg'),
(5, 4, '0.130', 'L', '1.00', 'L'),
(5, 7, '0.130', 'kg', '1.00', 'kg'),
(5, 8, '0.190', 'kg', '1.50', 'kg'),
(5, 10, '0.013', 'kg', '0.10', 'kg'),
(5, 11, '0.006', 'kg', '0.05', 'kg'),
(5, 12, '0.006', 'kg', '0.05', 'kg'),
(5, 13, '0.130', 'kg', '1.00', 'kg'),
(6, 4, '0.130', 'L', '1.00', 'L'),
(6, 7, '0.130', 'kg', '1.00', 'kg'),
(6, 8, '0.130', 'kg', '1.00', 'kg'),
(6, 10, '0.013', 'kg', '0.10', 'kg'),
(6, 11, '0.006', 'kg', '0.05', 'kg'),
(6, 12, '0.006', 'kg', '0.05', 'kg'),
(6, 14, '0.190', 'kg', '1.50', 'kg'),
(7, 1, '0.250', 'kg', '1.50', 'kg'),
(7, 3, '0.008', 'kg', '0.50', 'kg'),
(7, 4, '0.170', 'L', '1.00', 'L'),
(7, 5, '0.250', 'kg', '1.50', 'kg'),
(7, 7, '0.080', 'kg', '0.50', 'kg'),
(7, 10, '0.017', 'kg', '0.10', 'kg'),
(7, 11, '0.008', 'kg', '0.05', 'kg'),
(7, 12, '0.008', 'kg', '0.05', 'kg'),
(7, 15, '0.170', 'L', '1.00', 'L'),
(8, 3, '0.190', 'kg', '1.50', 'kg'),
(8, 4, '0.190', 'L', '1.50', 'L'),
(8, 6, '0.060', 'kg', '0.50', 'kg'),
(8, 8, '0.130', 'L', '1.00', 'L'),
(8, 9, '0.130', 'L', '1.00', 'L'),
(8, 10, '0.060', 'kg', '0.50', 'kg'),
(8, 11, '0.013', 'kg', '0.10', 'kg'),
(8, 12, '0.006', 'kg', '0.05', 'kg'),
(8, 13, '0.006', 'kg', '0.05', 'kg'),
(8, 14, '0.006', 'kg', '0.05', 'kg'),
(9, 5, '0.170', 'kg', '2.00', 'kg'),
(9, 7, '0.250', 'L', '3.00', 'L'),
(9, 9, '0.080', 'L', '1.00', 'L'),
(9, 11, '0.008', 'kg', '0.10', 'kg'),
(9, 12, '0.004', 'kg', '0.05', 'kg'),
(9, 13, '0.004', 'kg', '0.05', 'kg'),
(9, 14, '0.004', 'kg', '0.05', 'kg'),
(9, 15, '0.500', 'L', '6.00', 'L'),
(10, 5, '0.130', 'kg', '1.00', 'kg'),
(10, 9, '0.130', 'L', '1.00', 'L'),
(10, 10, '0.130', 'kg', '1.00', 'kg'),
(10, 11, '0.013', 'kg', '0.10', 'kg'),
(10, 12, '0.006', 'kg', '0.05', 'kg'),
(10, 13, '0.006', 'kg', '0.05', 'kg'),
(10, 14, '0.005', 'kg', '0.05', 'kg'),
(10, 16, '0.750', 'L', '6.00', 'L'),
(11, 4, '0.120', 'L', '1.20', 'L'),
(11, 7, '0.180', 'kg', '1.80', 'kg'),
(11, 11, '0.180', 'kg', '1.80', 'kg'),
(11, 13, '0.750', 'kg', '7.50', 'kg'),
(11, 20, '0.030', 'kg', '0.30', 'kg'),
(12, 1, '0.600', 'kg', '6.00', 'kg'),
(12, 2, '0.050', 'kg', '0.50', 'kg'),
(12, 3, '0.005', 'kg', '0.05', 'kg'),
(12, 4, '0.100', 'L', '1.00', 'L'),
(12, 5, '0.020', 'kg', '0.20', 'kg'),
(12, 6, '0.020', 'kg', '0.20', 'kg'),
(12, 7, '0.200', 'kg', '2.00', 'kg'),
(12, 8, '0.005', 'kg', '0.05', 'kg'),
(12, 9, '0.005', 'kg', '0.05', 'kg'),
(12, 10, '0.005', 'L', '0.05', 'L'),
(13, 3, '0.150', 'kg', '3.00', 'kg'),
(13, 4, '0.013', 'L', '0.25', 'L'),
(13, 11, '0.005', 'kg', '0.10', 'kg'),
(13, 12, '0.050', 'L', '1.00', 'L'),
(13, 13, '0.003', 'kg', '0.05', 'kg'),
(13, 14, '0.003', 'kg', '0.05', 'kg'),
(14, 4, '0.030', 'L', '0.50', 'L'),
(14, 10, '0.003', 'kg', '0.05', 'kg'),
(14, 15, '0.100', 'L', '1.50', 'kg');

-- --------------------------------------------------------

--
-- Table structure for table `employers`
--

CREATE TABLE `employers` (
  `id` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `age` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `role` enum('admin','admin+','manager','staff') NOT NULL,
  `insurance_number` varchar(50) NOT NULL,
  `hired_date` date NOT NULL,
  `days_worked` int(11) DEFAULT 0,
  `usertype` varchar(255) NOT NULL DEFAULT 'employer',
  `two_step_verification` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employers`
--

INSERT INTO `employers` (`id`, `fullName`, `age`, `email`, `pwd`, `phone`, `address`, `role`, `insurance_number`, `hired_date`, `days_worked`, `usertype`, `two_step_verification`) VALUES
(1, 'AdminSurya', '2023-04-04', 'vikrant@hotmail.nl', '$2y$10$unFP0hq6kwldK4C35ZaqEuWaz./WaqksUQ19TV47mDn/2yMZckMFa', '07306144934', 'Any Address', 'admin+', ' QQ 123456 C', '2023-04-18', 6, 'employer', '2SL5Z');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `verification_number` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `usertype` varchar(255) NOT NULL DEFAULT 'guest',
  `account_status` int(11) NOT NULL DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `inventory_stock` decimal(10,2) NOT NULL,
  `measure_unit` varchar(20) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `status` varchar(255) GENERATED ALWAYS AS (case when `inventory_stock` <= 10.00 then 'Urgent' else 'Enough' end) STORED,
  `availability` enum('In use','Not in use') DEFAULT 'In use'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `ingredient_name`, `inventory_stock`, `measure_unit`, `supplier_id`, `supplier_name`, `expiry_date`, `category`, `availability`) VALUES
(1, 'Potatoes', '110.00', 'Kilograms', 1, 'ABC Supplier', '2023-04-05', 'Vegetables', 'In use'),
(2, 'Peas', '120.00', 'Kilograms', 2, 'XYZ Supplier', '0000-00-00', 'Vegetables', 'In use'),
(3, 'Flour', '42.00', 'Kilograms', 3, 'MNO Supplier', '0000-00-00', 'Baking', 'In use'),
(4, 'Oil', '60.00', 'Litre', 4, 'PQR Supplier', '0000-00-00', 'Cooking', 'In use'),
(5, 'Cottage cheese', '30.00', 'Kilograms', 5, 'LMN Supplier', '0000-00-00', 'Dairy', 'In use'),
(6, 'Spinach', '42.00', 'Kilograms', 1, 'ABC Supplier', '0000-00-00', 'Vegetables', 'In use'),
(7, 'Tomatoes', '60.00', 'Kilograms', 4, 'PQR Supplier', '0000-00-00', 'Vegetables', 'In use'),
(8, 'Chickpeas', '45.00', 'Kilograms', 2, 'XYZ Supplier', '0000-00-00', 'Legumes', 'In use'),
(9, 'Eggplant', '42.00', 'Kilograms', 1, 'ABC Supplier', '0000-00-00', 'Vegetables', 'In use'),
(10, 'Carrots', '60.00', 'Kilograms', 4, 'PQR Supplier', '0000-00-00', 'Vegetables', 'In use'),
(11, 'Onions', '45.00', 'Kilograms', 2, 'XYZ Supplier', '0000-00-00', 'Vegetables', 'In use'),
(12, 'Lamb', '42.00', 'Kilograms', 3, 'MNO Supplier', '0000-00-00', 'Meat', 'In use'),
(13, 'Fish', '60.00', 'Kilograms', 1, 'ABC Supplier', '0000-00-00', 'Seafood', 'In use'),
(14, 'Chicken', '45.00', 'Kilograms', 4, 'PQR Supplier', '0000-00-00', 'Poultry', 'In use'),
(15, 'Cream', '28.00', 'Litre', 5, 'LMN Supplier', '0000-00-00', 'Dairy', 'In use'),
(16, 'Yogurt', '40.00', 'Litre', 3, 'MNO Supplier', '0000-00-00', 'Dairy', 'In use'),
(17, 'Cumin seeds', '60.00', 'Kilograms', 1, 'ABC Supplier', '0000-00-00', 'Spices', 'In use'),
(18, 'Basmati rice', '45.00', 'Kilograms', 2, 'XYZ Supplier', '0000-00-00', 'Cooking', 'In use'),
(19, 'Coriander powder', '42.00', 'Kilograms', 4, 'PQR Supplier', '0000-00-00', 'Spices', 'In use'),
(20, 'Turmeric powder', '40.00', 'Kilograms', 5, 'LMN Supplier', '0000-00-00', 'Spices', 'In use'),
(21, 'Garam masala', '20.00', 'Kilograms', 3, 'MNO Supplier', '0000-00-00', 'Spices', 'In use'),
(22, 'Mustard oil', '28.00', 'Litre', 1, 'ABC Supplier', '0000-00-00', 'Cooking', 'In use'),
(23, 'Mango puree', '60.00', 'Litre', 4, 'PQR Supplier', '0000-00-00', 'Fruits', 'In use'),
(24, 'Tequila', '30.00', 'Litre', 2, 'XYZ Supplier', '0000-00-00', 'Alcoholic Beverages', 'In use'),
(25, 'Gin', '42.00', 'Litre', 5, 'LMN Supplier', '0000-00-00', 'Alcoholic Beverages', 'In use');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `cart_id` int(11) NOT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `table_number` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `dish_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `order_status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `priority` enum('high priority','low priority') DEFAULT 'low priority',
  `kitchen_status` enum('new','in progress','completed') DEFAULT 'new',
  `time_due` time NOT NULL,
  `course_order` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `table_number` int(11) DEFAULT NULL,
  `num_guests` int(11) NOT NULL,
  `max_capacity` int(11) NOT NULL DEFAULT 8,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `status` enum('Pending','Arrived','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `reservations`
--
DELIMITER $$
CREATE TRIGGER `after_reservation_completed` AFTER UPDATE ON `reservations` FOR EACH ROW BEGIN
    -- Check if the status is being updated to 'completed'
    IF NEW.status = 'completed' THEN
        -- Update the account_status to 1 in the guests table
        UPDATE guests
        SET account_status = 1
        WHERE guest_id = NEW.guest_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_verification_code` AFTER UPDATE ON `reservations` FOR EACH ROW BEGIN
  IF NEW.status = 'Completed' THEN
    UPDATE reservations
    JOIN restaurant_tables ON reservations.table_number = restaurant_tables.table_number
    SET restaurant_tables.verification_code = FLOOR(RAND() * 9000 + 1000)
    WHERE reservations.reservation_id = NEW.reservation_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reset_password`
--

CREATE TABLE `reset_password` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `table_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `verification_code` varchar(20) NOT NULL,
  `last_used` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`table_id`, `table_number`, `is_available`, `verification_code`, `last_used`) VALUES
(1, 1, 1, '2300', '2023-05-21 16:04:50'),
(2, 2, 1, '3226', '2023-05-16 17:17:09'),
(3, 3, 1, '3302', '2023-05-15 02:37:44'),
(4, 4, 1, '2601', '2023-05-15 14:36:20'),
(5, 5, 1, '1020', '2023-05-15 14:37:27'),
(6, 6, 1, '1220', '2023-04-23 15:26:54'),
(7, 7, 1, '3603', '2023-05-17 13:03:14'),
(8, 8, 1, '3749', '2023-05-17 06:38:06'),
(9, 9, 1, '1220', '2023-04-23 15:26:54'),
(10, 10, 1, '1220', '2023-04-23 15:26:54');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `delivery_frequency` enum('daily','weekly','monthly') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `address`, `phone`, `email`, `delivery_frequency`) VALUES
(1, 'ABC Supplier', '123 Main St, London SW1A 1AA', '020 123 4567', 'info@abcsupplier.com', 'weekly'),
(2, 'XYZ Supplier', '456 Park Ave, London, SW1A 3AA', '020 456 7890', 'info@xyzsupplier.com', 'weekly'),
(3, 'MNO Supplier', '789 High St, London, SW1A 2AA', '020 789 1234', 'info@mnosupplier.com', 'weekly'),
(4, 'PQR Supplier', '234 Oxford St, London, SW1A 4BA', '020 234 5678', 'info@pqrsupplier.com', 'weekly'),
(5, 'LMN Supplier', '567 Regent St, London, SW1B 5AA', '020 567 8901', 'info@lmnsupplier.com', 'weekly');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`,`guest_id`,`reservation_id`,`dish_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `dish_id` (`dish_id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `table_number` (`table_number`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `ingredient_id` (`ingredient_id`),
  ADD KEY `delivery_id` (`delivery_id`);

--
-- Indexes for table `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`dish_id`),
  ADD KEY `dish_id` (`dish_id`);

--
-- Indexes for table `dish_ingredients`
--
ALTER TABLE `dish_ingredients`
  ADD PRIMARY KEY (`dish_id`,`ingredient_id`),
  ADD KEY `ingredient_id` (`ingredient_id`),
  ADD KEY `dish_id` (`dish_id`);

--
-- Indexes for table `employers`
--
ALTER TABLE `employers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`),
  ADD KEY `fk_supplier_id` (`supplier_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `dish_id` (`dish_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `reservation_id` (`reservation_id`,`customer_id`,`guest_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `table_number` (`table_number`);

--
-- Indexes for table `reset_password`
--
ALTER TABLE `reset_password`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `table_number_2` (`table_number`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `table_number` (`table_number`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `dishes`
--
ALTER TABLE `dishes`
  MODIFY `dish_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `employers`
--
ALTER TABLE `employers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `reset_password`
--
ALTER TABLE `reset_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`dish_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`),
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`dish_id`),
  ADD CONSTRAINT `orders_ibfk_5` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`),
  ADD CONSTRAINT `orders_ibfk_6` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `reservations_ibfk_5` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`),
  ADD CONSTRAINT `reservations_ibfk_7` FOREIGN KEY (`table_number`) REFERENCES `restaurant_tables` (`table_number`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
