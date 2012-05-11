
ALTER TABLE `engine4_poll_polls`
  ADD COLUMN `closed` tinyint(1) NOT NULL default '0'
  AFTER `search` ;
