# ESQL
Project for the University of Bologna Database course (70155), Accademic Year (2023/2024)

### Done by:
- Matteo Canghiari
- Davide De Rosa
- Ossama Nadifi

[Specifics of the project](https://virtuale.unibo.it/mod/resource/view.php?id=1355231)

## Information about ESQL
**ESQL** is a project done for the main course Database. The goal of this project is to develop a WEB interface, like the software MOODLE, which allows to run some queries and gives a result of them. 

## Setup [Ubuntu Users]
To use the ESQL project you have to install:
- PHP  
  ```bash
  sudo apt-get install php
  ```
- MySql Community Server
  ```bash
  sudo apt-get install mysql-server
  ```
- Apache2
  ```bash
  sudo apt-get install apache2
  ```
Finally, when you installed all the requirements you have to enable the PDO module with the following command:
```bash
  sudo phpenmod pdo_mysql
  ```
## Deploy [Ubuntu Users]
To deploy the WEB interface you have to know your file system. So there are three command that you must run, divided in:
- From the command line interface run
  ```bash
  sudo systemctl start mysql
  ```
  ```bash
  sudo systemctl start apache2
  ```
- Finally, where did you clone the repository run the following command and go to your localhost
   ```bash
  php -S localhost:port_number
  ```
