#
# Table structure for table 'tx_icsopendataapi_applications'
#
CREATE TABLE tx_icsopendataapi_applications (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	fe_cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	key_appli varchar(15) DEFAULT '' NOT NULL,
	application varchar(255) DEFAULT '' NOT NULL,
	description text,
	platform varchar(255) DEFAULT '' NOT NULL,
	count_use int(11) DEFAULT '0' NOT NULL,
	max int(11) DEFAULT '0' NOT NULL,
	publication_date int(11) DEFAULT '0' NOT NULL,
	publish tinyint(4) DEFAULT '0' NOT NULL,
	logo text,
	screenshot text,
	link varchar(255) DEFAULT '' NOT NULL,
	update_date int(11) DEFAULT '0' NOT NULL,
	lock_publication int(11) DEFAULT '0' NOT NULL,
#	categories text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_icsopendataapi_logs'
#
CREATE TABLE tx_icsopendataapi_logs (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	tx_icsopendataapi_application int(11) DEFAULT '0' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	cmd varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_icsopendataapi_month_logs'
#
CREATE TABLE tx_icsopendataapi_month_logs (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	tx_icsopendataapi_application int(11) DEFAULT '0' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	cmd varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_icsopendataapi_statistics'
#
CREATE TABLE tx_icsopendataapi_statistics (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	tx_icsopendataapi_application int(11) DEFAULT '0' NOT NULL,
	cmd varchar(255) DEFAULT '' NOT NULL,
	count int(11) DEFAULT '0' NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY date_appli(date, tx_icsopendataapi_application)
);

#
# Table structure for table 'tx_icsopendataapi_categories_parent_mm'
# 
#
#CREATE TABLE tx_icsopendataapi_categories_parent_mm (
#  uid_local int(11) DEFAULT '0' NOT NULL,
#  uid_foreign int(11) DEFAULT '0' NOT NULL,
#  tablenames varchar(30) DEFAULT '' NOT NULL,
#  sorting int(11) DEFAULT '0' NOT NULL,
#  KEY uid_local (uid_local),
#  KEY uid_foreign (uid_foreign)
#);


#
# Table structure for table 'tx_icsopendataapi_categories'
#
#CREATE TABLE tx_icsopendataapi_categories (
#	uid int(11) NOT NULL auto_increment,
#	pid int(11) DEFAULT '0' NOT NULL,
#	tstamp int(11) DEFAULT '0' NOT NULL,
#	crdate int(11) DEFAULT '0' NOT NULL,
#	cruser_id int(11) DEFAULT '0' NOT NULL,
#	deleted tinyint(4) DEFAULT '0' NOT NULL,
#	hidden tinyint(4) DEFAULT '0' NOT NULL,
#	name varchar(255) DEFAULT '' NOT NULL,
#	description text,
#	parent int(11) DEFAULT '0' NOT NULL,
#	
#	PRIMARY KEY (uid),
#	KEY parent (pid)
#);