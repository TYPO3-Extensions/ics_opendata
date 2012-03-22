#
# Table structure for table 'tx_icsodstoresrel_apps_filegroups_mm'
# 
#
CREATE TABLE tx_icsodstoresrel_apps_filegroups_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_icsodappstore_applications'
#
CREATE TABLE tx_icsodappstore_applications (
	tx_icsodstoresrel_filegroup int(11) DEFAULT '0' NOT NULL
);