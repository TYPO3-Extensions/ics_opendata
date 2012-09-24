#
# Table structure for table 'tx_icsodcategories_categories'
#
CREATE TABLE tx_icsodcategories_categories (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext,
	identifier varchar(31) DEFAULT '' NOT NULL,
	description text,
	parent int(11) DEFAULT '0' NOT NULL,
	relation int(11) DEFAULT '0' NOT NULL,
	picto text,
	
	UNIQUE (identifier),
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_icsodcategories_categories_relation_mm'
# 
#
CREATE TABLE tx_icsodcategories_categories_relation_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  sorting_foreign int(11) DEFAULT '0' NOT NULL,
  
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_icsoddatastore_filegroups'
#
CREATE TABLE tx_icsoddatastore_filegroups (
	tx_icsodcategories_categories int(11) DEFAULT '0' NOT NULL
);
#
# Table structure for table 'tx_icsodappstore_applications'
#
CREATE TABLE tx_icsodappstore_applications (
	tx_icsodcategories_categories int(11) DEFAULT '0' NOT NULL
);
