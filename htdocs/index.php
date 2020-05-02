<? 
/**
 * PhpFileSql 
 * (ru) - мини система управления базами данных (БД).
 *        БД представляются в виде текстовых, зашифрованных файлов.
 *        Всё написано чисто на php не требует зависимостей, для работы нужен один класс.
 * 
 *        Ниже примеры использования.
 * 
 *        сокращения в комментариях ниже:
 *         БД - база данных
 * 
 * (en) - mini database management system (DB).
 *        Databases are presented as text, encrypted files.
 *        Everything is written purely in php does not require dependencies, to work you need one class.
 *
 *        Below are examples of use.
 * 
 *        Abbreviations in the comments below:
 *         DB - database
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/** 
 * (ru) - класс для работы с БД на файлах
 *  
 * (en) - class for working with the database on files
 */
include_once('./classes/PhpFileSql.php');


/**
 * (ru) - путь до папки в которой должны быть файлы БД 
 *        (! желательно что бы он указывал за веб пространство проекта)
 * 
 * (en) - path to the folder where the database files should be
 *        (! it is desirable that he points to the web space of the project)
 */
$urlFileDb = 'C:/OSPanel/domains/php-file-sql.lc/dbPhpFileSql/'; // my windows
//$urlFileDb = '/var/www/dbPhpFileSql/'; // my Linux

$phpFileSql = new PhpFileSql($urlFileDb);

$login = 'ssv';
$pass = 'qwerty';
$nameDataBase = 'gy';

$phpFileSql->connect($login, $pass, $nameDataBase);

/**
 * (ru) - закрыть текущее подключение к БД
 * 
 * (en) - close the current database connection
 */
//$phpFileSql->close();

/**
 * (ru) - сохранить текущую состояние БД в файл 
 *        (сохранение всегда происходит когда объект класса PhpFileSql уничтожается)
 * 
 * (en) - save the current state of the database in a file
 *        (saving always happens when an object of the PhpFileSql class is destroyed)
 */
//$phpFileSql->saveThisDbInFile();

/**
 * (ru) - создать БД (файл БД) и подключиться к ней, методом connect()
 * 
 * (en) - create a database (database file) and connect to it, method connect()
 */
//$phpFileSql->createDataBase('asd', 'asdasd', 'zxc22' );

/** 
 * (ru) - удалить БД
 * 
 * (en) - delete DB
 */
//$phpFileSql->deleteDataBase('asd', 'asdasd', 'zxc22');

/** 
 * (ru) - получить массив со всеми имеющимися БД
 * 
 * (en) - get an array with all available databases
 */
//$res = $phpFileSql->getDataBases();

/** 
 * (ru) - создать таблицу <имя таблицы>, <массив с названиями столбцов> 
 *        (пока все столбцы типа строка)
 * 
 * (en) - create table <table name>, <array with column names>
 *        (while all columns are of type string(char*) )
 * 
 */  
//$phpFileSql->createTable('asd555', array( array('id', 'PRIMARY_KEY_AUTO_INCREMENT' ), 'login', 'pass', 'flag'));

/** 
 * (ru) - удалить таблицу
 * 
 * (en) - delete table
 */
//$phpFileSql->dropTable('asd2');

/** 
 * (ru) - переименовать таблицу
 * 
 * (en) - rename table
 */
//$phpFileSql->renameTable('asd', 'asd3');

/** 
 * (ru) - вставить значение (строку в таблицу), 
 *        не указанные столбцы будут заполнены пустыми значениями
 * 
 * (en) - insert the value (row into the table),
 *        columns not specified will be filled with empty values
 */ 
//$phpFileSql->insertInto(
//    'asd555', 
//    array(
//        'login' => '111',
//        'pass' => '1111'
//    )
//);

/** 
 * (ru) - обновить запись/строку в таблицы (используется 1 вариант условия выборки)
 * 
 * (en) - update record/line in the table (1 option of the selection condition is used)
 */
//$phpFileSql->update(
//    'asd3', 
//    array(
//        'login' => 'qwe321',
//        'pass' => 'zxsdf'
//    ),
//    array(
//        '=' => array(
//            'login',
//            'qwe321'
//        )
//    ) 
//);

/** 
 * (ru) - обновить запись/строку в таблицы (используется 2 вариант условия выборки)
 * 
 * (en) - update record/line in the table (2 option of the selection condition is used)
 */
//$phpFileSql->update(
//    'asd3', 
//    array(
//        'login' => 'qwe123',
//        'pass' => 'zxsdfasd'
//    ),
//    array(
//        'AND' => array(
//            array(
//                '=' => array(
//                    'login',
//                    'qwe111'
//                ),
//            ),
//            array(
//                '!=' => array(
//                    'login',
//                    'qwe123'
//                )
//            ),
//        )
//    ) 
//);

/** 
 * (ru) - удалить запись в таблицы
 * 
 * (en) - 
 */
//$phpFileSql->delete(
//    'asd3',
//    array(
//        '=' => array(
//            'login',
//            'qwe12345'
//        )
//    )   
//);

/** 
 * (ru) - вернёт массив со всеми имеющимися в БД таблицами 
 * 
 * (en) - delete table entry
 */
$res = $phpFileSql->getAllTables();

echo 'res <pre>';
print_r($res);
echo '</pre>';

/** 
 * (ru) - выведет текущую ошибку (ошибка записывается если метод вернул false)
 * 
 * (en) - will display the current error (an error is written if the method returned false)
 */
$phpFileSql->showErrors();

/** 
 * (ru) - добавить новый столбец в таблицу
 * 
 * (en) - add a new column to the table
 */
//$phpFileSql->alterTable('testTable', array( 'asd' , 'asd2'));

/** 
 * (ru) - выбрать записи из таблицы, используется 1 вариант условия выборки
 *        (всего доступны два варианта условия)
 *        метод вернёт массив со всеми значениями
 * 
 * (en) - select an entry from the table, 1 option of the selection condition is used
 *        (two options are available)
 *        the method will return an array with all values
 */
//$res = $phpFileSql->select(
//    'testTable', 
//    '*', 
//    array(
//        '=' => array(
//            'login',
//            'asd'
//        )
//    ) 
//);

/**
 * (ru) - выбрать записи из таблицы, используется 2 вариант условия выборки 
 *        (всего доступны два варианта условия)
 *        метод вернёт массив со всеми значениями
 * 
 * (en) - select an entry from the table, 2 option of the selection condition is used
 *        (two options are available)
 *        the method will return an array with all values
 */
//
//$res = $phpFileSql->select(
//    'testTable', 
//    '*', 
//    array(
//        'OR' => array(
//            array(
//                '=' => array(
//                    'login',
//                    'asd'
//                ),
//            ),
//            array(
//                '!=' => array(
//                    'login',
//                    'asd3'
//                )
//            ),
//        )
//            
//    ) 
//);

//echo "res<pre>";
//print_r($res);
//echo "</pre>";