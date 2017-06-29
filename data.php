<?php
/**
 * ※※※※※※※※※※※※※※※※※※※※※※
 * ※とてもいい加減なスクリプトなので、　　　※
 * ※ローカル確認以外に利用しないでください。※
 * ※セキュリティリスクが高いスクリプトです。※
 * ※※※※※※※※※※※※※※※※※※※※※※
 *
 * データ取得、更新用スクリプト
 *
 * w2uiでajaxを利用したデータの表示、
 * データの送信を行います。
 */

/**
 * DB定義
 * 環境に併せて編集してください。
 */
define('USER',    'root');
define('PWD',     '123456');
define('DB_NAME', 'test');
define('DB_HOST', 'localhost');
define('DSN', 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';charset=utf8');

$driver_options = array(
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
$db = new \PDO(DSN, USER, PWD, $driver_options);

switch ($_POST['mode']) {
    case 'update':
        $res = updateData($db, $_POST);
        break;
    case 'insert':
        $res = insertData($db, $_POST);
        break;
    case 'select':
    default:
        break;
}
$data = selectData($db);
header('Content-Type: application/json');
print setArrayData($data);

/**
 * データ更新
 *
 * @param   object  $db     DBオブジェクト
 * @param   array   $post   POSTデータ
 * @return  array
 */
function updateData($db, $post) {
    $sql = 'UPDATE zoo
            SET
                cnt     = :cnt,
                updated = NOW()
            WHERE
                name = :name';
    $value = array(
        'cnt'  => $post['cnt'],
        'name' => $post['name'],
    );
    $sth = $db->prepare($sql);
    $res = $sth->execute($value);
    return $res;
}

/**
 * データ取得
 *
 * @param   object  $db     DBオブジェクト
 * @return  array
 */
function selectData($db) {
    $sql = 'SELECT
               *
            FROM zoo';
    $res = $db->query($sql);
    while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
        $data[] = (array)$row;
    }
    return $data;
}

/**
 * データ配列生成
 * w2uiで読み込むJSONを生成します。
 *
 * @param   object  $db     DBオブジェクト
 * @return  string  JSON
 */
function setArrayData($data) {
    /*
     * データ定義用配列
     * 以下のようなJSONを生成するために配列を生成します。
     * "data":[
     *   {"recid":"1","name":"dog","cnt":"4","updated":"2017-06-28 15:57:06"},
     *   ...,
     * ],
     */
    foreach ($data as $d => $v) {
        $sum += $v['cnt'];
        $res['data'][] = array(
            'recid'   => $v['id'],
            'name'    => $v['name'],
            'cnt'     => $v['cnt'],
            'updated' => $v['updated'],
        );
    }

    /*
     * カラム定義用配列
     * 以下のJSONを生成するために配列を生成します。
     * "head":[
     *   {"field":"name","caption":"\u540d\u524d","size":"30%"},
     *   {"field":"cnt","caption":"\u6570","size":"30%",
     *       "editable":{"type":"int","min":0,"max":100}
     *   },
     *   {"field":"updated","caption":"\u66f4\u65b0\u65e5\u6642","size":"30%"}
     * ],
     */
    $res['head'] = array(
        array('field' => 'name',    'caption' => '名前',     'size' => '30%'),
        array('field' => 'cnt',     'caption' => '匹数',     'size' => '30%',
            'editable' => array('type' => 'int', 'min' => 0, 'max' => 100),
        ),
        array('field' => 'updated', 'caption' => '更新日時', 'size' => '30%')
    );

    /*
     * 合計データ定義用配列
     * 以下のようなJSONを生成するために配列を生成します。
     * "sum":[
     *   {"recid":null,"name":"\u5408\u8a08","cnt":18,"updated":null}
     * ]
     */
    $res['sum'][]  = array(
        'recid'   => null,
        'name'    => '合計',
        'cnt'     => $sum,
        'updated' => null,
    );
    return json_encode($res);
}
