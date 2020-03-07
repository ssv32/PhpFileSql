<? 
/**
 * PhpFileSql 
 * - мини система управления базами данных (БД), 
 *  БД представляются в виде текстовых, зашифрованных файлов.
 * Всё написано чисто на php не требует зависимостей, для работы нужен один класс.
 * 
 * Ниже примеры использования
 * 
 * сокращения везде ниже:
 *  БД - база данных
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// класс для работы с БД на файлах 
include_once('./classes/PhpFileSql.php');


// путь до папки в которой должны быть файлы БД 
//  (! желательно что бы он указывал за веб пространство проекта)
$urlFileDb = 'C:/OSPanel/domains/php-file-sql.lc/dbPhpFileSql/';
//$urlFileDb = '/var/www/dbPhpFileSql/';

$phpFileSql = new PhpFileSql($urlFileDb);

$login = 'ssv';
$pass = 'qwerty';
$nameDataBase = 'gy';

$phpFileSql->connect($login, $pass, $nameDataBase);

// закрыть текущее подключение к БД
//$phpFileSql->close();

// сохранить текущую состояние БД в файл 
//  (сохранение всегда происходит когда объект класса PhpFileSql уничтожается)
//$phpFileSql->saveThisDbInFile();

// создать БД (файл БД) и подключиться к ней, методом connect
//$phpFileSql->createDataBase('asd', 'asdasd', 'zxc22' );

// удалить БД
//$phpFileSql->deleteDataBase('asd', 'asdasd', 'zxc22');

// получить массив со всеми имеющимися БД
//$res = $phpFileSql->getDataBases();

// создать таблицу <имя таблицы>, <массив с названиями столбцов> 
//  (пока все столбцы типа строка)
//$phpFileSql->createTable('asd2', array('login', 'pass', 'flag'));

// удалить таблицу
//$phpFileSql->dropTable('asd2');

// переименовать таблицу
//$phpFileSql->renameTable('asd', 'asd3');

// вставить значение (строку в таблицу), 
//  не указанные столбцы будут заполнены пустыми значениями
//$phpFileSql->insertInto(
//    'asd3', 
//    array(
//        'login' => 'qwe12345',
//        'pass' => 'zxc222qwe'
//    )
//);

// обновить запись/строку в таблицы (используется 1 вариант условия выборки)
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

// обновить запись/строку в таблицы (используется 2 вариант условия выборки)
//$phpFileSql->update(
//    'asd3', 
//    array(
//        'login' => 'qwe123',
//        'pass' => 'zxsdfasd'
//    ),
//    array(
//        'AND' => array(
//            '=' => array(
//                'login',
//                'qwe111'
//            ),
//            '!=' => array(
//                'login',
//                'qwe123'
//            )
//        )
//    ) 
//);

// удалить запись в таблицы
//$phpFileSql->delete(
//    'asd3',
//    array(
//        '=' => array(
//            'login',
//            'qwe12345'
//        )
//    )   
//);

// вернёт массив со всеми имеющимися в БД таблицами 
$res = $phpFileSql->getAllTables();

echo 'res <pre>';
print_r($res);
echo '</pre>';

// выведет текущую ошибку
$phpFileSql->showErrors();

// добавить новый столбец в таблицу
//$phpFileSql->alterTable('testTable', array( 'asd' , 'asd2'));

// выбрать записи из таблицы, используется 1 вариант условия выборки
//  (всего доступны два варианта условия)
//  метод вернёт массив со всеми значениями
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

// выбрать записи из таблицы, используется 2 вариант условия выборки 
//  (всего доступны два варианта условия)
//   метод вернёт массив со всеми значениями
//$res = $phpFileSql->select(
//    'testTable', 
//    '*', 
//    array(
//        'OR' => array(
//            '!=' => array(
//                'login',
//                'asd2'
//            ),
//            '=' => array(
//                'login',
//                'asd'
//            )
//        )
//            
//    ) 
//);



