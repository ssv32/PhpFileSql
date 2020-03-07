# PhpFileSql
<br/>
PhpFileSql <br/>
 (ru) - мини система управления базами данных (СУБД) на php.<br/>
Базы данных хранятся в виде зашифрованных, текстовых файлов на сервере и не требует от хостинга каких либо СУБД или зависимостей в php.<br/>
        Т.е. для того что бы хранить таблицы с данными, доставать, обновлять, создавать новые столбцы/строки достаточно иметь на хоcтинге только php.<br/>
        <br/>
        Каждая БД это отдельный файл зашифрованный, нужен правильный логин и пароль,<br/>
        что бы открыть содержимое (или удалять). <br/>
        <br/>
        Рекомендации по использованию:<br/>
         - желательно что бы раздел с БД (файлами БД) был выше чем веб пространство проекта.  <br/>
        <br/>
        Цели: <br/>
         - иметь возможность просто брать и работать с данными на хостингах где нет СУБД, а есть только php;<br/>
         - попробовать сделать что то рабочее за минимальное время;<br/>
         - сделать всё в виде одного класса;<br/>
         - БД представляет собой 1 файл;<br/>
         - минимум каких либо зависимостей.<br/>
        <br/>
        Примечания:<br/>
         - ! реализовано не всё, а совсем маленькая часть от того что есть в полноценных СУБД.<br/>
         - условия WHERE поддерживают всего 2 варианта записи (больших вложенных условий нельзя делать);<br/>
         - в условии WHERE в случае AND/OR не поддерживается несколько одинаковых условий <br/>
           например <поле1>=<значение1> AND <поле2>=<значение2> - в этом случае только одно = выполнится .<br/>
        <br/>
        Сокращения в текстах:<br/>
         - БД, база данных;<br/>
         - СУБД, система управления базами данных.<br/>

-----
<br/>
 (en) - mini database management system (DBMS) in php.<br/>
        Databases are stored as an encrypted, text file on the server and do not require any messages or dependencies in php from hosting.<br/>
        In order to store tables with data, get, update, create new columns / rows, it is enough to have only php on the host.<br/>
        <br/>
        Each database is a separate file encrypted, you need the correct login and password,<br/>
        to open the contents (or delete).<br/>
        <br/>
        Recommendations for use:<br/>
         - it is desirable that the section with the database (database files) be higher than the web space of the project.  <br/>
        <br/>
        Goals: <br/>
         - be able to just take and work with data on hosting where there is no DBMS, but only php;<br/>
         - try to do something working in minimal time;<br/>
         - make everything in one class;<br/>
         - DB is 1 file;<br/>
         - minimum of any dependencies.<br/>
        <br/>
        Notes:<br/>
         - ! not everything is implemented, but a very small part of what is in a full-fledged DBMS.<br/>
         - WHERE conditions support only 2 recording options (large nested conditions cannot be made);<br/>
         - in the WHERE clause in the case of AND / OR, several identical conditions are not supported <br/>
           for example <field1> = <value1> AND <field2> = <value2> - in this case only one '=' will be executed.<br/>
        <br/>
        Abbreviations in the texts:<br/>
         - DB, database;<br/>
         - DBMS, database management system.<br/>
