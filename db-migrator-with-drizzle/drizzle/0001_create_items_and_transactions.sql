CREATE TABLE `items` (
	`id` bigint unsigned AUTO_INCREMENT NOT NULL,
	`code` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`category` varchar(255) NOT NULL,
	`unit` varchar(50) NOT NULL,
	`stock` int NOT NULL DEFAULT 0,
	`created_by` bigint unsigned,
	`created_at` timestamp,
	`updated_by` bigint unsigned,
	`updated_at` timestamp,
	`deleted_by` bigint unsigned,
	`deleted_at` timestamp,
	CONSTRAINT `items_id` PRIMARY KEY(`id`),
	CONSTRAINT `items_code_unique` UNIQUE(`code`)
);
--> statement-breakpoint
CREATE TABLE `transactions` (
	`id` bigint unsigned AUTO_INCREMENT NOT NULL,
	`item_id` bigint unsigned NOT NULL,
	`user_id` bigint unsigned NOT NULL,
	`type` enum('in','out') NOT NULL,
	`quantity` int NOT NULL,
	`date` date NOT NULL,
	`notes` text,
	`created_at` timestamp,
	`updated_at` timestamp,
	CONSTRAINT `transactions_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
ALTER TABLE `items` ADD CONSTRAINT `items_created_by_users_id_fk` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE `items` ADD CONSTRAINT `items_updated_by_users_id_fk` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE `items` ADD CONSTRAINT `items_deleted_by_users_id_fk` FOREIGN KEY (`deleted_by`) REFERENCES `users`(`id`) ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE `transactions` ADD CONSTRAINT `transactions_item_id_items_id_fk` FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE `transactions` ADD CONSTRAINT `transactions_user_id_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE no action ON UPDATE no action;--> statement-breakpoint
CREATE INDEX `transactions_item_id_index` ON `transactions` (`item_id`);--> statement-breakpoint
CREATE INDEX `transactions_user_id_index` ON `transactions` (`user_id`);--> statement-breakpoint
CREATE INDEX `transactions_type_index` ON `transactions` (`type`);--> statement-breakpoint
CREATE INDEX `transactions_date_index` ON `transactions` (`date`);