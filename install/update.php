<?php
$registry = \Mega\Cls\core\registry::singleton();
$registry->set('administrator','core_version','Lion');
$orm = \Mega\Cls\Database\orm::singleton();
//add key for developers mode
$registry->newKey('administrator','devMode','1');

//edite forean key in registry table
//@$orm->exec('ALTER TABLE `registry` DROP FOREIGN KEY `fk_plugin`; ALTER TABLE `registry` ADD CONSTRAINT `fk_plugins` FOREIGN KEY (`plugin`) REFERENCES `import`.`plugins`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION;',[],NON_SELECT);
//@$orm->exec('ALTER TABLE `blocks` DROP FOREIGN KEY `plugin`; ALTER TABLE `blocks` ADD CONSTRAINT `plugins` FOREIGN KEY (`plugin`) REFERENCES `import`.`plugins`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION;',[],NON_SELECT);

?>
