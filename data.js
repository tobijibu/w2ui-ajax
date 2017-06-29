var DATA = DATA || {};

/**
 * データ送信・取得
 * ajaxでデータを送信・取得し、DATAに値を代入します。
 *
 * @return  object
 */
doAjax = function(id) {
  let defer = $.Deferred(),
      formData = $(id).serialize();
  $.ajax({
    type     : 'POST',
    url      : './data.php',
    dataType : 'json',
    data     : formData,
    success: function(json){
      DATA.head = json.head;  // --> カラム名
      DATA.data = json.data;  // --> データ行
      DATA.sum  = json.sum;   // --> 合計行
      defer.resolve();
    }
  });
  return defer.promise();
}

/**
 * データ編集
 * データを編集します。
 * 入力内容を判定し、問題がなければdoAjax()を実行します。
 *
 * @param event
 * @return  void
 */
edit = function(event){
  let oldVal = event.value_previous,  // --> 編集前の値
      newVal = event.value_new,       // --> 編集後の値
      row    = event.index;  // --> 編集した行
      name;
  //空のまま変更がなかった場合
  if (! oldVal && ! newVal) return;
  //変更が無かった場合
  if (oldVal == newVal) return;
  //「数」カラムでない場合
  if (event.column != 1) return;

  // 編集した行の"name"
  // "mygrid"はw2grid生成時に指定する、nameプロパティの値です。
  name = w2ui.mygrid.records[row].name;

  //nameとcntをformに設定
  $('#postData input[name=name]').val(name);
  $('#postData input[name=cnt]').val(newVal);

  //データ送信
  let post = doAjax('#postData');
  //グリッドを更新
  post.done(function() {
    //最新のデータを設定して更新します。
    w2ui.mygrid.records = DATA.data;
    w2ui.mygrid.summary = DATA.sum;
    w2ui.mygrid.refresh();
  });
}

/**
 * グリッド生成
 *
 * @return  void
 */
init = function() {
  let get = doAjax('#getData');
  get.done(function() {
    $('#mygrid').w2grid({
      name: 'mygrid',       // --> w2ui.mygridという形で利用できるようにします。
      selectType : 'cell',
      columns : DATA.head,  // --> カラム名
      records : DATA.data,  // --> データ行
      summary : DATA.sum,   // --> 合計行
      onChange: function(event) {
        edit(event);
      },
    });
  });
}
init();

