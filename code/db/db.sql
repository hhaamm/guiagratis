SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `guiagratis` DEFAULT CHARACTER SET utf8 ;
USE `guiagratis` ;

-- -----------------------------------------------------
-- Table `guiagratis`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `guiagratis`.`users` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `mail` VARCHAR(255) NULL ,
  `password` VARCHAR(255) NULL ,
  `username` VARCHAR(45) NULL ,
  `register_token` VARCHAR(255) NULL ,
  `activated` TINYINT(1) NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `guiagratis`.`exchange_states`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `guiagratis`.`exchange_states` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `detail` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `guiagratis`.`exchange_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `guiagratis`.`exchange_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `detail` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `guiagratis`.`exchanges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `guiagratis`.`exchanges` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NULL ,
  `lat` FLOAT NULL ,
  `lng` FLOAT NULL ,
  `description` TEXT NULL ,
  `user_id` BIGINT NOT NULL ,
  `exchange_state_id` INT NOT NULL ,
  `created` TIMESTAMP NULL ,
  `exchange_type_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_offers_users` (`user_id` ASC) ,
  INDEX `fk_offers_offer_states1` (`exchange_state_id` ASC) ,
  INDEX `fk_exchanges_exchange_types1` (`exchange_type_id` ASC) ,
  CONSTRAINT `fk_offers_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `guiagratis`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_offers_offer_states1`
    FOREIGN KEY (`exchange_state_id` )
    REFERENCES `guiagratis`.`exchange_states` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_exchanges_exchange_types1`
    FOREIGN KEY (`exchange_type_id` )
    REFERENCES `guiagratis`.`exchange_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `guiagratis`.`exchange_photos`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `guiagratis`.`exchange_photos` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `url` VARCHAR(45) NULL ,
  `exchange_id` BIGINT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_offer_photos_offers1` (`exchange_id` ASC) ,
  CONSTRAINT `fk_offer_photos_offers1`
    FOREIGN KEY (`exchange_id` )
    REFERENCES `guiagratis`.`exchanges` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `guiagratis`.`offer_comments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `guiagratis`.`offer_comments` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `comment` TEXT NULL ,
  `offer_id` BIGINT NOT NULL ,
  `user_id` BIGINT NOT NULL ,
  `created` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_offer_comments_offers1` (`offer_id` ASC) ,
  INDEX `fk_offer_comments_users1` (`user_id` ASC) ,
  CONSTRAINT `fk_offer_comments_offers1`
    FOREIGN KEY (`offer_id` )
    REFERENCES `guiagratis`.`exchanges` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_offer_comments_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `guiagratis`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `guiagratis`.`private_conversations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `guiagratis`.`private_conversations` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `created` TIMESTAMP NULL ,
  `title` VARCHAR(255) NULL ,
  `user_1_id` BIGINT NOT NULL ,
  `users_2_id` BIGINT NOT NULL ,
  `exchange_id` BIGINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_private_conversations_users1` (`user_1_id` ASC) ,
  INDEX `fk_private_conversations_users2` (`users_2_id` ASC) ,
  INDEX `fk_private_conversations_exchanges1` (`exchange_id` ASC) ,
  CONSTRAINT `fk_private_conversations_users1`
    FOREIGN KEY (`user_1_id` )
    REFERENCES `guiagratis`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_private_conversations_users2`
    FOREIGN KEY (`users_2_id` )
    REFERENCES `guiagratis`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_private_conversations_exchanges1`
    FOREIGN KEY (`exchange_id` )
    REFERENCES `guiagratis`.`exchanges` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `guiagratis`.`private_messages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `guiagratis`.`private_messages` (
  `id` BIGINT NOT NULL AUTO_INCREMENT ,
  `message` TEXT NULL ,
  `created` TIMESTAMP NULL ,
  `private_conversation_id` BIGINT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_private_messages_private_conversations1` (`private_conversation_id` ASC) ,
  CONSTRAINT `fk_private_messages_private_conversations1`
    FOREIGN KEY (`private_conversation_id` )
    REFERENCES `guiagratis`.`private_conversations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
