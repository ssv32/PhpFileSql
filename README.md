# PhpFileSql

PhpFileSql - мини СУБД на файлах.
Это мини СУБД сделанная на php, хранит данные в виде файлов на сервере и не требует от хостинга каких либо СУБД или зависимостей в php).
Т.е. для того что бы хранить таблицы с данными, доставать, обновлять, создавать новые столбцы/строки достаточно иметь на хоcтинге только php.
! Реализовано не всё, а совсем маленькая часть от того что есть в полноценных СУБД.

Каждая БД это отдельный файл зашифрованный, нужен правильный логин и пароль,
что бы открыть содержимое (или удалять). 

! Желательно что бы раздел с БД (файлами БД) был выше чем веб пространство проекта.  

Цель, что бы иметь возможность просто брать и работать с данными на хостингах где нет СУБД, а есть только php. 
