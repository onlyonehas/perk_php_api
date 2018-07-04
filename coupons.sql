CREATE DATABASE IF NOT EXISTS discounts;

CREATE TABLE IF NOT EXISTS `brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

INSERT INTO `brand` (`id`,`name`) 
VALUES
(1,'Tesco'),
(2,'Sainsburys'),
(3,'Asda'),
(4,'JD'),
(5,'SportDirect');

CREATE TABLE IF NOT EXISTS `coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_code` varchar(256) NOT NULL,
  `value` int(11)  NOT NULL,
  `brand_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

INSERT INTO `coupon` (`coupon_code`, `value`, `brand_id`) VALUES
('RANDOM12', 50,1),
('JUNE20OFF',20,2),
('JUNE30OFF',30,2),
('VIP30OFF',30,3),
('EXCLUS1VE',25,4),
('UN1QUE',10,5);

