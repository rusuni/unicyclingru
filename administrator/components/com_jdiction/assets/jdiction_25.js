window.addEvent('domready', function() {
  var tbl = $$('table.adminlist');

  if (tbl.length) {
    tbl = tbl[0];
  } else {
    return;
  }
  var tblHead = tbl.tHead;
  var tblHeadSpan = 1;
  var tblFoot = tbl.tFoot;
  var tblBody = tbl.tBodies[0];
  var tblBodyOffset = 0;
  var position;
  var rowposition;
  var row;

  if (!tblHead) {
    tblHead = tblBody;
    tblBodyOffset = 1;
  } else {
    tblHeadSpan = tblHead.rows.length;
  }

  var header = $(tblHead).getElements('th a');
  if (header.length == 0) {
    return;
  }

  Array.each(header, function(o, i) {
    if (o.text == 'Status') {
      position = o.getParent();
      rowposition = position.cellIndex;
    }
  });
  if (!position) {
    position = header.getLast().getParent();
    rowposition = (tblBody.rows[0].cells.length) - 1;
  }

  var th = document.createElement('th');
  var th = new Element('th', {
    html: 'Translation',
    width: 100,
    rowspan: tblHeadSpan
  } );
  th.inject(position, 'after');

  if (tblFoot) {
    for (var h=0; h<tblFoot.rows.length; h++) {
      tblFoot.rows[h].cells[0].colSpan = tblFoot.rows[h].cells[0].colSpan + 1;
    }
  }

  for (var i=tblBodyOffset; i<tblBody.rows.length; i++) {
    row = jdiction[i-tblBodyOffset];
    if (!row) {
      continue;
    }
    var td = new Element('td', {
      'class': 'center'
    });

    var ele = td;

    if (row['link'] != '') {
      var link = new Element('a', {
        'href': row['link'],
        'style': 'width: '+(Object.getLength(row['status'])*20)+'px; margin: 0 auto 0 auto; display: block',
        'class': 'center'
      });
      link.inject(ele);
      ele = link;
    }

    Object.each(row['status'], function(status,lang) {
      var statustag = new Element('span', {
        'style': 'background-image: url(components/com_jdiction/assets/icon-status-16-'+status+'.png); background-position: center center; background-size: 20px 20px; padding-top: 6px; width: 20px; height: 20px; background-repeat: no-repeat; display: block; float: left;'
      });

      var img = new Element('img', {
        'src': '../media/com_jdiction/images/flags/'+lang+'.png',
        'width': 13
      });

      img.inject(statustag);
      statustag.inject(ele);
    });

    // ICON PER LANGUAGE
    var childs = $(tblBody.rows[i]).getChildren('td');
    td.inject(childs[rowposition], 'after');
  }

});
