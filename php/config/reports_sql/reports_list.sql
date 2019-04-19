USE `oxmosys_db`;

/*!40000 ALTER TABLE `app_reports` DISABLE KEYS */;
INSERT INTO `app_reports` (`app_reports_type_id`, `outfilename`, `descri`, `inputs`, `query_path`) VALUES
	(0, 'maga_movim_xdata', 'Movimenti x Data', 'inidate=date=Data Inizio&findate=date=Data Fine', 'excel/excel_movim_xdata.sql'),
	(0, 'maga_giac_xdata', 'Giacenza x Data', 'inidate=date=Data Inizio&findate=date=Data Fine', 'excel/excel_giac_xdata.sql');
/*!40000 ALTER TABLE `app_reports` ENABLE KEYS */;