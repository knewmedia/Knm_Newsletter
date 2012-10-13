<?php

$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE `{$this->getTable('newsletter_subscriber')}`
    	ADD `prefix` VARCHAR( 25 ) NULL,
    	ADD `firstname` VARCHAR( 50 ) NULL,
    	ADD `lastname` VARCHAR( 50 ) NULL,
		ADD `dob` DATE NULL,
		ADD `recommender_firstname` VARCHAR( 50 ) NULL,
        ADD `recommender_lastname` VARCHAR( 50 ) NULL,
        ADD `recommendation_message` VARCHAR( 2048 ) NULL
		;");
$installer->run("
    ALTER TABLE `{$installer->getTable('newsletter_template')}` ADD `template_html` TEXT NOT NULL;
");
$installer->run("
    ALTER TABLE `{$installer->getTable('newsletter_queue')}` ADD `newsletter_html` TEXT NOT NULL;
");
$installer->endSetup();
