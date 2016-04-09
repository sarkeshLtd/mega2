<?php
namespace apps\forum;
use Mega\Cls\Core as core;
use Mega\Cls\Database as db;
class setup{
	use addons;
	use \Mega\Apps\Administrator\addons;
	/*
	 * function for setup plugin
	 */
	public function install(){
        $orm = db\orm::singleton();
        $query = "CREATE TABLE IF NOT EXISTS `forums_forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `des` text NOT NULL,
  `rank` int(11) NOT NULL,
  `localize` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;";
        @$orm->exec($query,[],NON_SELECT);

        $query = "CREATE TABLE IF NOT EXISTS `forums_replays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body` text NOT NULL,
  `publishDate` int(11) NOT NULL,
  `username` int(11) NOT NULL,
  `topic` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;";
        @$orm->exec($query,[],NON_SELECT);

        $query = "CREATE TABLE IF NOT EXISTS `forums_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `body` text NOT NULL,
  `publishDate` int(11) NOT NULL,
  `forum` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;";
        @$orm->exec($query,[],NON_SELECT);

        //save registry keys
        $registry =  core\registry::singleton();
        $registry->newKey('forum','widgetNumReplays','5');
        $registry->newKey('forum','postNumInHome','3');
        $registry->newKey('forum','postNumInForum','20');
        $registry->newKey('forum','widgetNumTopics','5');

        //install widgets
        $this->installWidget('forum','lastReplays','Last replays in forum');
        $this->installWidget('forum','lastTopics','Last topics in forum');
    }
}
