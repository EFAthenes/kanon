function urlEFAEncode(str)
{
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}

function htmlEntities(str) 
{
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function getCurrentURL()
{
     var url=location.protocol + "//" + location.host + location.pathname;
     return url;
}

function isInt(n) 
{
   return typeof n === 'number' && n % 1 == 0;
}

function isFloat(n) 
{
   return ((typeof n==='number')&&(n%1!==0));
}

function isDouble(n) 
{
   return ((typeof n==='number')&&(n%1!==0));
}

function isNumber(n) 
{
   return (typeof n==='number');
}

function initSelect(name,value)
{
    //alert(selectObject+" // "+value);
    $("#"+name).val(value);
}

function initSelectValidate(name_1,name_2,validate,value)
{
    initSelect(name_2,value);
    if( $("#"+name_1).val()==-1 && $("#"+name_2).val()==-1)
    {
        $("#"+validate).hide();
    }
    else
    {
        $("#"+validate).show();
    }
}

function onchangeSelectHideDiv(name_select,validate,value)
{
    if( $("#"+name_select).val()==-1)
    {
        $("#"+validate).hide();
    }
    else
    {
        $("#"+validate).show();
    }    
}

function printObject(obj)
{
    Object.getOwnPropertyNames(obj).forEach(function(val, idx, array)
    {
        alert(val + " -> " + obj[val]);
    });
}

function infoDate(dateVar)
{
    if(dateVar!=null && dateVar!="" && dateVar.indexOf('-')!=-1)
    {
        var dateArray=dateVar.split("-");
        if(dateArray.length==3)
        {
            //alert(dateVar+" // "+dateVar[2]+"-"+dateVar[1]+"-"+dateVar[0]);
            return dateArray[2]+"-"+dateArray[1]+"-"+dateArray[0];
        }
    }
    else
    {
        //alert("Problem => "+dateVar);
    }
    return "0000-00-00";
}

function daysFromNowFrenchDate(date_1)
{
    var theDate= null;
    var dateArray=date_1.split("-");
    if(dateArray.length===3)
    { 
        theDate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
    }    
    var now=new Date();
    return daysBetweenTwoDates(now,theDate);
}

function daysBetweenTwoInfoDates(date_1,date_2)
{
    var firstDate= null;
    var dateArray=date_1.split("-");
    if(dateArray.length===3)
    { 
        firstDate = new Date(dateArray[0],dateArray[1]-1,dateArray[2]);
    }
    
    var secondDate= null;
    dateArray=date_2.split("-");
    if(dateArray.length===3)
    { 
        secondDate = new Date(dateArray[0],dateArray[1]-1,dateArray[2]);
    }    
    return daysBetweenTwoDates(firstDate,secondDate)
}
function daysBetweenTwoFrenchDates(date_1,date_2)
{
    var firstDate= null;
    var dateArray=date_1.split("-");
    if(dateArray.length===3)
    { 
        firstDate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
    }
    
    var secondDate= null;
    dateArray=date_2.split("-");
    if(dateArray.length===3)
    { 
        secondDate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
    }    
    return daysBetweenTwoDates(firstDate,secondDate)
}

function daysBetweenTwoDates(firstDate,secondDate)
{
    if(firstDate instanceof Date && secondDate instanceof Date)
    {
        var oneDayInMilliSeconds=86400000;
        //Math.abs
        var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDayInMilliSeconds))); 
//        console.log("date1=>"+firstDate.toString());
//        console.log("date2=>"+secondDate.toString());
//        console.log("Diff=>"+diffDays);
        return diffDays;
    } 
    return 0;
}

function isFrenchDateInThePast(date_1)
{
    var firstDate= null;
    var dateArray=date_1.split("-");
    if(dateArray.length===3)
    { 
        firstDate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
    } 
    var now =new Date();
    if(firstDate instanceof Date && now instanceof Date)
    {
        var oneDayInMilliSeconds=86400000;
        var diffDays = (firstDate.getTime() - now.getTime())/(oneDayInMilliSeconds); 
        console.log("diffDays =>"+diffDays);
        if(diffDays<=-1)
        {
            return true;
        }
    } 
    return false;    
}

function isFrenchDateInThePastTwoDays(date_1)
{
    var firstDate= null;
    var dateArray=date_1.split("-");
    if(dateArray.length===3)
    { 
        firstDate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
    } 
    var now =new Date();
    if(firstDate instanceof Date && now instanceof Date)
    {
        var oneDayInMilliSeconds=86400000;
        var diffDays = (firstDate.getTime() - now.getTime())/(oneDayInMilliSeconds); 
        console.log("diffDays =>"+diffDays);
        if(diffDays<=0)
        {
            return true;
        }
    } 
    return false;    
}

function addOneDayToInfoDate(dateString)
{
    var theDate= null;
    var dateArray=dateString.split("-");
    if(dateArray.length===3)
    { 
        theDate = new Date(dateArray[0],dateArray[1]-1,dateArray[2]);
    }   
    theDate=addOneDayToDate(theDate);
    if(theDate instanceof Date)
    {
        return theDate.getFullYear()+"-"+("0"+efaMonth(theDate.getMonth())).slice(-2)+"-"+("0"+theDate.getDate()).slice(-2);
    }
    else
    {
        return "0000-00-00";
    }
}
function getEfaDateToday()
{
    var now=new Date();
    return now.getFullYear()+"-"+("0"+efaMonth(now.getMonth())).slice(-2)+"-"+("0"+now.getDate()).slice(-2);
}
function getEfaFrenchDateToday()
{
    var now=new Date();
    //return now.getFullYear()+"-"+("0"+efaMonth(now.getMonth())).slice(-2)+"-"+("0"+now.getDate()).slice(-2);
    return ("0"+now.getDate()).slice(-2)+"-"+("0"+efaMonth(now.getMonth())).slice(-2)+"-"+now.getFullYear();
}
function addOneDayToFrenchDate(dateString)
{
    var theDate= null;
    var dateArray=dateString.split("-");
    if(dateArray.length===3)
    { 
        theDate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
    }
    theDate=addOneDayToDate(theDate);
    if(theDate instanceof Date)
    {
        return ("0"+theDate.getDate()).slice(-2)+"-"+("0"+efaMonth(theDate.getMonth())).slice(-2)+"-"+theDate.getFullYear();
    }
    else
    {
        return "00-00-0000";
    }    
}

function addDaysToFrenchDate(dateString,days)
{
    var theDate= null;
    var dateArray=dateString.split("-");
    if(dateArray.length===3)
    { 
        theDate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
    }
    theDate=addDaysToDate(theDate,days);
    if(theDate instanceof Date)
    {
        return ("0"+theDate.getDate()).slice(-2)+"-"+("0"+efaMonth(theDate.getMonth())).slice(-2)+"-"+theDate.getFullYear();
    }
    else
    {
        return "00-00-0000";
    }    
}


function addDaysToDate(theDate,days)
{
    //alert(theDate);
    if(theDate instanceof Date)
    {
        var oneDayInMilliSeconds=86400000*days;
        //theDate = new Date(theDate.getTime() - oneDayInMilliSeconds);
        var theDate_2 = new Date();
        theDate_2.setTime(theDate.getTime() + oneDayInMilliSeconds);
        //theDate= new Date(theDate.getYear());
        return theDate_2;
    }
    return null;
}

function addOneDayToDate(theDate)
{
    //alert(theDate);
//    if(theDate instanceof Date)
//    {
//        var oneDayInMilliSeconds=86400000;
//        //theDate = new Date(theDate.getTime() - oneDayInMilliSeconds);
//        var theDate_2 = new Date();
//        theDate_2.setTime(theDate.getTime() + oneDayInMilliSeconds);
//        //theDate= new Date(theDate.getYear());
//        return theDate_2;
//    }
    return addDaysToDate(theDate,1);
}

function rmOneDayToInfoDate(dateString)
{
    var theDate= null;
    var dateArray=dateString.split("-");
    if(dateArray.length===3)
    { 
        theDate = new Date(dateArray[0],dateArray[1]-1,dateArray[2]);
    }   
    theDate=rmOneDayToDate(theDate);
    if(theDate instanceof Date)
    {
        return theDate.getFullYear()+"-"+("0"+efaMonth(theDate.getMonth())).slice(-2)+"-"+("0"+theDate.getDate()).slice(-2);
    }
    else
    {
        return "0000-00-00";
    }
}
function rmOneDayToFrenchDate(dateString)
{
    var theDate= null;
    var dateArray=dateString.split("-");
    if(dateArray.length===3)
    { 
        theDate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
    }
    theDate=rmOneDayToDate(theDate);
    if(theDate instanceof Date)
    {
        return ("0"+theDate.getDate()).slice(-2)+"-"+("0"+efaMonth(theDate.getMonth())).slice(-2)+"-"+theDate.getFullYear();
    }
    else
    {
        return "00-00-0000";
    }    
}
function rmOneDayToDate(theDate)
{
    if(theDate instanceof Date)
    {
        var oneDayInMilliSeconds=86400000;
        var theDate_2 = new Date();
        theDate_2.setTime(theDate.getTime() - oneDayInMilliSeconds);
        return theDate_2;
    }
    return null;
}
function efaMonth(monthNumber)
{
    var m=parseInt(monthNumber)+1;
    return  m;
}

function setFrenchDateToXDays(days)
{
    var theDate= new Date();
    var oneDayInMilliSeconds=86400000;
    var theDate_2 = new Date();
    theDate_2.setTime(theDate.getTime() + days*oneDayInMilliSeconds);
    if(theDate instanceof Date)
    {
        return ("0"+theDate_2.getDate()).slice(-2)+"-"+("0"+efaMonth(theDate_2.getMonth())).slice(-2)+"-"+theDate_2.getFullYear();
    }
    else
    {
        return "00-00-0000";
    }    
}



function slashN2Br(string)
{
    return string.replace(/\n/g, "<br />");
}

function initBlockIdDisplay(divIdName,value)
{
    if(value>0)
    {
        $("#"+divIdName).show();
    }
    else
    {
        $("#"+divIdName).hide();
    }
}

function initRadioId(divIdName,value)
{
    $("input[name="+divIdName+"][value="+value+"]").attr("checked", "checked");
}

function showHideDiv(divIdName)
{
    if($("#"+divIdName).length>0)
    {
        if($("#"+divIdName).is(":visible"))
        {
            $("#"+divIdName).fadeOut("slow");
        }
        else
        {
            $("#"+divIdName).show("slow");
        }
    }
}

function showHideDivLabel(divIdName,divIdNameLabel,labelVisible,labelNotVisible)
{
    
    if(labelVisible==undefined || labelVisible=="")
    {
        labelVisible="afficher";
    }
    if(labelNotVisible==undefined || labelNotVisible=="")
    {
        labelVisible="réduire";
    }    
    
    if($("#"+divIdName).length>0)
    {
        if($("#"+divIdName).is(":visible"))
        {
            $("#"+divIdName).fadeOut("slow");
            $("#"+divIdNameLabel).html(labelVisible);
        }
        else
        {
            $("#"+divIdName).show("slow");
            $("#"+divIdNameLabel).html(labelNotVisible);
        }
    }
}


// use : BrowserDetect.browser + ' ' + BrowserDetect.version + BrowserDetect.OS
function detectBrowser()
{
    var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari",
			versionSearch: "Version"
		},
		{
			prop: window.opera,
			identity: "Opera",
			versionSearch: "Version"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			   string: navigator.userAgent,
			   subString: "iPhone",
			   identity: "iPhone/iPod"
	    },
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

    };
    BrowserDetect.init();
    return BrowserDetect;
}

function gotoHash() 
{
    if (location.hash) 
    {
        var browser=detectBrowser();
        if (browser.browser != "Chrome" && browser.browser != "Safari" && browser.browser != "Opera")
        {
            window.location.hash = location.hash
        } 
        else 
        {
            window.location.href = location.hash;
            window.location.href = location.hash
        }
    }
}

function objectToString(obj)
{
    var the_string="";
    if(obj==null)
    {
        alert("obj==null");
        return;
    }
    Object.getOwnPropertyNames(obj).forEach(function(val, idx, array)
    {
        the_string+=val + " -> " + obj[val]+"<br />";
    });
    return the_string;
}

function autoSizeTextArea(id_name)
{
    $("#"+id_name).each(function () 
    {
        this.style.height = (this.scrollHeight)+"px";
    });
}

function setDefaultNumberInteger(object)
{
    if(object.value.length==0)
    {
        object.value="0";
    }
    object.value=parseInt(object.value);
}
function setDefaultNumberIntegerPositive(object)
{
    if(object.value.length==0)
    {
        object.value="0";
    }
    object.value=parseInt(object.value);
    if(object.value<=0)
    {
        object.value=1;
    }
}

function setDefaultNumberFloat(object)
{
    if(object.value.length==0)
    {
        object.value="0";
    }
    object.value=parseFloat(object.value);
}
function validateStringEmail(email)
{
    const validateEmail = (email) => {
      return String(email)
        .toLowerCase()
        .match(
          /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
    };
    return validateEmail(email);
}
