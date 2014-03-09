 var _ID_PLAYING=0;
 var _ROOT_FOLDER="./library/music/";
 var ST_PLAYING=1;
 var ST_STOPPED=2;
 var ST_PAUSED=3;
 var _PLAYER_STATE=ST_STOPPED;
 var _SlideSpeed=300;

 $(document).on("ready",function(){
    getPlaylist();
    $("#main-player").on('ended', function(){
        // done playing
        _PLAYER_STATE=ST_PLAYING;
        next();
    });
     $("#main-player").on('play', function(){
       console.log("play - "+_ID_PLAYING);
       _PLAYER_STATE=ST_PLAYING;
       play(_ID_PLAYING,false,false);
         $(".action-album-art-pause").removeClass("action-album-art-pause").addClass("action-album-art-play");
      $('.container-album-art [id_to_play="'+_ID_PLAYING+'"]').removeClass("action-album-art-play")
              .addClass("action-album-art-pause").off().on("click",albumArtPause);
     });
     $("#main-player").on('pause', function(){
       console.log("pause - "+_ID_PLAYING);
       _PLAYER_STATE=ST_PAUSED;
       
         $('.container-album-art [id_to_play="'+_ID_PLAYING+'"]').removeClass("action-album-art-pause").
                 addClass("action-album-art-play").off().on("click",function(){albumArtPlay($(this).attr("id_to_play"));});
       pause();
     });
     
     //-----------album art controls------------
     
     $(".control-album-art-top").hide();
     $(".control-album-art-bottom").hide();
     $(".container-album-art").on("mouseover",function(){
        $(this).children(".control-album-art-top").fadeIn(_SlideSpeed);
        $(this).children(".control-album-art-bottom").fadeIn(_SlideSpeed);
     }).on("mouseleave",function(){
        $(this).children(".control-album-art-top").fadeOut(_SlideSpeed);
        $(this).children(".control-album-art-bottom").fadeOut(_SlideSpeed);  
     });
     $(".action-album-art-play").off().on("click",function(){albumArtPlay($(this).attr("id_to_play"));});
     $(".action-album-art-pause").off().on("click",albumArtPause);
     
     //-----------------------------------------
     
 });
 function albumArtPlay(id_mel){
     if (typeof id_mel=='undefined')
         id_mel=_ID_PLAYING;
     if (_ID_PLAYING==id_mel)
        $("#main-player").get(0).play();
    else
        play(id_mel,true);  
   $(".action-album-art-pause").off().on("click",albumArtPause);
         
 }
 function albumArtPause(){
         $("#main-player").get(0).pause();
     $(".action-album-art-play").off().on("click",function(){albumArtPlay($(this).attr("id_to_play"));});
     }
     
 function activateAlbumControlsPP(elem)
 {
     
    $(".container-album-art").each(function(){
        $(this).on("mouseover",function(){
            $(this).children(".control-album-art-top").fadeIn(_SlideSpeed);
            $(this).children(".control-album-art-bottom").fadeIn(_SlideSpeed);
        }).on("mouseleave",function(){
            $(this).children(".control-album-art-top").fadeOut(_SlideSpeed);
            $(this).children(".control-album-art-bottom").fadeOut(_SlideSpeed);  
        });
    $(this).children(".control-album-art-bottom").children(".action-album-art-play").off().on("click",
                function(){albumArtPlay($(this).attr("id_to_play"));});
     $(this).children(".control-album-art-bottom").children(".action-album-art-pause").off().on("click",albumArtPause);
    });
 }
 function GenerateJSONString(arNumeParam,arValParam)
{
    var theString='';
    var theObject={};
    
    for (var i=0; i < arNumeParam.length;i++)
        {
          
          theObject[arNumeParam[i]]=arValParam[i];
        }
        theString=JSON.stringify(theObject);
//        alert(theString);
     return theString;   
}

function GetDataFromWS(numePagina,params,submitMethod,callBackFunction)
{
    
    submitMethod=submitMethod||"POST";
    params=params||"[]";
    var Jparams=JSON.parse(params);
    var request = $.ajax({
      url: numePagina,
      type: submitMethod,
      
      data: Jparams//{id_pag : idPagina}
    });
 
    
    request.done(function(msg) {
        
        var aObj=msg;
        if (msg.error)
        {
            alert(mesg.error);
            return;
        }
       console.log(msg);
//     var aObj=JSON.parse(msg);
        if (callBackFunction)
        {
           var callbacks = $.Callbacks();
           callbacks.add(callBackFunction);
           callbacks.fire(aObj);
        }
    });
    request.fail(function(jqXHR, textStatus) {
      alert( "Request failed: " + textStatus );
    });
}
function next(id)
{
    var URI="localhost/Player/service/next";
    if (typeof (id)==="undefined")
    {
        id=0;
    }
    else
    {
       URI+="/id/"+id; 
    }
    GetDataFromWS(URI,"[]","POST",
            function(obj){
                // $("#titlu-melodie").text(obj.response[0].artist+" - "+obj.response[0].title);
                changeSongTitle(obj.response[0].artist, obj.response[0].title);
                _ID_PLAYING= parseInt(obj.response[0].id_mel);
                $("#main-player > source").attr("src",_ROOT_FOLDER+obj.response[0].path);
               var player=$('#main-player').get(0);
                player.load();
                if ( _PLAYER_STATE===ST_PLAYING)
                {
                    player.play();
                    _PLAYER_STATE=ST_PLAYING;
                }
               setAlbumArt(obj.response[0].album_art,"album-art"); 
            }
      );
}
function prev(id)
{
     var URI="localhost/Player/service/prev";
    if (typeof (id)==="undefined")
    {
        id=0;
    }
    else
    {
       URI+="/id/"+id; 
    }
    GetDataFromWS(URI,"[]","POST",
            function(obj){
                 // $("#titlu-melodie").text(obj.response[0].artist+" - "+obj.response[0].title);
                 changeSongTitle(obj.response[0].artist, obj.response[0].title);
                _ID_PLAYING= parseInt(obj.response[0].id_mel);
                $("#main-player > source").attr("src",_ROOT_FOLDER+obj.response[0].path);
               var player=$('#main-player').get(0);
                player.load();
                if ( _PLAYER_STATE===ST_PLAYING)
                {
                    player.play();
                    _PLAYER_STATE=ST_PLAYING;
                }
               setAlbumArt(obj.response[0].album_art,"album-art"); 
            }
      );
}
function setSong(id_container,dataAlbumArt,selected,resetOthers)
{
    if (typeof resetOthers=='undefined'){
        resetOthers=true;
    }
    $("#"+id_container).css(
            {
                backgroundImage:"url('"+dataAlbumArt+"')"//'./library/img/default.jpg'+"')"
            });
   if (resetOthers){
       $(".selected-song").each(function(){
           $(this).removeClass("selected-song");
       });
   }
   if (selected)
   {
        $("#"+id_container).addClass("selected-song");    
   }
}
function loadSong(path,text)
{
    $("#titlu-melodie").text(text);
    $("#main-player > source").attr("src",_ROOT_FOLDER+path);
                    var player=$('#main-player').get(0);
                    player.load();
}
function pause()
{
     GetDataFromWS("localhost/Player/service/pause","[]","POST",
            function(obj){
                console.log(obj.response[0]);
            }
      );
}
function play(id,startPlay,loadSong)
{
    var idElement=-1;
    var URI="localhost/Player/service/play";
    if (typeof (id)==="undefined")
    {
        id=0;
        idElement=$(".selected-song").attr("id");
    }
    else
    {
      idElement= $('.container-album-art [id_to_play="'+id+'"]').parent().parent().attr("id");
       URI+="/id/"+id; 
    }
     if (typeof (startPlay)==="undefined")
        startPlay=false;
    //se apeleaza la play in controlul audio-> doar pt a modifica in baza de date playing la 1
    if (typeof (loadSong)==="undefined")
        loadSong=true;
    GetDataFromWS(URI,"[]","POST",
            function(obj){
                // $("#titlu-melodie").text(obj.response[0].artist+" - "+obj.response[0].title);
                changeSongTitle(obj.response[0].artist, obj.response[0].title);
                _ID_PLAYING= parseInt(obj.response[0].id_mel);
                if (loadSong)
                {
                    $("#main-player > source").attr("src",_ROOT_FOLDER+obj.response[0].path);
                    var player=$('#main-player').get(0);
                    player.load();
                    if (startPlay)
                    {
                        albumArtPlay(_ID_PLAYING);
                    }
                   setSong(idElement,obj.response[0].album_art,true,true);
                }
            }
      );
}
function getPlaylist()
{
    var URI="localhost/Player/service/playlist";
    GetDataFromWS(URI,"[]","POST",
        function(obj){ 
           $(".container-album-art").remove();
           for(var i=0;i<obj.response.length;i++)
            {
                var melodie=obj.response[i];
                var idAlbumArt=AvailableID("album-art");
                var idControlPP=AvailableID("action-album-art-pp");
                var htmlAlbumArt='<div class="  container  container-album-art center"'+
                     //   ' id_to_play="'+melodie.id_mel+'" ' +
                    'id="'+idAlbumArt+'">'+
                    '<div class="control-album-art-top" >'+
                        '<span class="text-info-mel">'+melodie.title+'</span>'+
                    '</div>'+
                    '<div class="control-album-art-bottom">'+
                        '<div id="'+idControlPP+'" '+
                        ' id_to_play="'+melodie.id_mel+'" '+
                        ' class="cursor-pointer pull-left '+
                        ((melodie.playing=='2')?' action-album-art-pause':' action-album-art-play')+
                        '"></div>'+
                        
                    '</div>'+
                '</div>'
                ;
                  
                $("#container-album-arts").append(htmlAlbumArt);
                if (melodie.playing!='0'){
                    _ID_PLAYING=melodie.playing;
                    setSong(idAlbumArt,melodie.album_art,true,false);
                        loadSong(melodie.path,melodie.artist+' - '+melodie.title);
                }
                else{
                  setSong(idAlbumArt,melodie.album_art,false,false);
                }
                    console.log(melodie);
           }
           activateAlbumControlsPP();
           $(".control-album-art-top").hide();
                $(".control-album-art-bottom").hide();
            $(".action-album-art-play").off().on("click",function(){albumArtPlay($(this).attr("id_to_play"));});
              $(".action-album-art-pause").off().on("click",albumArtPause);
     });
}


function AvailableID(theID)
{
    if (!($('#'+theID)[0]))
        return theID;
   var i=0;
    while ($('#'+theID+i)[0])
        {
            i++;
        } 
    return theID+''+i;
}

function changeSongTitle(artist, title) {
    $("#titlu-melodie").text(artist + " - " + title);
}
