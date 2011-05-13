#
# Table structure for table 'tx_icsopendataapi_applications'
#
CREATE TABLE tx_icsopendataapi_applications (
	tx_userdatastore_categories text,
	tx_userdatastore_filegroups text
);

#
# Table structure for table 'tx_userdatastore_categories_applications_mm'
# 
#
CREATE TABLE tx_userdatastore_categories_applications_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_userdatastore_filegroups_applications_mm'
# 
#
CREATE TABLE tx_userdatastore_filegroups_applications_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);