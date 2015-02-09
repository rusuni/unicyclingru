
jQuery(function() {
  var tbl = jQuery('.table');
  if (tbl.length > 0) {
    tbl = tbl[0];
  } else {
    //search for alternative tables but for now we quit here
    return;
  }

  var tblHead = jQuery(tbl).find('tHead');
  var tblHeadSpan = 1;
  var tblBody = jQuery(tbl).find('tBody');
  var tblBodyOffset = 0;
  var row;
  var tmp;
  var i;

  if (tblHead.length == 0) {
    tblHead = tblBody;
    tblBodyOffset = 1;
  } else {
    tblHeadSpan = tblHead.length;
  }

  var position = jQuery(tblHead[0].children[0]).find('th').length-1;

  for(i=0; i<position; i++) {
    tmp = jQuery(tblHead[0].children[0].children[i]).find('a');
    if (tmp.length > 0 && tmp[0].innerHTML == Joomla.JText._('JSTATUS')) {
      position = i;
      break;
    }
  }
  // If we didn't find the status we search for the title
  if (position == jQuery(tblHead[0].children[0]).find('th').length-1) {
    for(i=0; i<position; i++) {
      tmp = jQuery(tblHead[0].children[0].children[i]).find('a');
      if (tmp.length > 0 && tmp[0].innerHTML == Joomla.JText._('JGLOBAL_TITLE')) {
        position = i;
        break;
      }
    }
  }

  //find col span later
  var rowposition = position;

  var th = jQuery('<th width="1%" rowspan="'+tblHeadSpan+'" class="nowrap center">'+Joomla.JText._('LIB_JDICTION_TRANSLATION')+'</th>');
  th.insertAfter(tblHead[0].children[0].children[position]);

  jQuery.each(tblBody[0].children, function(line, item){
    if (line < tblBodyOffset) {
      return;
    }
    row = jdiction[line-tblBodyOffset];
    if (!row) {
      return;
    }

    var td = jQuery('<td class="center"></td>');

    var btngroup = jQuery('<div class="btn-group"/>');

    if (row['link'] != '') {
      var i = 0;
      var perrow = 0;
      for (a in row['status']) {
        if (row['status'].hasOwnProperty(a)) {
          perrow++;
        }
      }
      if (perrow > 3) {
        perrow = Math.ceil(perrow/2);
      }

      jQuery.each(row['status'], function(lang, status) {
        if (++i > perrow) {
          i=0;
          btngroup.appendTo(td);
          btngroup = jQuery('<div class="btn-group" style="margin: 1px 0" />');
        }

        var x = jQuery('<a href="'+row['link']+'" class="btn btn-micro"><span rel="tooltip" data-original-title="'+lang+'" style="background: url(../media/com_jdiction/images/flags/'+lang+'.png) no-repeat center center; width: 26px; height: 16px; display: inline-block;"><img src="components/com_jdiction/assets/icon-status-26-'+status+'.png" /></span></a>');

        x.appendTo(btngroup);
      });
    }

    btngroup.appendTo(td);


    td.insertAfter(item.children[rowposition]);
  });

});
