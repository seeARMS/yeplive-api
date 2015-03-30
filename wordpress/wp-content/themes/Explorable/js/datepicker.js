// Converts a date into '12-Oct-1984' format
function getDateString(dt) {
	var month = dt.getMonth() + 1;
	month = month.toString().length == 1 ? "0" + month : month;
	var intDate = dt.getDate();
	intDate = intDate.toString().length == 1 ? "0" + intDate : intDate;
  return dt.getFullYear() + '/' + month + "/" + intDate;
}

// Converts a date into 'July 2010' format
function getMonthYearString(dt) {
  return ['January','February','March','April','May','June','July',
          'August','September','October','November','December'][dt.getMonth()] +
         ' ' + dt.getFullYear();
}

// This is the function called when the user clicks any button
function chooseDate(e) {
  var targ; // Crossbrowser way to find the target (http://www.quirksmode.org/js/events_properties.html)
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) targ = targ.parentNode; // defeat Safari bug

  var div = targ.parentNode.parentNode.parentNode.parentNode.parentNode; // Find the div
  var idOfTextbox = div.getAttribute('datepickertextbox'); // Get the textbox id which was saved in the div
  var textbox = document.getElementById(idOfTextbox); // Find the textbox now
  if (targ.value=='<' || targ.value=='>') { // Do they want the change the month?
    createCalendar(div, new Date(targ.getAttribute('date')));
    return;
  }
  textbox.value = targ.getAttribute('date'); // Set the selected date
  div.parentNode.removeChild(div); // Remove the dropdown box now
}

// Parse a date in d-MMM-yyyy format
function parseMyDate(d) {
  if (d=="") return new Date('NotADate'); // For Safari
  var a = d.split('-');
  if (a.length!=3) return new Date(d); // Missing 2 dashes
  var m = -1; // Now find the month
  if (a[1]=='Jan') m=0;
  if (a[1]=='Feb') m=1;
  if (a[1]=='Mar') m=2;
  if (a[1]=='Apr') m=3;
  if (a[1]=='May') m=4;
  if (a[1]=='Jun') m=5;
  if (a[1]=='Jul') m=6;
  if (a[1]=='Aug') m=7;
  if (a[1]=='Sep') m=8;
  if (a[1]=='Oct') m=9;
  if (a[1]=='Nov') m=10;
  if (a[1]=='Dec') m=11;
  if (m<0) return new Date(d); // Couldn't find the month
  return new Date(a[2],m,a[0],0,0,0,0);
}

// This creates the calendar for a given month
function createCalendar(div, month) {
	
  var idOfTextbox = div.getAttribute('datepickertextbox'); // Get the textbox id which was saved in the div
  var textbox = document.getElementById(idOfTextbox); // Find the textbox now
  var tbl = document.createElement('table');
  var topRow = tbl.insertRow(-1);
  var td = topRow.insertCell(-1);
  var lastMonthBn = document.createElement('input');
  lastMonthBn.id = 'leftMYBtn';
  lastMonthBn.type='button'; // Have to immediately set the type due to IE
  td.appendChild(lastMonthBn);
  lastMonthBn.value='<';
  lastMonthBn.onclick=chooseDate;
  lastMonthBn.setAttribute('date',new Date(month.getFullYear(),month.getMonth()-1,1,0,0,0,0).toString());
  var td = topRow.insertCell(-1);
  td.colSpan=5;
  var mon = document.createElement('input');
  mon.type='text';
  td.appendChild(mon);
  mon.value = getMonthYearString(month);
  mon.size=15;
  mon.disabled='disabled';
  var td = topRow.insertCell(-1);
  var nextMonthBn = document.createElement('input');
  nextMonthBn.id = 'nextMYBtn';
  nextMonthBn.type='button';
  td.appendChild(nextMonthBn);
  nextMonthBn.value = '>';
  var element = nextMonthBn;
  
  nextMonthBn.onclick=chooseDate;
  nextMonthBn.setAttribute('date',new Date(month.getFullYear(),month.getMonth()+1,1,0,0,0,0).toString());
  var daysRow = tbl.insertRow(-1);
  daysRow.insertCell(-1).innerHTML="Mon";
  daysRow.insertCell(-1).innerHTML="Tue";
  daysRow.insertCell(-1).innerHTML="Wed";
  daysRow.insertCell(-1).innerHTML="Thu";
  daysRow.insertCell(-1).innerHTML="Fri";
  daysRow.insertCell(-1).innerHTML="Sat";
  daysRow.insertCell(-1).innerHTML="Sun";
  
  // Make the calendar
  var selected = parseMyDate(textbox.value); // Try parsing the date
  var today = new Date();
  date = new Date(month.getFullYear(),month.getMonth(),1,0,0,0,0); // Starting at the 1st of the month
  var extras = (date.getDay() + 6) % 7; // How many days of the last month do we need to include?
  date.setDate(date.getDate()-extras); // Skip back to the previous monday
  while (1) { // Loop for each week
    var tr = tbl.insertRow(-1);
    for (i=0;i<7;i++) { // Loop each day of this week
      var td = tr.insertCell(-1);
      var inp = document.createElement('input');
      inp.type = 'button';
      td.appendChild(inp);
      inp.value = date.getDate();
      inp.onclick = chooseDate;
      inp.setAttribute('date',getDateString(date));
      if (date.getMonth() != month.getMonth()) {
        if (inp.className) inp.className += ' ';
        inp.className+='othermonth';
      }
      if (date.getDate()==today.getDate() && date.getMonth()==today.getMonth() && date.getFullYear()==today.getFullYear()) {
        if (inp.className) inp.className += ' ';
        inp.className+='today';
      }
      if (!isNaN(selected) && date.getDate()==selected.getDate() && date.getMonth()==selected.getMonth() && date.getFullYear()==selected.getFullYear()) {
        if (inp.className) inp.className += ' ';
        inp.className+='selected';
      }
      date.setDate(date.getDate()+1); // Increment a day
    }
    // We are done if we've moved on to the next month
    if (date.getMonth() != month.getMonth()) {
      break;
    }
  }
  
  // At the end, we do a quick insert of the newly made table, hopefully to remove any chance of screen flicker
  if (div.hasChildNodes()) { // For flicking between months
	  
    div.replaceChild(tbl, div.childNodes[0]);
  } else { // For creating the calendar when they first click the icon
	  
    div.appendChild(tbl);
  }
}

// This is called when they click the icon next to the date inputbox
function showDatePicker(idOfTextbox) {
	
  var textbox = document.getElementById(idOfTextbox);
  
  // See if the date picker is already there, if so, remove it
  x = textbox.parentNode.getElementsByTagName('div');
  
  for (i=0;i<x.length;i++) {
    if (x[i].getAttribute('class')=='datepickerdropdown') {
      textbox.parentNode.removeChild(x[i]);
      return false;
    }
  }
  
  // Grab the date, or use the current date if not valid
  var date = parseMyDate(textbox.value);
  if (isNaN(date)) date = new Date();

  // Create the box
  var div = document.createElement('div');
  div.name = 'dateDiv';
  div.style.zIndex = "1000";
  div.className='datepickerdropdown';
  div.setAttribute('datepickertextbox', idOfTextbox); // Remember the textbox id in the div
  createCalendar(div, date); // Create the calendar
  insertAfter(div, textbox); // Add the box to screen just after the textbox
  return false;
}

// Adds an item after an existing one
function insertAfter(newItem, existingItem) {
  if (existingItem.nextSibling) { // Find the next sibling, and add newItem before it
    existingItem.parentNode.insertBefore(newItem, existingItem.nextSibling); 
  } else { // In case the existingItem has no sibling after itself, append it
    existingItem.parentNode.appendChild(newItem);
  }
}

// This is called when the page loads, it searches for inputs where the class is 'datepicker'
function datePickerInit() {
  // Search for elements by class
	
  var allElements = document.getElementsByTagName("*");
  for (i=0; i<allElements.length; i++) {
    var className = allElements[i].className;
    if (className=='datepicker' || className.indexOf('datepicker ') != -1 || className.indexOf(' datepicker') != -1) {
      // Found one! Now lets add a datepicker next to it
    	
      var a = document.createElement('a');
      //a.name = "dateLink";
      a.href='#';
      a.className='datepickershow'
      a.setAttribute('onclick','return showDatePicker("' + allElements[i].id + '")');
      var img = document.createElement('img');
      img.src='../img/calendar.jpg';
      
      img.name = 'dateImg';
      img.title='Show calendar';
	  img.style.width = "35px";
	  img.style.height = "35px";
	  img.style.position = "relative";
	  img.style.top = "13px";
      a.appendChild(img);
      insertAfter(a, allElements[i]);
    }
  }
}
var flagflag1 = false;
function clickElement(element)
{
	if(element.name == 'dateImg' )
		return;
	if(element.id == 'nextMYBtn')
		return;
	if(element.id == 'leftMYBtn')
		return;
	var flagflag2 = false;
	while(element.parentNode)
	{
		if(element.className == 'datepickerdropdown' )
		{
			flagflag2 = true;
			break;
		}
		element = element.parentNode;
	}
	if(flagflag2 == true)
		return;
	x1 = document.getElementsByTagName("*");
	
	  for(k = 0; k < x1.length; k++)
	  {
		  if(x1[k].getAttribute('class')=='datepickerdropdown')
		  {
			  flagflag1 = false;
			  explorerFunc(element,x1[k],x1[k]);
			  if(flagflag1 == false)
			  {
				  x1[k].parentNode.removeChild(x1[k]);
			  }
		  }
	  }
}
function explorerFunc(element,childElement,dateDiv)
{
	for(i = 0; i < childElement.length; i++)
	{
		explorerFunc(element,childElement[i],dateDiv);
		if(element == childElement[i])
		{
			flagflag1 = true;
		}
	}
}
// Hook myself into the page load event
if (window.addEventListener) { // W3C standard
  window.addEventListener('load', datePickerInit, false);
  window.addEventListener('click', function(e){  clickElement(e.target); }, false);
} else if (window.attachEvent) { // Microsoft
  window.attachEvent('onload', datePickerInit);
  window.attachEvent('onclick', function(){  clickElement(window.event.srcElement);});
}
