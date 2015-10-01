-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 25, 2011 at 02:24 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `mbg_albums`
--

CREATE TABLE IF NOT EXISTS `mbg_albums` (
  `AlbumID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `AlbumName` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `DateCreated` int(20) NOT NULL,
  `Thumbnail1Size` varchar(10) DEFAULT NULL,
  `Thumbnail2Size` varchar(10) DEFAULT NULL,
  `Thumbnail3Size` varchar(10) DEFAULT NULL,
  `OrderID` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`AlbumID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mbg_albums`
--

