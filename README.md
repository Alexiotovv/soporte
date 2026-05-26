Requisitos:
php8.2, laravel12, composer, mysql8

1. Clonar el proyecto.
3. correr en la raiz del proyecto, ejecutar el comando: composer install
4. Crea tu base de datos en mysql, el nombre que tu elijas, ej. soporte
5. copia el archivo .env.example con otro nombre a .env
6. modifica las variables del archivo .env:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=soporte
    DB_USERNAME=root
    DB_PASSWORD='123456'
7. ejecuta el comando php artisan migrate.
8. ejecuta el comando php artisan db:seed
9. 10. correr el proyecto: php artisan serv
11. Abrir el navegador y escribe la url http://localhost:8000
12. en el archivo UsersTableSeeder, puedes encontrar los usuarios de prueba.
