SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `Amount` (
  `ResellerID` char(8) NOT NULL,
  `amount` double NOT NULL default '0',
  `opentime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` varchar(30) NOT NULL,
  `rest` float NOT NULL,
  `comment` varchar(255) NOT NULL,
  KEY `CustomerID` (`ResellerID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `Amount` (`ResellerID`, `amount`, `opentime`, `type`, `rest`, `comment`) VALUES
('2', 10000, '2009-02-01 04:00:00', 'push', 0, ''),
('5', 90000, '2009-02-02 04:00:00', 'push', 0, ''),
('8', 100000, '2009-02-03 04:00:00', 'push', 0, ''),
('6', 100000, '2009-02-04 04:00:00', 'push', 0, ''),
('6', 40000, '2009-02-05 04:00:00', 'push', 0, ''),
('4', 30000, '2009-02-06 04:00:00', 'push', 0, ''),
('9', 40000, '2009-02-07 04:00:00', 'push', 0, ''),
('4', 80000, '2009-02-08 04:00:00', 'push', 0, ''),
('2', 100000, '2009-02-09 04:00:00', 'push', 0, ''),
('8', 10000, '2009-02-10 04:00:00', 'push', 0, ''),
('0', 90000, '2009-02-11 04:00:00', 'push', 0, ''),
('4', 20000, '2009-02-12 04:00:00', 'push', 0, ''),
('4', 40000, '2009-02-13 04:00:00', 'push', 0, ''),
('2', 20000, '2009-02-14 04:00:00', 'push', 0, ''),
('1', 20000, '2009-02-15 04:00:00', 'push', 0, ''),
('7', 70000, '2009-02-16 04:00:00', 'push', 0, ''),
('1', 60000, '2009-02-17 04:00:00', 'push', 0, ''),
('9', 100000, '2009-02-18 04:00:00', 'push', 0, ''),
('8', 90000, '2009-02-19 04:00:00', 'push', 0, ''),
('5', 30000, '2009-02-20 04:00:00', 'push', 0, '');

CREATE TABLE IF NOT EXISTS `Contacts` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `msn` text NOT NULL,
  `icq` text NOT NULL,
  `jabber` text NOT NULL,
  `phone` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `Currency` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `symbol` tinytext NOT NULL,
  `desc` text NOT NULL,
  `rate` float NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

INSERT INTO `Currency` (`id`, `name`, `symbol`, `desc`, `rate`) VALUES
(1, 'eur', '', 'Euro', 1),
(2, 'usd', '$', 'Unites States Dollar', 1.4265),
(4, 'jpy', '', '', 115.7),
(5, 'bgn', '', '', 1.9558),
(6, 'czk', '', '', 24.585),
(7, 'dkk', '', '', 7.4562),
(8, 'ils', '', '', 4.9495),
(9, 'gbp', '', '', 0.86685),
(10, 'huf', '', '', 268.58),
(11, 'ltl', '', '', 3.4528),
(12, 'lvl', '', '', 0.7093),
(13, 'pln', '', '', 3.978),
(14, 'ron', '', '', 4.132),
(15, 'sek', '', '', 8.9108),
(16, 'chf', '', '', 1.2221),
(17, 'nok', '', '', 7.769),
(18, 'hrk', '', '', 7.4375),
(19, 'rub', '', '', 40.042),
(20, 'try', '', '', 2.2895),
(21, 'aud', '', '', 1.3346),
(22, 'brl', '', '', 2.2981),
(23, 'cad', '', '', 1.3925),
(24, 'cny', '', '', 9.2621),
(25, 'hkd', '', '', 11.1002),
(26, 'idr', '', '', 12224.6),
(27, 'inr', '', '', 64.428),
(28, 'krw', '', '', 1543.57),
(29, 'mxn', '', '', 16.6077),
(30, 'myr', '', '', 4.328),
(31, 'nzd', '', '', 1.7479),
(32, 'php', '', '', 61.77),
(33, 'sgd', '', '', 1.7632),
(34, 'thb', '', '', 43.323),
(35, 'zar', '', '', 9.8805);

CREATE TABLE IF NOT EXISTS `Department` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `type` enum('Public','Private') NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;


CREATE TABLE IF NOT EXISTS `GatewayModules` (
  `id` int(11) NOT NULL auto_increment,
  `modulename` tinytext NOT NULL,
  `currency` tinytext NOT NULL,
  `data` text NOT NULL,
  `status` binary(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;


CREATE TABLE IF NOT EXISTS `Invoice` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `accountid` int(11) NOT NULL,
  `orderid` int(11) NOT NULL default '-1',
  `amount` float NOT NULL,
  `status` enum('Unpaid','Paid','Cancelled') NOT NULL default 'Unpaid',
  `datecreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `datedue` timestamp NOT NULL default '0000-00-00 00:00:00',
  `datepaid` timestamp NOT NULL default '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  `transactionid` int(11) NOT NULL default '-1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;


CREATE TABLE IF NOT EXISTS `Lang` (
  `id` int(11) NOT NULL auto_increment,
  `code` tinytext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `Lang` (`id`, `code`) VALUES
(1, 'en'),
(2, 'ru');

CREATE TABLE IF NOT EXISTS `Notification` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `moduleid` int(11) NOT NULL,
  `subject` text NOT NULL,
  `text` text NOT NULL,
  `address` text NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `status` enum('Fail','Done') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=56 ;


CREATE TABLE IF NOT EXISTS `NotificationModule` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `shortname` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;


CREATE TABLE IF NOT EXISTS `NotifyModuleData` (
  `id` int(11) NOT NULL auto_increment,
  `moduleid` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

INSERT INTO `NotifyModuleData` (`id`, `moduleid`, `name`, `value`) VALUES
(1, 2, 'server', '127.0.0.1'),
(2, 2, 'adminname', '25'),
(3, 2, 'user', ''),
(4, 2, 'pass', ''),
(5, 2, 'crypto', ''),
(6, 6, 'server', 'mail.netdedicated.ru'),
(7, 6, 'adminname', '25'),
(8, 6, 'user', 'support@netdedicated.ru'),
(9, 6, 'pass', '645504'),
(10, 6, 'crypto', 'NONE');

CREATE TABLE IF NOT EXISTS `NotifyTemplate` (
  `id` int(11) NOT NULL auto_increment,
  `type` tinytext NOT NULL,
  `subject` text NOT NULL,
  `text` text NOT NULL,
  `desc` text NOT NULL,
  `langcode` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

INSERT INTO `NotifyTemplate` (`id`, `type`, `subject`, `text`, `desc`, `langcode`) VALUES
(1, 'userorderaccepted', 'Your order accepted!', 'Hello, {USER.username}!<br>You your for {PKG.name} successfully accepted! If you already paid invoice #{INV.id} then we will deliver your order shortly!<br><br>Sincerely,<br>{COMPANYNAME}', '', 'en'),
(2, 'adminnewuser', 'New user registered', 'Hello,<br>New user registered in {SYSTEM.name}<br>Date: {TIME}<br>Username: {USER.name}<br><br>Best regards', '', 'en'),
(3, 'usernewuser', 'Thank you for registration!', 'Hello,<br>You have been succefully registered at {SYSTEM.name} with username {USER.name}<br><br>Best regards,', '', 'en'),
(4, 'userneworder', 'New order placed for your account', 'Hello,<br>This is notification about new order placed for your account at {SYSTEM.name}<br><br>Order information:<br>Order num: #{ORDER.id}<br>Date placed: {ORDER.datecreated}<br>Ordered item: {PKG.name}<br>Invoice: #{INV.id} for {INV.amount}<br><br>Best regards,', '', 'en'),
(5, 'usernewinvoice', 'New invoice notification', 'Hello,<br>This is notification about new invoice at {SYSTEM.name}<br><br>Invoice information:<br>Invoice num: #{INV.id}<br>Total due: {INV.totaldue}<br>Due date: {INV.duedate}<br><br>Please, consider paying this invoice before due date at http://{SYSTEM.domain}/invoice.php?id={INV.id}<br><br>Best regards', '', 'en'),
(6, 'usernewticket', 'New ticket submitted', 'Hello,<br>New ticket has been submitted for your account at "SYSTEM.name"<br><br>Ticket num: #{TICKET.id}<br>Subject: {TICKET.subject}<br>Submitter: {USER.username}<br>You can login at http://{SYSTEM.domain} and overview this ticket<br><br>Best regards', '', 'en'),
(7, 'usernewticketreply', 'New ticket reply received', 'Hello,<br>This is notification about new reply to ticket {TICKET.id}<br>Reply by: {USER.username}<br>Reply text:<br>{TC.text}<br><br>Best regards', '', 'en'),
(8, 'adminneworder', 'New order placed', 'Hello,<br>This is notification about new order.<br><br>Order information:<br>Order num: #{ORDER.id}<br>Ordered item: {PKG.name}<br>Order period: {ORDER.cycle}<br>Order type: {ORDER.type}<br>Invoice num: #{INV.id}<br>Total amount: {INV.amount}<br>Customer: {USER.username}<br><br>Best regards ', '', 'en'),
(9, 'adminnewticket', 'New ticket submitted', 'Hello,<br>New ticket has been submitted<br><br>Ticket num: #{TICKET.id}<br>Subject: {TICKET.subject}<br>Submitter: {USER.username}<br><br>Best regards', '', 'en'),
(10, 'adminnewticketreply', 'New ticket reply received', 'Hello,<br>This is notification about new reply to ticket {TICKET.id}<br>Reply by: {USER.username}<br>Reply text:<br>{TC.text}<br><br>Best regards', '', 'en'),
(11, 'dailyreport', 'Daily billing report', 'Hello,<br>Daily report for {DATE}<br><br>New orders today: {NEWORDERSTODAY}<br>New invoices created: {NEWINVSNUM}<br>Overdue orders: {OVERDUENUM}<br>Open tickets: {OPENTICKETSNUM}<br>Tickets waiting for reply: {TICKETSFORREPLYNUM}<br><br>Automatically suspended orders: {SUSPENDEDORDERS}<br>Orders requires manual suspension:<br>{ORDERSTOSUSPENDED}<br><br>Automatically terminated orders: {TERMINATEDORDERS}<br>\r\nOrders requires manual termination:<br>{ORDERSTOTERMINATE}<br><br>Regards', '', 'en'),
(12, 'usernewpayment', 'Invoice payment confirmation', 'Dear {USER.username},\r\n<p>This is a payment receipt for Invoice {INV.id} sent on {INV.datecreated}</p>\r\n<p>{INV.comment}</p>\r\n<p>Amount: {INV.amount}<br>Transaction #: {INV.transactionid}<br>Status: {INV.status}</p>\r\n<p>You may review your invoice history at any time by logging in to your client area.</p>\r\n<p>Note: This email will serve as an official receipt for this payment.</p>', '', 'en'),
(13, 'adminservicesetuperror', 'Error during automatic service setup', 'Hi,<br>We catched fatal error when tried to setup new account after receiving new payment for it.<br>Account details:<br>User #: {USER.id}<br>Username: {USER.username}<br><br>Invoice details:<br>Invoice #: {INV.id}<br>Amount: {INV.amount}<br>Transaction #: {INV.transactionid}<br><br>Error message:<br>{MESSAGE}<br><br>Error debug information:<br>{DEBUG}', '', 'en'),
(14, 'usernewservicedetails', 'Ordered service details', 'Dear {USER.username}!<br><br>Services you ordered successfully provided for you and service access details can be found below.<br><br>Order information:<br>Order #: {ORDER.id}<br>Status: {ORDER.status}<br>Order date: {ORDER.orderdate}<br>Last invoice#: {ORDER.lastinv}, {LASTINV.status}<br>Ordered product: {PRODUCT.name}<br><br>Ordered product access data:<br>{ACCESSDATA}<br><br>Regards,<br>', '', 'en');

CREATE TABLE IF NOT EXISTS `Order` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `accountid` mediumint(9) NOT NULL,
  `productid` mediumint(9) NOT NULL,
  `status` enum('Pending','Active','Suspended','Terminated') NOT NULL default 'Pending',
  `cycle` tinyint(4) NOT NULL default '1',
  `orderdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `lastinv` mediumint(9) NOT NULL default '0',
  `nextdue` timestamp NOT NULL default '0000-00-00 00:00:00',
  `firstamount` float NOT NULL,
  `recuramount` float NOT NULL,
  `serverid` int(11) NOT NULL default '-1',
  `accessdata` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;


CREATE TABLE IF NOT EXISTS `Package` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `presetid` mediumint(9) NOT NULL,
  `price` float NOT NULL default '0',
  `paytype` enum('Free','Onetime','Recurring') NOT NULL,
  `stock` mediumint(9) NOT NULL default '-1',
  `desc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;


CREATE TABLE IF NOT EXISTS `PkgGroups` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `type` tinytext NOT NULL,
  `status` enum('Open','Closed') NOT NULL default 'Open',
  `gateways` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `Preset` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `groupid` mediumint(9) NOT NULL,
  `paramsdata` text NOT NULL,
  `status` binary(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;


CREATE TABLE IF NOT EXISTS `Profile` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `surname` tinytext NOT NULL,
  `sex` enum('M','F') NOT NULL,
  `phone` tinytext NOT NULL,
  `im` tinytext NOT NULL,
  `country` tinytext NOT NULL,
  `address` tinytext NOT NULL,
  `city` tinytext NOT NULL,
  `postcode` tinytext NOT NULL,
  `company` tinytext NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;


CREATE TABLE IF NOT EXISTS `ServerGroups` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `moduleid` mediumint(9) NOT NULL,
  `status` binary(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;


CREATE TABLE IF NOT EXISTS `ServerModule` (
  `id` mediumint(9) NOT NULL auto_increment,
  `modulename` tinytext NOT NULL,
  `status` binary(1) NOT NULL default '1',
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;


CREATE TABLE IF NOT EXISTS `Servers` (
  `ServerID` mediumint(30) NOT NULL auto_increment,
  `servergroupid` mediumint(9) NOT NULL,
  `servername` tinytext NOT NULL,
  `maxclients` smallint(5) unsigned default NULL,
  `autofill` binary(1) NOT NULL default '1',
  `accessdata` text NOT NULL,
  `status` binary(1) NOT NULL default '1',
  PRIMARY KEY  (`ServerID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=57 ;


CREATE TABLE IF NOT EXISTS `Settings` (
  `parameter` tinytext NOT NULL,
  `value` tinytext NOT NULL,
  `userid` int(11) NOT NULL default '-1' COMMENT '-1 means global'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `Settings` (`parameter`, `value`, `userid`) VALUES
('system.currency', 'eur', -1),
('system.currency.autoupdate', 'ecb', -1),
('system.paygateway.default', '6', -1),
('system.cron.autosuspend', '1', -1),
('system.cron.autoterminate', '1', -1),
('system.cron.daystosuspend', '4', -1),
('system.cron.daystoterminate', '15', -1),
('system.cron.daystonewinv', '15', -1),
('system.lang.default', 'ru', -1),
('system.notifymodule.default', '7', -1);

CREATE TABLE IF NOT EXISTS `TempOrder` (
  `id` int(11) NOT NULL auto_increment,
  `data` text NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;


CREATE TABLE IF NOT EXISTS `Ticket` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `depid` text NOT NULL,
  `subject` text NOT NULL,
  `status` enum('Customer','Support','Closed','Hold','Progress') NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

INSERT INTO `Ticket` (`id`, `userid`, `depid`, `subject`, `status`, `date`) VALUES
(3, 0, '', '', 'Support', '2011-01-03 09:30:11');

CREATE TABLE IF NOT EXISTS `TicketChange` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('status','reply') NOT NULL,
  `ticketid` int(11) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

INSERT INTO `TicketChange` (`id`, `userid`, `message`, `type`, `ticketid`, `date`) VALUES
(4, 5, 'Ñ‚ÐµÑÑ‚', 'reply', 3, '2011-01-03 09:30:11');

CREATE TABLE IF NOT EXISTS `Transaction` (
  `id` int(11) NOT NULL auto_increment,
  `invoiceid` int(11) NOT NULL default '-1',
  `customerid` int(11) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `amount` float NOT NULL default '0',
  `gatewayid` int(11) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;


CREATE TABLE IF NOT EXISTS `User` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `username` char(32) NOT NULL,
  `password` tinytext NOT NULL,
  `email` tinytext NOT NULL,
  `opentime` timestamp NULL default CURRENT_TIMESTAMP,
  `info` mediumtext NOT NULL,
  `status` enum('Active','Suspend','Admin') NOT NULL default 'Active',
  `lastlogin` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

INSERT INTO `User` (`id`, `username`, `password`, `email`, `opentime`, `info`, `status`, `lastlogin`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'admin@multicabinet.ru', '2011-05-13 10:00:08', 'administrator', 'Admin', '0000-00-00 00:00:00');

CREATE TABLE IF NOT EXISTS `UserSettings` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `parameter` tinytext NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;
