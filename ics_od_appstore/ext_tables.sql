#
# Table structure for table 'tx_icsodappstore_applications'
#
CREATE TABLE tx_icsodappstore_applications (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	fe_cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	apikey varchar(15) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text,
	platform varchar(255) DEFAULT '' NOT NULL,
	platforms int(11) DEFAULT '0' NOT NULL
	countcall int(11) DEFAULT '0' NOT NULL,
	maxcall int(11) DEFAULT '0' NOT NULL,
	release_date int(11) DEFAULT '0' NOT NULL,
	logo text,
	screenshot text,
	link varchar(255) DEFAULT '' NOT NULL,
	update_date int(11) DEFAULT '0' NOT NULL,
	lock_publication int(11) DEFAULT '0' NOT NULL,
	publish tinyint(4) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
