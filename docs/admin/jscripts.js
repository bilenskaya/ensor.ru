function razver(id)
{
  var div = document.getElementById(id).style;
  if ( 'none' == div.display )
  {
		div.display = 'block';
  }
  else
  {
		div.display = 'none';
  }
}
