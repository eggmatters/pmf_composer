-- CREATE SCHEMA `pmf_db` DEFAULT CHARACTER SET utf8 ;
CREATE TABLE `pmf_db`.`users`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(45) NOT NULL,
  `user_role` VARCHAR(45) NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`));
CREATE TABLE `pmf_db`.`posts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_title` VARCHAR(250) NOT NULL,
  `post_body` TEXT NOT NULL,
  `users_id` INT NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  INDEX `fk_posts_users_idx` (`users_id` ASC),
  CONSTRAINT `fk_posts_users`
    FOREIGN KEY (`users_id`)
    REFERENCES `pmf_db`.`users` (`id`)
    ON DELETE NO ACTION);
CREATE TABLE `pmf_db`.`tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tag_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`));
CREATE TABLE `pmf_db`.`posts_tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `posts_id` INT NOT NULL,
  `tags_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_posts_tags_posts_idx` (`posts_id` ASC),
  INDEX `fk_posts_tags_tags_idx` (`tags_id` ASC),
  CONSTRAINT `fk_posts_tags_posts`
    FOREIGN KEY (`posts_id`)
    REFERENCES `pmf_db`.`posts` (`id`)
    ON DELETE NO ACTION,
  CONSTRAINT `fk_posts_tags_tags`
    FOREIGN KEY (`tags_id`)
    REFERENCES `pmf_db`.`tags` (`id`)
    ON DELETE NO ACTION);
INSERT INTO users (user_name, user_role, created_at, updated_at) values
('steve','mopper',now(),now()),('becky','picker-upper',now(),now()),('mike','sweeper',now(),now());
INSERT INTO posts (post_title, post_body, users_id, created_at, updated_at) values
("Steve's post", "I mop up things",1,now(),now()),
("Becky's post", "I pick up things",2,now(),now()),
("Mike's post", "I sweep up things",3,now(),now());
insert into tags (tag_name) values ('cleaning-tips'),('value-adds'),('stubborn-stains');
insert into posts_tags (posts_id, tags_id) values
(1,1),(1,2),(1,3),(2,1),(2,2),(2,3),(3,1),(3,2),(3,3);