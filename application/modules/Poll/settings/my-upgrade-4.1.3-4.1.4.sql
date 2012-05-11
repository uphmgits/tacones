
UPDATE IGNORE `engine4_core_settings`
SET `name` = 'poll.maxoptions'
WHERE `name` LIKE 'poll%.maxOptions' ;

UPDATE IGNORE `engine4_core_settings`
SET `name` = 'poll.showpiechart'
WHERE `name` LIKE 'poll%.showPieChart' ;

UPDATE IGNORE `engine4_core_settings`
SET `name` = 'poll.canchangevote'
WHERE `name` LIKE 'poll%.canChangeVote' ;

UPDATE IGNORE `engine4_core_settings`
SET `name` = 'poll.perpage'
WHERE `name` LIKE 'poll%.perPage' ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('mobi_browse_poll', 'poll', 'Polls', '', '{"route":"poll_general","action":"browse"}', 'mobi_browse', '', 6);