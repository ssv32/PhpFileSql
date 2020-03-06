<? 
/**
 * PhpFileSql 
 * - мини база данных на файлах, ниже примеры использования
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// класс для рабты с БД на файлах 
include_once('./classes/PhpFileSql.php');

// путь до папки в которой должны быть файлы БД 
//  (! желательно что бы он указывал за веб пространство проекта)
$urlFileDb = '/var/www/dbPhpFileSql/';

$phpFileSql = new PhpFileSql($urlFileDb);

$login = 'ssv';
$pass = 'qwerty';
$nameDataBase = 'gy';

$phpFileSql->connect($login, $pass, $nameDataBase);

//$phpFileSql->close();

//$phpFileSql->saveThisDbInFile();

//$phpFileSql->showErrors();

//$phpFileSql->close();
//$phpFileSql->createDataBase('asd', 'asdasd', 'zxc22' );

//$phpFileSql->deleteDataBase('asd', 'asdasd', 'zxc22');


//$res = $phpFileSql->getDataBases();

//$phpFileSql->createTable('asd2', array('login', 'pass', 'flag'));

//$phpFileSql->dropTable('asd2');
//$phpFileSql->dropTable('asd2');

//$phpFileSql->renameTable('asd', 'asd3');

//$phpFileSql->insertInto(
//    'asd3', 
//    array(
//        'login' => 'qwe12345',
//        'pass' => 'zxc222qwe'
//    )
//);

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

//$phpFileSql->delete(
//    'asd3',
//    array(
//        '=' => array(
//            'login',
//            'qwe12345'
//        )
//    )   
//);

$res = $phpFileSql->getAllTables();

echo 'res <pre>';
print_r($res);
echo '</pre>';

//echo 'datasDataBase <pre>';
//print_r($phpFileSql->datasDataBase);
//echo '</pre>';


$phpFileSql->showErrors();

//$phpFileSql->alterTable('testTable', array( 'asd' , 'asd2'));

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

//echo 'select $res<pre>';
//print_r($res);
//echo '</pre>';



