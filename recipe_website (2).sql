-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2025 at 04:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recipe_website`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_logs`
--

CREATE TABLE `ai_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `query` text NOT NULL,
  `response` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_logs`
--

INSERT INTO `ai_logs` (`log_id`, `user_id`, `query`, `response`, `created_at`) VALUES
(1, 1, 'How do I make pancakes fluffier?', 'Add more baking powder and whisk the batter well.', '2025-03-10 11:06:00'),
(2, 2, 'What’s a good substitute for bacon in carbonara?', 'Try using mushrooms or smoked tofu.', '2025-03-10 11:06:00'),
(3, 3, 'Can I make chocolate cake without eggs?', 'Yes, use applesauce or mashed bananas as a substitute.', '2025-03-10 11:06:00');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `user_id`, `recipe_id`, `comment`, `created_at`) VALUES
(4, 1, 3, 'Can I use dark chocolate instead of cocoa powder?', '2025-03-10 11:05:59'),
(5, 2, 5, 'delicious 😘❤️👍', '2025-03-10 20:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) DEFAULT NULL,
  `following_id` int(11) DEFAULT NULL,
  `followed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`follow_id`, `follower_id`, `following_id`, `followed_at`) VALUES
(3, 1, 3, '2025-03-10 11:12:14'),
(4, 2, 1, '2025-03-10 20:08:58');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `type` enum('like','comment','follow','rating') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'Jane Smith liked your Pancakes recipe.', 'like', 0, '2025-03-10 11:05:59'),
(2, 2, 'Alice Johnson commented on your Spaghetti Carbonara recipe.', 'comment', 0, '2025-03-10 11:05:59'),
(3, 3, 'John Doe rated your Chocolate Cake recipe 5 stars.', 'rating', 0, '2025-03-10 11:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `ratings_reviews`
--

CREATE TABLE `ratings_reviews` (
  `rating_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings_reviews`
--

INSERT INTO `ratings_reviews` (`rating_id`, `user_id`, `recipe_id`, `rating`, `review`, `created_at`) VALUES
(4, 1, 3, 5, 'Best chocolate cake I’ve ever had!', '2025-03-10 11:05:59'),
(5, 2, 5, 5, NULL, '2025-03-10 20:09:10');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `ingredients` text NOT NULL,
  `steps` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `prep_time` int(11) DEFAULT NULL,
  `cook_time` int(11) DEFAULT NULL,
  `servings` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `user_id`, `title`, `description`, `ingredients`, `steps`, `category_id`, `image`, `video`, `prep_time`, `cook_time`, `servings`, `created_at`) VALUES
(3, 4, 'Spaghetti Carbonara', 'Classic Italian pasta dish', 'Spaghetti, Eggs, Parmesan, Bacon, Garlic', 'Cook pasta, Fry bacon, Mix eggs and cheese, Combine all', 3, 'carbonara.jpg', NULL, 15, 20, 2, '2025-03-10 11:05:59'),
(4, 3, 'Chocolate Cake', 'Rich and moist chocolate cake', 'Flour, Sugar, Cocoa Powder, Eggs, Butter', 'Mix dry ingredients, Add wet ingredients, Bake at 350°F', 4, 'chocolate_cake.jpg', NULL, 20, 30, 8, '2025-03-10 11:05:59'),
(5, 1, 'BEEF PULAO', 'Beef Pulao is a flavorful and aromatic rice dish made with tender beef, fragrant basmati rice, and a blend of whole and ground spices. The beef is slow-cooked to extract rich flavors, then combined with sautéed onions, tomatoes, and yogurt before being simmered with rice in a flavorful beef broth. This hearty and comforting dish is perfect for special occasions and pairs well with raita or salad.', 'For Cooking Beef: beef (with bones for extra flavor)rn1 onion (sliced)rn1 tablespoon ginger-garlic pastern1 teaspoon saltrn1 teaspoon black pepperrn5 cups waterrnFor Pulao Base:rnrn2 cups basmati rice (washed &amp; soaked for 30 minutes)rn1 large onion (thinly sliced)rn1 tomato (chopped)rn2 green chilies (sliced)rn1/2 cup yogurtrn1 teaspoon cumin seedsrn1 teaspoon coriander powderrn1/2 teaspoon turmeric powderrn1/2 teaspoon garam masalarn1 bay leafrn2-3 clovesrn2-3 cardamom podsrn1 small cinnamon stickrn2 tablespoons ghee or oilrn3-4 cups beef broth (from the cooked beef)', 'Cook the Beef:rnrnIn a pot, add beef, sliced onion, ginger-garlic paste, salt, black pepper, and water.rnCook on medium heat for about 45 minutes to 1 hour until beef is tender.rnStrain the broth and keep it aside. Separate the beef pieces.rnPrepare the Pulao Base:rnrnIn a deep pan, heat ghee or oil and add cumin seeds, bay leaf, cloves, cardamom, and cinnamon.rnAdd sliced onions and fry until golden brown.rnAdd chopped tomatoes, green chilies, coriander powder, turmeric, and garam masala.rnCook until the tomatoes soften, then add yogurt and mix well.rnAdd the cooked beef pieces and sauté for a few minutes.rnCook the Rice:rnrnAdd the soaked and drained basmati rice to the pot and mix gently.rnPour in the beef broth (about 3-4 cups) and bring it to a boil.rnReduce the heat, cover, and let it cook on low heat for about 15-20 minutes until rice is fully cooked.rnFluff the rice with a fork and let it rest for 5 minutes.', 20, '1741589735_WhatsApp Image 2025-03-08 at 3.17.26 PM.jpeg', '1741589735_WhatsApp Video 2025-03-10 at 11.49.15 AM.mp4', 20, 30, 25, '2025-03-10 11:55:35'),
(6, 1, 'Chicken Karahi Recipe 🍗🔥', 'Chicken Karahi - A Flavorful &amp;amp; Spicy Delight! 🍗🔥rnChicken Karahi is a popular and aromatic Pakistani and North Indian dish known for its rich, spicy, and tangy flavors. Cooked in a traditional karahi (wok-like pan), this dish is made with tender chicken pieces simmered in a thick, flavorful tomato-based gravy, infused with a blend of aromatic spices, garlic, ginger, and green chilies.rnrnUnlike other curries, Chicken Karahi is prepared without excessive water, allowing the natural juices of the tomatoes and chicken to create a thick, delicious masala. Finished with a touch of butter, garam masala, and fresh coriander, this dish is a true delight for spice lovers.rnrnBest enjoyed with naan, chapati, or steamed rice, Chicken Karahi is a must-try for those who love bold and traditional flavors! 😋🔥', 'For Chicken Karahi:rn500g chicken (bone-in, cut into small pieces)rn3 medium tomatoes (chopped or blended)rn2 medium onions (finely sliced)rn4 cloves garlic (chopped)rn1-inch ginger (julienned)rn2 green chilies (sliced)rn½ cup yogurtrn½ cup oil or gheern1 teaspoon cumin seedsrn1 teaspoon coriander powderrn1 teaspoon red chili powderrn½ teaspoon turmeric powderrn1 teaspoon salt (adjust to taste)rn1 teaspoon garam masalarn1 teaspoon black pepperrn1 tablespoon butter (optional, for richness)rnFresh coriander (for garnish)rn', 'Step 1: Fry the ChickenrnHeat oil in a wok or deep pan. Add cumin seeds and sliced onions. Fry until golden brown.rnAdd garlic and sauté for 30 seconds.rnAdd chicken and fry on medium-high heat until it turns white and slightly golden.rnStep 2: Cook the Tomatoes &amp;amp; SpicesrnAdd chopped tomatoes, red chili powder, turmeric, coriander powder, and salt. Stir well.rnCover and cook on low heat for 10 minutes until tomatoes soften.rnMash the tomatoes and mix everything well.rnStep 3: Add Yogurt &amp;amp; SimmerrnAdd yogurt and mix well. Cook for another 5 minutes until oil separates.rnAdd black pepper, garam masala, green chilies, and butter (optional). Stir well.rnStep 4: Final Touch &amp;amp; GarnishingrnCook uncovered for 5 minutes on high heat to get the signature Karahi texture.rnGarnish with fresh coriander and julienned ginger.', 20, '1741617957_WhatsApp Image 2025-03-08 at 5.40.55 PM.jpeg', '1741617529_WhatsApp Video 2025-03-08 at 5.35.04 PM.mp4', 30, 45, 20, '2025-03-10 19:38:49'),
(7, 2, 'Daal Chawal😊🍛', 'Serve hot Dal over steamed rice.\\r\\nGarnish with ghee, lemon wedges, or fresh coriander.\\r\\nEnjoy with pickles, yogurt, or papad for extra flavor! 😊🍛', 'For Dal (Lentils)\\r\\n1 cup yellow lentils (Masoor Dal or Moong Dal)\\r\\n3 cups water\\r\\n1 medium onion (chopped)\\r\\n2 tomatoes (chopped)\\r\\n3 cloves garlic (chopped)\\r\\n1 teaspoon cumin seeds\\r\\n½ teaspoon turmeric powder\\r\\n1 teaspoon red chili powder\\r\\nSalt to taste\\r\\n2 tablespoons oil or ghee\\r\\nFresh coriander (for garnish)\\r\\nFor Chawal (Rice)\\r\\n1 cup basmati rice\\r\\n2 cups water\\r\\n½ teaspoon salt\\r\\n', 'Step 1: Cooking the Dal\\r\\nWash the lentils thoroughly and soak them for 15–20 minutes.\\r\\nIn a pot, heat oil and add cumin seeds. Let them splutter.\\r\\nAdd chopped onions and sauté until golden brown.\\r\\nAdd chopped garlic, tomatoes, turmeric, red chili powder, and salt. Cook until tomatoes are soft.\\r\\nAdd the soaked lentils and 3 cups of water. Stir well.\\r\\nCover and cook on low heat for 20–25 minutes until lentils are soft.\\r\\nMash slightly with a spoon and garnish with fresh coriander.', 20, '1741618818_WhatsApp Image 2025-03-08 at 5.06.20 PM.jpeg', '', 30, 45, 20, '2025-03-10 20:00:18');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_categories`
--

CREATE TABLE `recipe_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_categories`
--

INSERT INTO `recipe_categories` (`category_id`, `category_name`) VALUES
(1, 'Appetizer'),
(2, 'Beverages'),
(19, 'Breakfast'),
(22, 'Dessert'),
(21, 'Dinner'),
(5, 'Gluten-Free'),
(9, 'Healthy'),
(20, 'Lunch'),
(7, 'Salad'),
(6, 'Seafood'),
(23, 'Snacks'),
(8, 'Soup'),
(3, 'Vegan'),
(4, 'Vegetarian');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_reports`
--

CREATE TABLE `recipe_reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `reported_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipe_tags`
--

CREATE TABLE `recipe_tags` (
  `recipe_tag_id` int(11) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_tags`
--

INSERT INTO `recipe_tags` (`recipe_tag_id`, `recipe_id`, `tag_id`) VALUES
(3, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `recipe_views`
--

CREATE TABLE `recipe_views` (
  `view_id` int(11) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `viewed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_views`
--

INSERT INTO `recipe_views` (`view_id`, `recipe_id`, `user_id`, `ip_address`, `viewed_at`) VALUES
(3, 3, 1, '192.168.1.3', '2025-03-10 11:06:00');

-- --------------------------------------------------------

--
-- Table structure for table `saved_recipes`
--

CREATE TABLE `saved_recipes` (
  `saved_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `saved_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_recipes`
--

INSERT INTO `saved_recipes` (`saved_id`, `user_id`, `recipe_id`, `saved_at`) VALUES
(4, 2, 3, '2025-03-10 11:05:59'),
(7, 1, 7, '2025-03-10 20:10:24');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_name`) VALUES
(4, 'Dairy-Free'),
(3, 'Gluten-Free'),
(5, 'High-Protein'),
(2, 'Quick & Easy'),
(1, 'Vegetarian');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `profile_pic`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Ayesha kj', 'ayesha333111777@gmail.com', '$2y$10$JRWjV.h48Jb4bS83AMvlVOFDWc9d6guSrsiXy8XZQvB.GgZuTrJA6', 'WhatsApp Image 2025-01-22 at 8.54.51 PM.jpeg', 'user', '2025-03-10 10:28:12', '2025-03-10 10:28:30'),
(2, 'Arooj', 'aroojrashid123@gmail.com', '$2y$10$N8mN9hHaHNbLcnoVinFJL.7hdaCNh92ot1FcQXgGyG9zwQdZMoeOu', 'WhatsApp Image 2025-03-08 at 10.02.59 PM.jpeg', 'user', '2025-03-10 10:32:12', '2025-03-10 10:32:39'),
(3, 'John Doe', 'john.doe@example.com', 'password123', 'profile1.jpg', 'user', '2025-03-10 11:05:59', '2025-03-10 11:05:59'),
(4, 'Jane Smith', 'jane.smith@example.com', 'password456', 'profile2.jpg', 'admin', '2025-03-10 11:05:59', '2025-03-10 11:05:59'),
(5, 'Alice Johnson', 'alice.johnson@example.com', 'password789', 'profile3.jpg', 'user', '2025-03-10 11:05:59', '2025-03-10 11:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `preference_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `preference_key` varchar(255) NOT NULL,
  `preference_value` text NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_logs`
--
ALTER TABLE `ai_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`follow_id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings_reviews`
--
ALTER TABLE `ratings_reviews`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `unique_rating` (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `recipe_categories`
--
ALTER TABLE `recipe_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `recipe_reports`
--
ALTER TABLE `recipe_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipe_tags`
--
ALTER TABLE `recipe_tags`
  ADD PRIMARY KEY (`recipe_tag_id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `recipe_views`
--
ALTER TABLE `recipe_views`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `saved_recipes`
--
ALTER TABLE `saved_recipes`
  ADD PRIMARY KEY (`saved_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `tag_name` (`tag_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`preference_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_logs`
--
ALTER TABLE `ai_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ratings_reviews`
--
ALTER TABLE `ratings_reviews`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `recipe_categories`
--
ALTER TABLE `recipe_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `recipe_reports`
--
ALTER TABLE `recipe_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe_tags`
--
ALTER TABLE `recipe_tags`
  MODIFY `recipe_tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `recipe_views`
--
ALTER TABLE `recipe_views`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `saved_recipes`
--
ALTER TABLE `saved_recipes`
  MODIFY `saved_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_logs`
--
ALTER TABLE `ai_logs`
  ADD CONSTRAINT `ai_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings_reviews`
--
ALTER TABLE `ratings_reviews`
  ADD CONSTRAINT `ratings_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_reviews_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `recipe_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `recipe_reports`
--
ALTER TABLE `recipe_reports`
  ADD CONSTRAINT `recipe_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_reports_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_tags`
--
ALTER TABLE `recipe_tags`
  ADD CONSTRAINT `recipe_tags_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_views`
--
ALTER TABLE `recipe_views`
  ADD CONSTRAINT `recipe_views_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_recipes`
--
ALTER TABLE `saved_recipes`
  ADD CONSTRAINT `saved_recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_recipes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
