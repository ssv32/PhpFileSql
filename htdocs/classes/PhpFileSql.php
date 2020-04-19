<?
/**
 * PhpFileSql 
 * (ru) - мини система управления базами данных (БД).
 *        Каждая БД это отдельный файл зашифрованный, нужен правильный логин и пароль 
 *        что бы открыть содержимое. Желательно что бы раздел с БД был выше чем веб 
 *        пространство проекта. 
 * 
 *        Большинство методов если возвращают false, кладут текст ошибки 
 *         в свойство textErrors а метод showErrors() выведет текст ошибки
 * 
 *        сокращения в комментариях ниже:
 *         БД - база данных
 * 
 * (en) - mini database management system (DB).
 *        Each database is a separate file encrypted, you need the correct login and password
 *        to open the contents. It is desirable that the database partition be higher than the web
 *        project space.
 * 
 *        Most methods, if they return false, put the error text
 *         in the textErrors property and the showErrors () method will output the error text
 * 
 *        Abbreviations in the comments below
 *         DB - database
 * 
 * @author ssv32 <ssv_32@mail.ru>
 * @version 0.1
 */
class PhpFileSql {

    /**
     * $daseTemplateDataBase 
     *  (ru) - базовый шаблон БД (нужен для создания новой БД)
     * 
     *  (en) - basic database template (needed to create a new database)
     * 
     * @var array 
     */
    private $daseTemplateDataBase = array(
        'testDecrypt' => true,
        'tables' => array()
    );
    
    /**
     * $typeMethodsCipher 
     *  (ru) - метод шифрования текста в файле
     * 
     *  (en) - method for encrypting text in a file
     * 
     * @var string 
     */
    private $typeMethodsCipher = 'AES256';

    /**
     * $flagConnectDb 
     *  (ru) - флаг true если было удачное подключение к БД
     * 
     *  (en) - true flag if there was a successful connection to the database
     * 
     * @var boolean 
     */
    private $flagConnectDb = false;
    
    /**
     * $urlDbs
     *  (ru) - путь на сервере, до раздела где будут файлы с базами данных
     *         ! желательно что бы файлы были в не веб пространства проекта
     *  
     *  (en) - path on the server, to the section where the database files will be
     *         ! it is desirable that the files are in non-web space of the project
     * 
     * @var string 
     */
    private $urlDbs; 
    
    /**
     * $prefixNameFileDb
     *  (ru) - префикс у файла которые хранят данные базы данных. 
     *         пример как будет называться файл в котором лежит БД 
     *         phpFileDb_<имя БД>
     * 
     *  (en) - the prefix of the file that stores the database data.
     *         example database file name
     *         phpFileDb_<database name>
     * 
     * @var string 
     */
    private $prefixNameFileDb = 'phpFileDb_';
    
    /**
     * $listErrors
     *  (ru) - массив текстов ошибок (ключ это код ошибки)
     *  
     *  (en) - array of error texts (the key is the error code)
     *  
     * @var array 
     */
    private $listErrors = array(
        'err_search_file_db' => 'БД не найдена (Database not found).',
        'err_empty_pass' => 'Не задан пароль (No password set).',
        'err_decrypt' => 'Ошибка расшифровки или неправельные авторизационные данные (Decryption error or incorrect authorization data.).',
        'err_this_db' => 'Нет текущей БД (No current database).',
        'err_encrypt_date' => 'Проблемы с шифрованием текущей БД (Problems with encryption of the current database).',
        'err_save_db_in_file' => 'Проблемы с записью в файл текущей БД (Problems writing to the current database file).',
        'err_connect_bd' => 'Не было удачного подключения к БД (There was no successful connection to the database).',
        'err_delete_file' => 'Ошибка удаления файла БД (Error deleting database file).',
        'err_create_table_empty' => 'Такая таблица уже есть (Such a table already exists).',
        'err_drop_table_not_table' => 'Удаляемой таблицы не существует (The table being deleted does not exist).',
        'err_rename_table_not_table' => 'Таблица с новым названием уже есть (A table with a new name is already there).',
        'err_not_table' => 'Таблица не найдена (Table not found).',
        'err_not_where' => 'Не задано условие (No condition specified).'
    );
    
    /**
     * $textErrors
     *  (ru) - тут будет текст текущей ошибки
     * 
     *  (en) - here will be the text of the current error
     * 
     * @var string 
     */
    public $textErrors;  
    
    public function __construct($urlDbs){
        $this->urlDbs = $urlDbs;
    }
    
    /**
     * GetMessage 
     *  (ru) - вернёт текст ошибки с кодом $codeMassage
     * 
     *  (en) - will return an error text with the code $ codeMassage
     * 
     * @param string $codeMassage -
     *   (ru) - код ошибки
     *   (en) - error code
     * @return string - 
     *   (ru) - текст ошибки
     *   (en) - error text
     */
    private function GetMessage($codeMassage){
        return $this->listErrors[$codeMassage];
    }
    
    /**
     * showErrors()
     *  (ru) - выведет текущую ошибку
     *  (en) - will display the current error
     */
    public function showErrors(){
        if(!empty($this->textErrors)){
            echo '! error: '.$this->textErrors;
        }else{
            echo 'not error';
        }
    }
    
    /**
     * $passFile
     *  (ru) - пароль к расшифровки файла БД (к которой идёт подключение)
     *  (en) - password to decrypt the database file (to which you are connecting)
     * 
     * @var string 
     */
    private $passFile;
    
    /**
     * $nameDataBase
     *  (ru) - имя текущей БД
     *  
     *  (en) - name of the current database
     * 
     * @var string 
     */
    private $nameDataBase;  
    
    /**
     * $datasDataBase
     *  (ru) - данные текущей БД
     *  
     *  (en) - current database data
     * 
     * @var array 
     */
    private $datasDataBase; 
    
    /**
     * connect
     *  (ru) - подключение к БД (к файлу БД)
     * 
     *  (en) - connection to the database (to the database file)
     * 
     * @param string $login - 
     *        (ru) - логин
     *        (en) - login
     * @param string $pass - 
     *        (ru) - пароль
     *        (en) - password
     * @param string $nameDataBase - 
     *        (ru) - имя БД
     *        (en) - DB name
     * @return boolean
     */
    public function connect($login, $pass, $nameDataBase){
        $result = false;
        $this->nameDataBase = $nameDataBase;
        $this->passFile = md5($login).md5($pass).md5($nameDataBase);
                
        if($this->searchDb($nameDataBase)){
            if( $this->openFileDb($nameDataBase) ){
                
                $result = true;
            }else{
                $result = false;
                $this->textErrors = $this->GetMessage('err_search_file_db');
            }
        }else{
            $result = false;
            $this->textErrors = $this->GetMessage('err_search_file_db');
        }
        $this->flagConnectDb = $result;
        return $result;
    }
    
    /**
     * searchDb 
     *  (ru) - поиск файла БД, проверит есть ли файл
     *  
     *  (en) - database file search, check if there is a file
     * 
     * @param string $nameDataBase - 
     *        (ru) - имя БД
     *        (en) - DB name
     * @return boolean true/false
     */
    public function searchDb($nameDataBase){       
        return file_exists($this->urlDbs.$this->prefixNameFileDb.$nameDataBase);
    }
    
    /**
     * openFileDb
     *  (ru) - открытие файла БД 
     * 
     *  (en) - open DB file
     * 
     * @param string $nameDataBase - 
     *        (ru) - имя БД
     *        (en) - DB name
     * @return boolean
     */
    private function openFileDb($nameDataBase){
        
        // (ru) - взять данные из файла
        // (en) - take data from file
        $data = file_get_contents($this->urlDbs.$this->prefixNameFileDb.$nameDataBase);
               
        // (ru) - расшифровать данные
        // (en) - decrypt data
        $decryptDate = $this->decryptDate($data);
                
        if($decryptDate !== false){
            // (ru) - записать расшифрованные данные должны быть массив
            // (en) - write decrypted data should be an array
            $this->datasDataBase = json_decode( $decryptDate, true );
            $result = true;
        }else{
            $result = false;
            $this->textErrors = $this->GetMessage('err_decrypt');   
        }
        return $result; 
    }
    
    /**
     * decryptDate
     *  (ru) - расшифровать данные из файла БД
     * 
     *  (en) - decrypt data from a database file
     * 
     * @param string $date - 
     *        (ru) - строка с данными из файла
     *        (en) - line with data from the file
     * @return boolean/string
     */
    private function decryptDate($date){
        $result = false;
        if(!empty($this->passFile)){
            // (ru) - расшифровка
            // (en) - decryption
            $result = openssl_decrypt($date, $this->typeMethodsCipher, $this->passFile);    
        }else{
            $this->textErrors = $this->GetMessage('err_empty_pass');
            $result = false;
        }
        return $result;
    }
    
    /**
     * saveDbInFile
     *  (ru) - сохранить текущую БД в файл
     * 
     *  (en) - save current database to file
     * 
     * @return boolean 
     */
    public function saveThisDbInFile(){
        if($this->flagConnectDb == true){
            if(!empty($this->datasDataBase)){
                
                $data = $this->encryptDate( json_encode( $this->datasDataBase) );
                
                if ($data !==  false){
                    // (ru) сохраняем в файл
                    // (en) save to file
                    $res = file_put_contents($this->urlDbs.$this->prefixNameFileDb.$this->nameDataBase, $data);
                                        
                    if($res !== false){
                        $result = true;
                    }else{
                        $result = true;
                        $this->textErrors = $this->GetMessage('err_save_db_in_file');
                    }
                }else{
                    $this->textErrors = $this->GetMessage('err_encrypt_date');
                    $result = false;
                }
            }else{
                $this->textErrors = $this->GetMessage('err_this_db');
                $result = false;
            }
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }

        return $result;
    }
    
    /**
     * saveDbInFile
     *  (ru) - сохранить данные БД в файл
     * 
     *  (en) - save database data to file
     * 
     * @return boolean 
     */
    private function saveDbInFile($url, $data){
        return file_put_contents($url, $data);
    }
    
    /**
     * encryptDate
     *  (ru) - зашифровать данные
     * 
     *  (en) - encrypt data
     * 
     * @param string $date
     * @return boolean
     */
    private function encryptDate($date){
        $result = false;
        if(!empty($this->passFile)){
            $result = openssl_encrypt($date, $this->typeMethodsCipher, $this->passFile);
        }else{
            $this->textErrors = $this->GetMessage('err_empty_pass');
            $result = false;
        }
        return $result;
    }
    
    /**
     * close 
     *  (ru) - закрыть текущее подключение к БД
     * 
     *  (en) - close the current database connection
     * 
     * @return boolean
     */
    public function close(){
        $result = false;
        if($this->flagConnectDb == true){
            $this->saveThisDbInFile();
            $this->flagConnectDb = false;
            $this->passFile = false; 
            $this->nameDataBase = false; 
            $this->datasDataBase = false;
            $result = true;
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;
    }
    
    /**
     * createDataBase 
     *  (ru) - создать БД (файл БД) и открыть методом connect
     * 
     *  (en) - create a database (database file) and open the connect method
     * 
     * @param string $login
     * @param string $pass
     * @param string $nameDataBase
     * @return boolean 
     */
    public function createDataBase($login, $pass, $nameDataBase){
               
        $this->passFile = md5($login).md5($pass).md5($nameDataBase);
        
        $data = $this->encryptDate(json_encode( $this->daseTemplateDataBase) );
        if ($data !==  false){
            // (ru) - сохраняем в файл
            // (en) - save to file
            $res = $this->saveDbInFile($this->urlDbs.$this->prefixNameFileDb.$nameDataBase, $data);

            if($res !== false){
                $result = true;
            }else{
                $result = true;
                $this->textErrors = $this->GetMessage('err_save_db_in_file');
            }
        }else{
            $this->textErrors = $this->GetMessage('err_encrypt_date');
            $result = false;
        }
        
        $this->passFile = false;
        return $this->connect($login, $pass, $nameDataBase);
    }
    
    /**
     * deleteDataBase
     *  (ru) - удалить БД (файл БД) если данные авторизации верны
     * 
     *  (en) - delete the database (database file) if the authorization data is correct
     * 
     * @param string $login
     * @param string $pass
     * @param string $nameDataBase
     * @return boolean
     */
    public function deleteDataBase($login, $pass, $nameDataBase){
        $result = false;
        if ($this->searchDb($nameDataBase)){
                        
            // (ru) - взять данные из файла
            // (en) - take data from file
            $data = file_get_contents($this->urlDbs.$this->prefixNameFileDb.$nameDataBase);
            
            // (ru) - расшифровать данные
            // (en) - decrypt data
            $passDb = md5($login).md5($pass).md5($nameDataBase);
            
            $decryptDate = openssl_decrypt($data, $this->typeMethodsCipher, $passDb); 

            if($decryptDate !== false){               
                
                $data = json_decode( $decryptDate, true );
                
                if ($data['testDecrypt'] == true){
                    // (ru) - удалить файл
                    // (en) - delete file
                    $res = unlink($this->urlDbs.$this->prefixNameFileDb.$nameDataBase);
                    
                    if($res){
                        $result = true;
                    }else{
                        $result = false;
                        $this->textErrors = $this->GetMessage('err_delete_file');
                    }
                }else{
                    $result = false;
                    $this->textErrors = $this->GetMessage('err_decrypt');
                }
            }else{
                $result = false;
                $this->textErrors = $this->GetMessage('err_decrypt');
            }
        }else{
            $result = false;
            $this->textErrors = $this->GetMessage('err_search_file_db');
        }
        return $result;
    }
      
    /**
     * getDataBases
     *  (ru) - получить массив со всеми имеющимися БД
     * 
     *  (en) - get an array with all available databases
     * 
     * @return array
     */
    public function getDataBases(){
        $filesBd = array();
        $filesBd = scandir($this->urlDbs);
               
        foreach ($filesBd as $key => $value) {          
            if( strpos($value, $this->prefixNameFileDb) !== false ){
                $filesBd[$key] = str_replace($this->prefixNameFileDb, '', $value );
            }else{
                unset($filesBd[$key]);
            }
        }
        return $filesBd;
    }
    
    /**
     * getAllTables 
     *  (ru) - вернёт массив со всеми имеющимися в БД таблицами 
     *         (false - если произошла ошибка)
     * 
     *  (en) - will return an array with all the tables in the database
     *         (false - if an error occurred)
     * 
     * @return false/array
     */
    public function getAllTables(){
        $result = false;
        if($this->flagConnectDb){
            $result = $this->datasDataBase['tables'];
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;
    }
    
    /**
     * createTable
     *  (ru) - создать таблицу
     * 
     *  (en) - create table
     * 
     * @param string $tableName - 
     *        (ru) - имя таблицы
     *        (en) - table name
     * @param array $arrayColumns - 
     *        (ru) - массив с именами столбцов
     *        (en) - array with column names
     * @return boolean
     */
    public function createTable($tableName, $arrayColumns){
        $result = false;
        if($this->flagConnectDb){
            
            if( empty($this->datasDataBase['tables'][$tableName])  ){
                
                $this->datasDataBase['tables'][$tableName]['columns'] = array();
                
                foreach ($arrayColumns as $value) {
                    $this->datasDataBase['tables'][$tableName]['columns'][] = array(
                        'name' => $value
                    );
                }
                
                $this->datasDataBase['tables'][$tableName]['row'] = array();
                
                $result = true;
            }else{
                $this->textErrors = $this->GetMessage('err_create_table_empty');
                $result = false;
            }
            
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;
    }
    
    /**
     * dropTable
     *  (ru) - удаление таблицы
     * 
     *  (en) - delete table
     * 
     * @param string $nameTable - 
     *        (ru) - имя таблицы
     *        (en) - table name
     * @return boolean
     */
    public function dropTable($nameTable){
        $result = false;
        if($this->flagConnectDb){
            if( !empty($this->datasDataBase['tables'][$nameTable])){
                // (ru) - удаляем таблицу
                // (en) - delete table
                unset($this->datasDataBase['tables'][$nameTable]);
            }else{
                $this->textErrors = $this->GetMessage('err_drop_table_not_table');
                $result = false;
            } 
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;
    }
    
    /**
     * renameTable 
     *  (ru) - переименовать таблицу
     * 
     *  (en) - rename table
     * 
     * @param string $nameTable - 
     *        (ru) - старое название таблицы
     *        (en) - old table name
     * @param string $newNameTable - 
     *        (ru) - новое название таблицы
     *        (en) - new table name
     * @return boolean
     */
    public function renameTable($nameTable, $newNameTable){
        $result = false;
        if($this->flagConnectDb){
            if( empty($this->datasDataBase['tables'][$newNameTable])){
                
                // (ru) - создать таблицу с новым названием
                // (en) - create a table with a new name
                $this->datasDataBase['tables'][$newNameTable] = $this->datasDataBase['tables'][$nameTable];
                
                // (ru) - удаляем таблицу со старым названием 
                // (en) - delete the table with the old name
                unset($this->datasDataBase['tables'][$nameTable]);
                
            }else{
                $this->textErrors = $this->GetMessage('err_rename_table_not_table');
                $result = false;
            } 
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;
    }
    
    /**
     * alterTable
     *  (ru) - добавить новые столбцы
     * 
     *  (en) - add new columns
     * 
     * @param string $nameTable - 
     *        (ru) - имя таблицы
     *        (en) - table name
     * @param array $arrayNewColumns - 
     *        (ru) - массив с новыми столбцами
     *        (en) - array with new columns
     * @return boolean
     */
    public function alterTable($nameTable, $arrayNewColumns){
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                // (ru) - определить есть ли уже новые поля в БД
                // (en) - determine if there are already new fields in the database
                $arrayEmptyColums = false;
                foreach ($this->datasDataBase['tables'][$nameTable]['columns'] as $value) {
                    if( in_array($value['name'], $arrayNewColumns ) ){
                        $arrayEmptyColums[$value['name']] = $value['name'];
                    }
                }
                
                foreach ($arrayNewColumns as $key => $value) {
                    if(!empty($arrayEmptyColums[$value])){
                        unset($arrayNewColumns[$key]);
                    }
                }
                
                foreach ($arrayNewColumns as $value) {
                    $this->datasDataBase['tables'][$nameTable]['columns'][]['name'] = $value;
                    foreach ($this->datasDataBase['tables'][$nameTable]['row'] as $key2 => $value2) {
                        $this->datasDataBase['tables'][$nameTable]['row'][$key2][$value] = '';
                    }
                }
                $result = true;
            }else{
                $this->textErrors = $this->GetMessage('err_not_table');
                $result = false;
            }
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;
    }
        
    /**
     * isOneVersionWhere 
     *  (ru) - проверит соответствует ли условие, условию как ниже (первый вариант)
     *         (пока поддерживается только сравнение и не рано '=', '!=' )
     *  
     *  (en) - will check whether it matches the condition, the condition as below (first option)
     *         (so far only comparison is supported and not early '=', '! =')
     * 
     *  $where = array(
     *    '=' => array(
     *       'login',
     *       'asd2'
     *    )
     *  ) 
     * 
     * @param array $where - 
     *        (ru) - условие (пример выше, что то типа дерева)
     *        (en) - condition (example above, something like a tree)
     * @return boolean
     */        
    private function isOneVersionWhere($where){
        $result = false;
        if (count($where) == 1){
            foreach ($where as $key => $value) {
                if( in_array($key, array('=', '!=' )) && (count($value) == 2) ){
                    $result = true;
                }
            }
            $value = array_shift($where);
            
        }
        return $result;
    }
    
    /**
     * isTwoVersionWhere 
     *  (ru) - проверит соответствует ли условие, условию как ниже (второй вариант)
     *         (пока поддерживается только сравнение и не рано '=', '!=' и связки 'AND', 'OR' )
     *  
     *  (ru) - will check whether the condition matches the condition as below (second option)
     *         (so far only comparison is supported and not early '=', '! =' and the 'AND', 'OR' connectives)
     * 
     *  $where = array(
     *      'OR' => array(
     *          '=' => array(
     *              'login',
     *              'asd2'
     *          ),
     *          '!=' => array(
     *              'login',
     *              'asd'
     *          ),
     *          '!=' => array(
     *              'login',
     *              'asd'
     *          ),
     *      )    
     *  ) 
     * 
     * @param array $where - 
     *        (ru) - условие (пример выше, что то типа дерева)
     *        (en) - condition (example above, something like a tree)
     * @return boolean
     */ 
    private function isTwoVersionWhere($where){
        $result = true;
        foreach ($where as $key => $value) {
            if(in_array($key, array('OR', 'AND'))){
                foreach ($value as $key2 => $value2) {
                    if ( in_array($key2, array('=', '!=' )) && (count($value2) != 2) ){
                        $result = false;
                    }
                }
            }else{
                $result = false;
            }
        }
        return $result;
    }
        
    /**
     * getStrOneTypeWhere
     *  (ru) - соберёт строчку с условием определённого вида,
     *         для условий из массива $where (метода например select) 1 варианта
     * 
     *  (en) - will collect a line with a condition of a certain kind,
     *         for conditions from the array $where (for example, select parameters) 1 option
     * 
     * @param array $where
     * @return string
     */
    private function getStrOneTypeWhere($where){
        $result = false;
        if(!empty($where['='])){
            $result = '($value'."['".$where['='][0]."'] == '".$where['='][1]."')";
        }elseif( !empty($where['!=']) ){
            $result = '($value'."['".$where['!='][0]."'] != '".$where['!='][1]."')";
        }
        return $result;
    }
    
    /**
     * getStrOneTypeWhere
     *  (ru) - соберёт строчку с условием определённого вида,
     *         для условий из массива $where (метода например select) 2 варианта
     * 
     *  (en) - will collect a line with a condition of a certain kind,
     *         for conditions from the array $where (for example, select parameters) 2 option
     * 
     * @param array $where
     * @return string
     */
    private function getStrTwoTypeWhere($where){
        $result = '';
        if( !empty($where['AND']) ){
            foreach($where['AND'] as $key => $val){
                $result .= ((!empty($result))? ' && ': '').$this->getStrOneTypeWhere(array( $key => $val) );
            }
        }elseif( !empty($where['OR'])){
            foreach($where['OR'] as $key => $val){
                $result .= ((!empty($result))? ' || ': '').$this->getStrOneTypeWhere(array( $key => $val));
            }
        }
        return $result;
    }
    
    /**
     * select
     *  (ru) - выбрать значения из таблицы
     * 
     *  (en) - select values ​​from table
     * 
     * @param string $nameTable - 
     *        (ru) - имя таблицы
     *        (en) - table name
     * @param array/string $arrayNameColumns - 
     *        (ru) - поля которые нужно вернуть
     *        (en) - fields to be returned
     * @param array $where - 
     *        (ru) - условие выборки - выше есть примеры
     *        (en) - sampling condition - there are examples above
     * @return array/boolean - 
     *        (ru) - вернёт false если неудача или массив со значениями
     *        (en) - will return false if failure or an array with values
     */ 
    public function select($nameTable, $arrayNameColumns = '*', $where = false ){
        $result = false;
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                
                $result = array();
                
                // (ru) - выбрать нужные поля
                // (en) - select the required fields
                if( ($arrayNameColumns !== '*') && is_array($arrayNameColumns)){
                    foreach ($this->datasDataBase['tables'][$nameTable]['row'] as $key => $value) {

                        foreach ($arrayNameColumns as $keyRow){
                            if(isset($value[$keyRow])){
                                $result[$key][$keyRow] = $value[$keyRow];
                            }
                        }
                    }
                }elseif($arrayNameColumns == '*'){
                    $result = $this->datasDataBase['tables'][$nameTable]['row'];
                }
                
                // (ru) - взять нужное по условию
                // (en) - take the necessary conditionally
                if($where != false){
                    
                    $strWhere = '';
                    if($this->isOneVersionWhere($where) ){
                        // (ru) - если условия 1 варианта
                        // (en) - if conditions 1 options
                        $strWhere = '$flag = '.$this->getStrOneTypeWhere($where).';';
                        
                    }elseif($this->isTwoVersionWhere($where) ){
                        // (ru) - если условие 2 варианта
                        // (en) - if condition 2 options
                        $strWhere = '$flag = ('.$this->getStrTwoTypeWhere($where).');';
                    } 
                    // (ru) - остальное пока не поддерживается
                    // (en) - the rest is not yet supported
                                        
                    if($strWhere != ''){
                                                
                        foreach ($result as $key => $value){
                            $flag = false;
                            eval($strWhere);
                                  
                            // (ru) - если данные не подходят под условия то убрать их
                            // (en) - if the data does not fit the conditions 
                            if( !$flag ){
                                unset($result[$key]);
                            }
                        }
                    } 
                }
            }else{
                $this->textErrors = $this->GetMessage('err_not_table');
                $result = false;
            }
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;
    } 
    
    /**
     * insertInto
     *  (ru) - добавить строку в таблицу
     * 
     *  (en) - add row to table
     * 
     * @param string $nameTable
     * @param array $arrayProperty - 
     *        (ru) - массив значений (<название столбца> => <значение>)
     *        (en) - array of values ​​(<column name> => <value>)
     * @return boolean
     */
    public function insertInto($nameTable, $arrayProperty){
        $result = false;
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                
                /**
                 * (ru) - находим столбцы присланные в метод какие есть в таблице 
                 *        (если присланы в метод но их нет то не создадутся)
                 *        (которые не указаны создадутся как пустые)
                 * 
                 * (en) - we find the columns sent to the method which are in the table
                 *        (if sent to the method but they are not there, they won’t be created)
                 *        (which are not specified will be created as empty)
                 */
                $arrayTrueProperty = array();
                foreach ($this->datasDataBase['tables'][$nameTable]['columns'] as $value) {
                    if(!empty($arrayProperty[$value['name']])){
                        $arrayTrueProperty[$value['name']] = $arrayProperty[$value['name']];
                    }else{
                        $arrayTrueProperty[$value['name']] = '';
                    }
                }
                $this->datasDataBase['tables'][$nameTable]['row'][] = $arrayTrueProperty;
                $result = true;
            }else{
                $this->textErrors = $this->GetMessage('err_not_table');
                $result = false;
            }
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;    
    }
    
    /**
     * update 
     *  (ru) - обновить строку в таблице
     * 
     *  (en) - update row in table
     * 
     * @param string $nameTable - 
     *        (ru) - имя таблицы
     *        (en) - table name
     * @param array $arrayProperty -  
     *        (ru) - массив значений (<название столбца> => <значение>)
     *        (en) - array of values ​​(<column name> => <value>)
     * @param array $where - 
     *        (ru) - условие все какие поддерживает метод select
     *        (en) - all conditions that the select method supports
     * @return boolean
     */
    public function update($nameTable, $arrayProperty, $where){
        $result = false;
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                
                // (ru) - взять нужное по условию
                // (en) - take the necessary conditionally
                if( !empty($where) ){
                    
                    $strWhere = '';
                    if($this->isOneVersionWhere($where) ){
                        // (ru) - если условия 1 варианта
                        // (en) - 1 option
                        $strWhere = '$flag = '.$this->getStrOneTypeWhere($where).';';
                        
                    }elseif($this->isTwoVersionWhere($where) ){
                        // (ru) - если условие 2 варианта
                        // (en) - 2 option
                        $strWhere = '$flag = ('.$this->getStrTwoTypeWhere($where).');';
                    } 
                    // (ru) - остальное пока не поддерживается
                    // (en) - the rest is not yet supported
                                                  
                    if($strWhere != ''){
                                                
                        foreach ($this->datasDataBase['tables'][$nameTable]['row'] as $key => $value){
                            
                            $flag = false;
                            eval($strWhere);
                                 
                            // (ru) - если данные подходят под условия обновить их
                            // (en) - if the data is suitable for the conditions, update them
                            if( $flag ){
                                foreach ($arrayProperty as $keyUpdateProperty => $valueUpdateProperty) {
                                    if(isset($this->datasDataBase['tables'][$nameTable]['row'][$key][$keyUpdateProperty])){
                                        $this->datasDataBase['tables'][$nameTable]['row'][$key][$keyUpdateProperty] = $valueUpdateProperty;    
                                    }
                                }
                            }
                        }
                    }
                    $result = true;
                }else{
                    $this->textErrors = $this->GetMessage('err_not_where');
                    $result = false;
                }
            }else{
                $this->textErrors = $this->GetMessage('err_not_table');
                $result = false;
            }
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;         
    }
       
    /**
     * delete
     *  (ru) - удалить строку таблицы
     * 
     *  (en) - delete table row
     * 
     * @param string $nameTable - 
     *        (ru) - имя таблицы
     *        (en) - table name
     * @param array $where - 
     *        (ru) - условие все какие поддерживает метод select
     *        (en) - all conditions that the select method supports
     * @return boolean
     */
    public function delete($nameTable, $where){
        $result = false;
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                
                // (ru) - взять нужное по условию
                // (en) - take the necessary conditionally
                if( !empty($where) ){
                    
                    $strWhere = '';
                    if($this->isOneVersionWhere($where) ){
                        // (ru) - если условия 1 варианта
                        // (en) - 1 option
                        $strWhere = '$flag = '.$this->getStrOneTypeWhere($where).';';
                        
                    }elseif($this->isTwoVersionWhere($where) ){
                        // (ru) - если условие 2 варианта
                        // (en) - 2 option
                        $strWhere = '$flag = ('.$this->getStrTwoTypeWhere($where).');';
                    } 
                    // (ru) - остальное пока не поддерживается
                    // (en) - the rest is not yet supported       
                    
                    if($strWhere != ''){
                                                
                        foreach ($this->datasDataBase['tables'][$nameTable]['row'] as $key => $value){
                            
                            $flag = false;
                            eval($strWhere);
                                                              
                            // (ru) - если данные подходят под условия обновить их
                            // (en) - if the data is suitable for the conditions, update them
                            if( $flag ){
                                unset($this->datasDataBase['tables'][$nameTable]['row'][$key]);
                            }
                        }
                    }
                    $result = true;
                }else{
                    $this->textErrors = $this->GetMessage('err_not_where');
                    $result = false;
                }
            }else{
                $this->textErrors = $this->GetMessage('err_not_table');
                $result = false;
            }
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;      
    }
    
    /**
     * (ru) - при уничтожении объекта БД сохранить в файл БД
     * (en) - after destroying the class object, save the database to the database file
     */
    function __destruct() {
        $this->saveThisDbInFile();
    }
    
}

