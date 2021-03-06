CREATE TABLE `SITE_DB`.`project_department_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,

  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `project_department_products_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `SITE_DB`.`project_departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_department_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `SITE_DB`.`items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
