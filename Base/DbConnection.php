<?php
/**
 * 1. Соединение с БД через PDO (lazy load)
 * 2. Иметь объект подключения везде в любой точке проекта
 * 3. execute, select (fetchOne, fetchAll)
 * 4. профилирование (время запроса, запрос, affected_rows)
 * 5. уметь работать с несколькими базами
 */

namespace Base;

class DbConnection
{
    const DB_USER = 1;
    const DB_POSTS = 2;

    const QUERY_TYPE_SELECT = 1;
    const QUERY_TYPE_INSERT = 2;
    const QUERY_TYPE_UPDATE = 3;
    const QUERY_TYPE_DELETE = 4;
    const QUERY_TYPE_TRUNCATE = 5;

    /** @var \PDO[] */
    private $_pdo = [];

    /**
     * @var array
     * [
     *  [0 => query_time, 1 => dbname, 2 => eventText, affectedRows, method]
     * ]
     */
    private $_log = [];

    private static $_tableDbNumberMap = [
      'users' => self::DB_USER,
      'messages' => self::DB_POSTS,
    ];

    private static $_instance;

    private $_dbNames;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getConnection(int $dbNum)
    {
        if (!isset($this->_pdo[$dbNum])) {


            $dbType = $_ENV['DB_TYPE'] ?? 'mysql';
            $dbHost = $_ENV['DB_HOST'];
            $dbName = $_ENV['DB_NAME'];
            $dbUser = $_ENV['DB_USER'];
            $dbPassword = $_ENV['DB_PASSWORD'];

            $t = microtime(1);
            $this->_pdo[$dbNum] = new \PDO("$dbType:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
            $this->_log[] = [
              microtime(1) - $t,
              $dbName,
              'connect',
              0,
              ''
            ];

            $this->_dbNames[$dbNum] = $dbName;
        }

        return $this->_pdo[$dbNum];
    }

    private function getTableNameFromQuery(string $query): string
    {
        /**
         * SELECT * FROM table WHERE ...
         * INSERT INTO table ...
         * UPDATE table SET ...
         * DELETE FROM table ..
         * TRUNCATE table ..
         */

        $query = strtolower($query);
        $query = trim($query);

        $queryType = $this->getQueryType($query);
        switch ($queryType) {
            case self::QUERY_TYPE_SELECT:
            case self::QUERY_TYPE_DELETE:
                $query = preg_replace('/\s+/', ' ', $query);
                $parts = explode(' from ', $query);
                $table = explode(' ', $parts[1])[0];
                break;
            case self::QUERY_TYPE_INSERT:
                $query = preg_replace('/\s+/', ' ', $query);
                $parts = explode(' into ', $query);
                $table = explode(' ', $parts[1])[0];
                break;
            case self::QUERY_TYPE_UPDATE:
            case self::QUERY_TYPE_TRUNCATE:
                $query = preg_replace('/\s+/', ' ', $query);
                $parts = explode(' ', $query);
                $table = explode(' ', $parts[1])[0];
                break;

            default:
                throw new \PDOException('Cant define table name for query: ' . $query);
        }

        return str_replace('`', '', $table);
    }

    private function getQueryType(string $query): int
    {
        $queryTypeString = substr($query, 0, 6);
        if ($queryTypeString == 'select') {
            return self::QUERY_TYPE_SELECT;
        } elseif ($queryTypeString == 'insert') {
            return self::QUERY_TYPE_INSERT;
        } elseif ($queryTypeString == 'update') {
            return self::QUERY_TYPE_UPDATE;
        } elseif ($queryTypeString == 'delete') {
            return self::QUERY_TYPE_DELETE;
        } elseif (substr($query, 0, 8) == 'truncate') {
            return self::QUERY_TYPE_TRUNCATE;
        } else {
            throw new \PDOException('Cant define query type: ' . $query);
        }
    }

    public function exec(string $query, string $_method, array $params = []): array
    {


        $table = $this->getTableNameFromQuery($query);

        if (!isset(self::$_tableDbNumberMap[$table])) {
            throw new \PDOException('No table ' . $table . ' in map');
        }
        $dbNumber = self::$_tableDbNumberMap[$table];
        $pdo = $this->getConnection($dbNumber);
        $prepared = $pdo->prepare($query);

        $t = microtime(1);
        $ret = $prepared->execute($params);


        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return -1;
        }
        $affectedRows = $prepared->rowCount();
        $this->_log[] = [
          microtime(1) - $t,
          $this->_dbNames[$dbNumber],
          $this->getClearQuery($prepared, $params),
          $affectedRows,
          $_method
        ];
        return ['affectedRows'=>$affectedRows, 'ret' => $prepared->fetch(\PDO::FETCH_ASSOC)];
    }

    public function fetchAll(string $query, string $_method, array $params = []): array
    {


        $table = $this->getTableNameFromQuery($query);

        if (!isset(self::$_tableDbNumberMap[$table])) {
            throw new \PDOException('No table ' . $table . ' in map');
        }
        $dbNumber = self::$_tableDbNumberMap[$table];
        $pdo = $this->getConnection($dbNumber);
        $prepared = $pdo->prepare($query);

        $t = microtime(1);
        $ret = $prepared->execute($params);

        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }

        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $this->_log[] = [
          microtime(1) - $t,
          $this->_dbNames[$dbNumber],
          $this->getClearQuery($prepared, $params),
          $prepared->rowCount(),
          $_method
        ];
        return $data;
    }

    public function fetchOne(string $query, string $_method, array $params = []): array
    {
        $data = $this->fetchAll($query, $_method, $params);
        return $data ? reset($data) : [];
    }

    public function getLog(bool $asHtml = true)
    {
        if ($asHtml) {
            $html = '<br><br><hr><br>';
            if ($this->_log) {
                foreach ($this->_log as $item) {
                    list($queryTime, $dbName, $text, $affectedRows, $method) = $item;
                    $html .= round($queryTime, 4) . ': ' . $dbName . ': ' . $text . " [$affectedRows] | $method<br>";
                }
            }
            return $html;
        } else {
            return $this->_log;
        }
    }

    private function getClearQuery(\PDOStatement $prepared, $params = [])
    {
        $query = $prepared->queryString;
        if ($params) {
            foreach ($params as $param => $value) {
                $query = str_replace($param, $value, $query);
            }
        }

        return $query;
    }
}
