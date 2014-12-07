function addform(div, val, help) {
document.getElementById(div).innerHTML = '<input type=text name="'+div+'" value="'+val+'" size="70">';
document.getElementById(div+'_change').innerHTML = '';
document.getElementById(div+'_help').innerHTML = help;
}

function addcheck(div, text) {
document.getElementById(div).innerHTML = text;
}

function delcheck(div) {
document.getElementById(div).innerHTML = '';
}

function add_ext_form(div, val, help) {
var oldstr='';
var nameindex=0;

for (var i = 0; i < val.length; i++) {
nameindex++;
oldstr = oldstr+' <input type=text name="'+div+'['+nameindex+']" value="'+val[i]+'" size="70"><BR>';
}
document.getElementById(div).innerHTML = oldstr;
document.getElementById(div+'_add').innerHTML = '<a onclick="return addField(\''+div+'\', \''+nameindex+'\')" href="#">Добавить</a>';
document.getElementById(div+'_change').innerHTML = '';
document.getElementById(div+'_help').innerHTML = help;
}

function add_tri_form(div, val, head1, head2, head3, help) {
var oldstr='<table width=100%><tr><td><SMALL>'+head1+'</SMALL></td><td><SMALL>'+head2+'</SMALL></td><td><SMALL>'+head3+'</SMALL></td></tr>';
var nameindex=0;
for (var i = 0; i < val.length; i++) {
nameindex++;
var spl_val = val[i].split(/[|]/);
if(!spl_val[0]) spl_val[0]="Не указано";
if(!spl_val[1]) spl_val[1]="Не указано";
if(!spl_val[2]) spl_val[2]="Не указано";
oldstr = oldstr+'<tr><td><textarea name="'+div+'1'+'['+nameindex+']" cols="15" rows="8">'+spl_val[0]+'</textarea></td>';
oldstr = oldstr+'<td><textarea name="'+div+'2'+'['+nameindex+']" cols="30" rows="8">'+spl_val[1]+'</textarea></td>';
oldstr = oldstr+'<td><textarea name="'+div+'3'+'['+nameindex+']" cols="30" rows="8">'+spl_val[2]+'</textarea></td></tr>';
}
oldstr = oldstr+'<tr><td><div id='+div+'_ext1></div></td><td><div id='+div+'_ext2></div></td><td><div id='+div+'_ext3></div></td></tr></table>';
document.getElementById(div).innerHTML = oldstr;
document.getElementById(div+'_add').innerHTML = '<a onclick="return add3Field(\''+div+'\', \''+nameindex+'\')" href="#">Добавить поля</a>';
document.getElementById(div+'_change').innerHTML = '';
document.getElementById(div+'_help').innerHTML = help;
}



var count = 1;
var curFieldNameId = 1; // Уникальное значение для атрибута name

function deleteField(a) {
var countOfFields = 1; // Текущее число полей
 // Получаем доступ к ДИВу, содержащему поле
 var contDiv = a.parentNode;
 // Удаляем этот ДИВ из DOM-дерева
 contDiv.parentNode.removeChild(contDiv);
 // Уменьшаем значение текущего числа полей
 countOfFields--;
 // Возвращаем false, чтобы не было перехода по сслыке
 return false;
}
function addField(todiv, index) {
 // Увеличиваем текущее значение числа полей
 index=index-0;
 addnameindex = count + index;
 count++;
  // Создаем элемент ДИВ
 var div = document.createElement("div");
  // Добавляем HTML-контент с пом. свойства innerHTML
 div.innerHTML = "<input name=\"" + todiv + "[" + addnameindex + "]\" type=\"text\" size=\"70\">";
 // Добавляем новый узел в конец списка полей
 document.getElementById(todiv).appendChild(div);
 // Возвращаем false, чтобы не было перехода по сслыке
 return false;
}

function add3Field(todiv, index) {
 // Увеличиваем текущее значение числа полей
 index=index-0;
 addnameindex = count + index;
 count++;
  // Создаем элемент ДИВ
 var div1 = document.createElement("div");
 var div2 = document.createElement("div");
 var div3 = document.createElement("div");
  // Добавляем HTML-контент с пом. свойства innerHTML
 div1.innerHTML = "<textarea name=\"" + todiv +"1"+"[" + addnameindex + "]\" cols=\"15\" rows=\"8\"></textarea>";
 div2.innerHTML = "<textarea name=\"" + todiv +"2"+"[" + addnameindex + "]\" cols=\"30\" rows=\"8\"></textarea>";
 div3.innerHTML = "<textarea name=\"" + todiv +"3"+"[" + addnameindex + "]\" cols=\"30\" rows=\"8\"></textarea>";
 // Добавляем новый узел в конец списка полей
 document.getElementById(todiv+"_ext1").appendChild(div1);
 document.getElementById(todiv+"_ext2").appendChild(div2);
 document.getElementById(todiv+"_ext3").appendChild(div3);
 // Возвращаем false, чтобы не было перехода по сслыке
 return false;
}