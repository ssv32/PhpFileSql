<?
/**
 * PhpFileSql 
 * - мини база данных на файлах 
 *  каждая БД это отдельный файл зашифрованный, нужен правильный логин и пароль 
 *  что бы открыть содержимое. Желательно что бы раздел с БД был выше чем веб 
 *  пространство проекта. 
 * 
 *  Большинство методов если возвращают false, кладут текст ошибки 
 *   в свойство textErrors а метод showErrors() выведет текст ошибки
 * 
 * сокращения везде ниже:
 *  БД - база данных
 * 
 * @author ssv32 <ssv_32@mail.ru>
 * @version 0.1
 */
class PhpFileSql {

    /**
     * $daseTemplateDataBase 
     *  - базовый шаблон БД (нужен для создания новой БД)
     * 
     * @var array 
     */
    private $daseTemplateDataBase = array(
        'testDecrypt' => true,
        'tables' => array()
    );
    
    /**
     * $typeMethodsCipher 
     *  - метод шифрования текста в файле
     * 
     * @var string 
     */
    private $typeMethodsCipher = 'AES256';

    /**
     * $flagConnectDb 
     *  - флаг true если было удачное подключение к БД
     * 
     * @var boolean 
     */
    private $flagConnectDb = false;
    
    /**
     * $urlDbs
     *  - путь на сервере, до раздела где будут файлы с базами данных
     *  ! желательно что бы файлы были в не веб пространства проекта
     * 
     * @var string 
     */
    private $urlDbs; 
    
    /**
     * $prefixNameFileDb
     *  - префикс у файла которые хранят данные базы данных. 
     *  пример как будет называться файл в котором лежит БД 
     *  phpFileDb_<имя БД>
     * 
     * @var string 
     */
    private $prefixNameFileDb = 'phpFileDb_';
    
    /**
     * $listErrors
     *  - массив текстов ошибок (ключ это код ошибки)
     * текст на Русском языке
     * 
     * @var array 
     */
    private $listErrors = array(
        'err_search_file_db' => 'БД не найдена.',
        'err_empty_pass' => 'не задан пароль.',
        'err_decrypt' => 'ошибка расшифровки или неправельные авторизационные данные.',
        'err_this_db' => 'нет текущей БД.',
        'err_encrypt_date' => 'проблемы с шифрованием текущей БД.',
        'err_save_db_in_file' => 'проблемы с записью в файл текущей БД.',
        'err_connect_bd' => 'не было удачного подключения к БД.',
        'err_delete_file' => 'ошибка удаления файла БД.',
        'err_create_table_empty' => 'такая таблица уже есть.',
        'err_drop_table_not_table' => 'удаляемой таблицы не существует.',
        'err_rename_table_not_table' => 'таблица с новым названием уже есть.',
        'err_not_table' => 'таблица не найдена.',
        'err_not_where' => 'не задано условие'
    );
    
    /**
     * $textErrors
     *  - тут будет текст текущей ошибки
     * 
     * @var string 
     */
    public $textErrors;  
    
    public function __construct($urlDbs){
        $this->urlDbs = $urlDbs;
    }
    
    /**
     * GetMessage 
     *   - вернёт текст ошибки с кодом $codeMassage
     * 
     * @param string $codeMassage - код ошибки
     * @return string - текст ошибки
     */
    private function GetMessage($codeMassage){
        return $this->listErrors[$codeMassage];
    }
    
    /**
     * showErrors()
     *  - выведет текущую ошибку
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
     *  - пароль к расшифровки файла БД (к которой идёт подключение)
     * 
     * @var string 
     */
    private $passFile;
    
    /**
     * $nameDataBase
     *  - имя текущей БД
     * 
     * @var string 
     */
    private $nameDataBase;  
    
    /**
     * $datasDataBase
     *  - данные текущей БД
     *  
     * @var array 
     */
    private $datasDataBase; 
    
    /**
     * connect
     *  - подключение к БД (к файлу БД)
     * 
     * @param string $login - логин
     * @param string $pass - пароль
     * @param string $nameDataBase - имя БД
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
     *  - поиск файла БД, проверит есть ли файл
     * 
     * @param string $nameDataBase - имя БД
     * @return boolean true/false
     */
    public function searchDb($nameDataBase){       
        return file_exists($this->urlDbs.$this->prefixNameFileDb.$nameDataBase);
    }
    
    /**
     * openFileDb
     *  - открытие файла БД 
     * 
     * @param string $nameDataBase - имя БД
     * @return boolean
     */
    private function openFileDb($nameDataBase){
        
        // взять данные из файла
        $data = file_get_contents($this->urlDbs.$this->prefixNameFileDb.$nameDataBase);
               
        // расшифровать данные
        $decryptDate = $this->decryptDate($data);
                
        if($decryptDate !== false){
            // записать расшифрованные данные должны быть массив
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
     *  - расшифровать данные из файла БД
     * 
     * @param string $date - строка с данными из файла
     * @return boolean/string
     */
    private function decryptDate($date){
        $result = false;
        if(!empty($this->passFile)){
            // расшифровка
            $result = openssl_decrypt($date, $this->typeMethodsCipher, $this->passFile);    
        }else{
            $this->textErrors = $this->GetMessage('err_empty_pass');
            $result = false;
        }
        return $result;
    }
    
    /**
     * saveDbInFile
     *  - сохранить текущую БД в файл
     * 
     * @return boolean 
     */
    public function saveThisDbInFile(){
        if($this->flagConnectDb == true){
            if(!empty($this->datasDataBase)){
                
                $data = $this->encryptDate( json_encode( $this->datasDataBase) );
                
                if ($data !==  false){
                    // сохраняем в файл
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
     *  - сохранить данные БД в файл
     * 
     * @return boolean 
     */
    private function saveDbInFile($url, $data){
        return file_put_contents($url, $data);
    }
    
    /**
     * encryptDate
     *  - зашифровать данные
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
     *  - закрыть текущее подключение к БД
     * 
     * @return boolean
     */
    public function close(){
        $result = false;
        if($this->flagConnectDb == true){
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
     *  - создать БД (файл БД) и открыть методом connect
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
            // сохраняем в файл
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
     *  - удалить БД (файл БД) если данные авторизации верны
     * 
     * @param string $login
     * @param string $pass
     * @param string $nameDataBase
     * @return boolean
     */
    public function deleteDataBase($login, $pass, $nameDataBase){
        $result = false;
        if ($this->searchDb($nameDataBase)){
                        
            // взять данные из файла
            $data = file_get_contents($this->urlDbs.$this->prefixNameFileDb.$nameDataBase);
            
            // расшифровать данные
            $passDb = md5($login).md5($pass).md5($nameDataBase);
            
            $decryptDate = openssl_decrypt($data, $this->typeMethodsCipher, $passDb); 

            if($decryptDate !== false){               
                
                $data = json_decode( $decryptDate, true );
                
                if ($data['testDecrypt'] == true){
                    // удалить файл
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
     *  - получить массив со всеми имеющимися БД
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
     *  - вернёт массив со всеми имеющимися в БД таблицами 
     *  (false - если произошла ошибка)
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
     *  - создать таблицу
     * 
     * @param string $tableName - имя таблицы
     * @param array $arrayColumns - массив с именами столбцов
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
                
            }else{
                $this->textErrors = $this->GetMessage('err_create_table_empty');
                $result = false;
            }
            
            $result = true;
        }else{
            $this->textErrors = $this->GetMessage('err_connect_bd');
            $result = false;
        }
        return $result;
    }
    
    /**
     * dropTable
     *  - удаление таблицы
     * 
     * @param string $nameTable - имя таблицы
     * @return boolean
     */
    public function dropTable($nameTable){
        $result = false;
        if($this->flagConnectDb){
            if( !empty($this->datasDataBase['tables'][$nameTable])){
                // удаляем таблицу
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
     *  - переименовать таблицу
     * 
     * @param string $nameTable - старое название таблицы
     * @param string $newNameTable - новое название таблицы
     * @return boolean
     */
    public function renameTable($nameTable, $newNameTable){
        $result = false;
        if($this->flagConnectDb){
            if( empty($this->datasDataBase['tables'][$newNameTable])){
                
                // создать таблицу с новым названием
                $this->datasDataBase['tables'][$newNameTable] = $this->datasDataBase['tables'][$nameTable];
                
                // удаляем таблицу со старым названием 
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
     *  - добавить новые столбцы
     * 
     * @param string $nameTable - имя таблицы
     * @param array $arrayNewColumns - массив с новыми столбцами
     * @return boolean
     */
    public function alterTable($nameTable, $arrayNewColumns){
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                // определить есть ли уже новые поля в БД
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
     *  - проверит соответствует ли условие, условию как ниже (первый вариант)
     *  (пока поддерживается только сравнение и не рано '=', '!=' )
     *  
     *  $where = array(
     *    '=' => array(
     *       'login',
     *       'asd2'
     *    )
     *  ) 
     * 
     * @param array $where - условие (пример выше, что то типа дерева)
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
     *  - проверит соответствует ли условие, условию как ниже (второй вариант)
     *  (пока поддерживается только сравнение и не рано '=', '!=' и связки 'AND', 'OR' )
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
     * @param array $where - условие (пример выше, что то типа дерева)
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
    
    // если условия 1 вого варианта
    
    /**
     * getStrOneTypeWhere
     *  - соберёт строчку с условием определённого вида,
     *  для условий из массива $where (метода например select) 1 варианта
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
     *  - соберёт строчку с условием определённого вида,
     *  для условий из массива $where (метода например select) 2 варианта
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
     *  - выбрать значения из таблицы
     * 
     * @param string $nameTable - имя таблицы
     * @param array/string $arrayNameColumns - поля которые нужно вернуть
     * @param array $where - условие выборки - выше есть примеры
     * @return array/boolean - вернёт false если неудача или массив со значениями
     */
    public function select($nameTable, $arrayNameColumns = '*', $where = false ){
        $result = false;
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                
                $result = array();
                
                // выбрать нужные поля
                foreach ($this->datasDataBase['tables'][$nameTable]['row'] as $key => $value) {
                    if( ($arrayNameColumns !== '*') && is_array($arrayNameColumns)){
                        
                        foreach ($arrayNameColumns as $keyRow){
                            $result[$key][$keyRow] = $value[$keyRow];
                        }
                        
                    }elseif($arrayNameColumns == '*'){
                        $result[] = $value;
                    }
                }
                
                // взять нужное по условию
                if($where != false){
                    
                    $strWhere = '';
                    if($this->isOneVersionWhere($where) ){
                        // если условия 1 варианта
                        $strWhere = '$flag = '.$this->getStrOneTypeWhere($where).';';
                        
                    }elseif($this->isTwoVersionWhere($where) ){
                        // если условие 2 варианта
                        $strWhere = '$flag = ('.$this->getStrTwoTypeWhere($where).');';
                    } // остальное пока не поддерживается
                                        
                    if($strWhere != ''){
                                                
                        foreach ($result as $key => $value){
                            $flag = false;
                            eval($strWhere);
                                  
                            // если данные не подходят под условия то убрать их
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
     *  - добавить строку в таблицу
     * 
     * @param string $nameTable
     * @param array $arrayProperty - массив значений 
     *   (<название столбца> => <значение>)
     * @return boolean
     */
    public function insertInto($nameTable, $arrayProperty){
        $result = false;
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                
                // находим столбцы присланные в метод какие есть в таблице 
                //  (если присланы в метод но их нет то не создадутся)
                //  (которые не указаны создадутся как пустые)
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
     *  - обновить строку в таблице
     * 
     * @param string $nameTable - имя таблицы
     * @param array $arrayProperty -  - массив значений 
     *   (<название столбца> => <значение>)
     * @param array $where - условие все какие поддерживает метод select
     * @return boolean
     */
    public function update($nameTable, $arrayProperty, $where){
        $result = false;
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                
                // взять нужное по условию
                if( !empty($where) ){
                    
                    $strWhere = '';
                    if($this->isOneVersionWhere($where) ){
                        // если условия 1 варианта
                        $strWhere = '$flag = '.$this->getStrOneTypeWhere($where).';';
                        
                    }elseif($this->isTwoVersionWhere($where) ){
                        // если условие 2 варианта
                        $strWhere = '$flag = ('.$this->getStrTwoTypeWhere($where).');';
                    } // остальное пока не поддерживается
                                                  
                    if($strWhere != ''){
                                                
                        foreach ($this->datasDataBase['tables'][$nameTable]['row'] as $key => $value){
                            
                            $flag = false;
                            eval($strWhere);
                                                              
                            // если данные подходят под условия обновить их
                            if( $flag ){
                                foreach ($arrayProperty as $keyUpdateProperty => $valueUpdateProperty) {
                                    if(!empty($this->datasDataBase['tables'][$nameTable]['row'][$key][$keyUpdateProperty])){
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
     *  - удалить строку таблицы
     * 
     * @param string $nameTable - имя таблицы
     * @param array $where - условие все какие поддерживает метод select
     * @return boolean
     */
    public function delete($nameTable, $where){
        $result = false;
        if($this->flagConnectDb){
            if(!empty($this->datasDataBase['tables'][$nameTable])){
                
                 // взять нужное по условию
                if( !empty($where) ){
                    
                    $strWhere = '';
                    if($this->isOneVersionWhere($where) ){
                        // если условия 1 варианта
                        $strWhere = '$flag = '.$this->getStrOneTypeWhere($where).';';
                        
                    }elseif($this->isTwoVersionWhere($where) ){
                        // если условие 2 варианта
                        $strWhere = '$flag = ('.$this->getStrTwoTypeWhere($where).');';
                    } // остальное пока не поддерживается
                                                  
                    if($strWhere != ''){
                                                
                        foreach ($this->datasDataBase['tables'][$nameTable]['row'] as $key => $value){
                            
                            $flag = false;
                            eval($strWhere);
                                                              
                            // если данные подходят под условия обновить их
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
     * при уничтожении объекта БД сохранить в файл БД
     */
    function __destruct() {
        $this->saveThisDbInFile();
    }
    
}

