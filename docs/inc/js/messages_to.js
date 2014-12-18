var suggest_count = 0; 
var searchq = ""; 

$(document).ready(function(){ 
$("*:not(.resultdropdown)").click(function() { 
$(".resultdropdown").html(""); 
});     
}); 

function select_popup(num) 
{ 
    num--; 
    $("#result table tr").removeClass("active"); 
    $("#result table tr").eq(num).addClass("active"); 
} 

var popup_counter=0; 

function check_form()
{
    //alert ("началось");    
    obj_to_id=document.getElementById("to_id");
    obj_text=document.getElementById("content")
    if(obj_to_id.value>0 & obj_text.value!="")
    {
        document.getElementById("message_form").onsubmit=function() { return true };        
    }
}

function keydown(el,evt) 
{ 
    if (!evt) var evt = window.event; 
    var key = evt.keyCode || evt.which;
    //alert(key);
    if (key==13) //Tab=9 Enter 13
    {   //alert("1111"+popup_counter);
        document.getElementById("message_form").onsubmit=function() { return false };

        obj1=document.getElementById("to_id");        
        obj1.value=$("#result table tr").eq(popup_counter-1).find("td").attr("value");        
        
         
        //window.location=$("#result table tr").eq(popup_counter-1).find("td").find("a").attr("href");
        obj=document.getElementById("message_to");        
        obj.value=$("#result table tr").eq(popup_counter-1).find("td").attr("alttext");
        if (obj.value!="")  
        {
                $(".resultdropdown").html("");
                popup_counter=0;
        }
        delay(500);
        
    }    
    else if (key == 27) // esc 
    { 
        $(".resultdropdown").html(""); 
        popup_counter=0; 
    } 
}
 
function search(el, evt) 
{ 
    if (!evt) var evt = window.event; 
    var key = evt.keyCode || evt.which;
    //alert(key); 
    if (key==0 || key==8 || ( key > 45 && key < 112) || (key > 123)) 
    { 
        suggest_count++; 
        var offset = $(el).offset(); 
        var top = offset.top+18; 
        var left = offset.left; 
        searchq = $(el).val(); 
        //searchq=key;
        setTimeout("searchGo ("+top+","+left+","+suggest_count+")",300); 
    } 
    else if (key==40) // Down 
    { 
        //document.getElementById("message_form").onsubmit=function() {return false}; 
        if (popup_counter<$("#result table tr").size())
        {
             popup_counter++;    
        }
        else
        {
            popup_counter=1;
        }
         
        select_popup(popup_counter); 
    } 
    else if (key==38 ) // Up 
    { 
        //document.getElementById("message_form").onsubmit=function() {return false}; 
        if (popup_counter>1)
        {
            popup_counter--;    
        }
        else
        {
            popup_counter=$("#result table tr").size();
        }
         
        select_popup(popup_counter); 
    } 
} 
 
 
function searchGo(top, left, count) 
{ 
    if (count == suggest_count) 
    { 
        var window = $("#result");
        obj_type_to=document.getElementById("selected_button");        
        //alert(obj_type_to.value); 
        window.css('width',400).css('left', left).css('top', top).css('z-index', '10005');        
        $.ajax
        ({ 
            url: '/search.php?q='+searchq+'&table='+obj_type_to.value, 
            cache: false, 
            success: function(html) { 
                                        window.html(html); 
                                    } 
        });   
    } 
}

function vote_up(el, evt) 
{     
    $.ajax
    ({ 
        url: '/add_vote.php',
        type: 'POST', 
        data: 'question_id='+$("#question_id").val()+'&vote=up',
        cache: false, 
        success: function(rate) { 
                                    $("#result").css('visibility','hidden');
                                    $("#rate").text(rate);
                                } 
    });
}

function vote_down(el, evt) 
{ 
    $.ajax
    ({ 
        url: '/add_vote.php',
        type: 'POST', 
        data: 'question_id='+$("#question_id").val()+'&vote=down',
        cache: false, 
        success: function(rate) { 
                                    $("#result").css('visibility','hidden');
                                    $("#rate").text(rate);
                                } 
    });
}

function fill_fields(popup_counter)
{
        obj1=document.getElementById("to_id");        
        obj1.value=$("#result table tr").eq(popup_counter-1).find("td").attr("value");
        

        document.getElementById("message_form").onsubmit=function() { return false };

         
        //window.location=$("#result table tr").eq(popup_counter-1).find("td").find("a").attr("href");
        obj=document.getElementById("message_to");        
        obj.value=$("#result table tr").eq(popup_counter-1).find("td").attr("alttext");
        if (obj.value!="")  
        {
                $(".resultdropdown").html("");
                popup_counter=0;
        }
    
}