drop database if exists kiosk;

create database kiosk character set utf8 collate utf8_spanish_ci;

use kiosk;

create table if not exists buildingType (
  id varchar(5) primary key not null,
  description varchar(50) not null,
  color VARCHAR(10) not null
)engine = InnoDB  character set utf8 collate utf8_spanish_ci;

insert into buildingType(id, description, color) values
('DOC', 'Docencia', '#D1C4E9'),
('TP', 'Taller Pesado', '#BBDEFB'),
('OFI', 'Oficinas', '#C8E6C9');

create table if not exists buildings (
  id varchar(5) primary key not null,
  name varchar(50) not null,
  latitude double null,
  longitude double null,
  idBuildingType varchar(5) not null
)engine = InnoDB  character set utf8 collate utf8_spanish_ci;

insert into buildings (id, name, latitude, longitude, idBuildingType) values
('D1', 'Docencia 1', 32.460254, -116.825575, 'DOC'),
('VIN', 'Vinculación', 32.460931, -116.824963, 'OFI'),
('REC', 'Rectoría', 32.461407, -116.824514, 'OFI'),
('TP1', 'Taller Pesado 1', 32.460554, -116.826032, 'TP');

create table users
(	
	id varchar(20) primary key not null,
	name varchar(50) not null,
	password varchar(50) not null
)engine = InnoDb character set utf8 collate utf8_spanish_ci;

insert into users (id, name, password) values
('admin', 'Administrator', sha1('abc123')),
('jsmith', 'John Smith', sha1('john316'));

alter table buildings add constraint fkBuildingType foreign key (idBuildingType)
references buildingType (id);