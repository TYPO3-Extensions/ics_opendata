#
# Table structure for table 'tx_icsodcoreapi_logs'
#
CREATE TABLE tx_icsodcoreapi_logs (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	application int(11) DEFAULT '0' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	cmd varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_icsodcoreapi_month_logs'
#
CREATE TABLE tx_icsodcoreapi_month_logs (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	application int(11) DEFAULT '0' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	cmd varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_icsodcoreapi_statistics'
#
CREATE TABLE tx_icsodcoreapi_statistics (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	application int(11) DEFAULT '0' NOT NULL,
	cmd varchar(255) DEFAULT '' NOT NULL,
	count int(11) DEFAULT '0' NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY date_appli(date, application)

);



