 var _ID_PLAYING=0;
 var _ROOT_FOLDER="./library/music/";
 var ST_PLAYING=1;
 var ST_STOPPED=2;
 var ST_PAUSED=3;
 var _PLAYER_STATE=ST_STOPPED;
 $(document).on("ready",function(){
    next();
    $("#main-player").on('ended', function(){
        // done playing
        _PLAYER_STATE=ST_PLAYING;
        next();
    });
     $("#main-player").on('play', function(){
       console.log("play - "+_ID_PLAYING);
       _PLAYER_STATE=ST_PLAYING;
       play(_ID_PLAYING,false,false);
     });
     $("#main-player").on('pause', function(){
       console.log("pause - "+_ID_PLAYING);
       _PLAYER_STATE=ST_PAUSED;
       pause();
     });
 });
 
 
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
            alert(mesg.eror);
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
    if (typeof (id)==="undefined")
        id=_ID_PLAYING;
    GetDataFromWS("localhost/Player/service/next/id/"+id,"[]","POST",
            function(obj){
                $("#titlu-melodie").text(obj.response[0].artist+" - "+obj.response[0].title);
                _ID_PLAYING= parseInt(obj.response[0].id_mel);
                $("#main-player > source").attr("src",_ROOT_FOLDER+obj.response[0].path);
               var player=$('#main-player').get(0);
                player.load();
                if ( _PLAYER_STATE===ST_PLAYING)
                {
                    player.play();
                    _PLAYER_STATE=ST_PLAYING;
                }
               setAlbumArt(obj.response[0].album_art); 
            }
      );
}
function prev(id)
{
    if (typeof (id)==="undefined")
        id=_ID_PLAYING;
    GetDataFromWS("localhost/Player/service/prev/id/"+id,"[]","POST",
            function(obj){
                 $("#titlu-melodie").text(obj.response[0].artist+" - "+obj.response[0].title);
                _ID_PLAYING= parseInt(obj.response[0].id_mel);
                $("#main-player > source").attr("src",_ROOT_FOLDER+obj.response[0].path);
               var player=$('#main-player').get(0);
                player.load();
                if ( _PLAYER_STATE===ST_PLAYING)
                {
                    player.play();
                    _PLAYER_STATE=ST_PLAYING;
                }
               setAlbumArt(obj.response[0].album_art); 
            }
      );
}
function setAlbumArt(data)
{
    $(".container-album-art").css(
            {
                backgroundImage:"url('"+data+"')"
            });
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
    if (typeof (id)==="undefined")
        id=_ID_PLAYING;
     if (typeof (startPlay)==="undefined")
        startPlay=false;
    //se apeleaza la play in controlul audio-> doar pt a modifica in baza de date playing la 1
    if (typeof (loadSong)==="undefined")
        loadSong=true;
    GetDataFromWS("localhost/Player/service/play/id/"+id,"[]","POST",
            function(obj){
                $("#titlu-melodie").text(obj.response[0].artist+" - "+obj.response[0].title);
                _ID_PLAYING= parseInt(obj.response[0].id_mel);
                if (loadSong)
                {
                    $("#main-player > source").attr("src",_ROOT_FOLDER+obj.response[0].path);
                    var player=$('#main-player').get(0);
                    player.load();
                    if (startPlay)
                    {
                        player.play();
                        _PLAYER_STATE=ST_PLAYING;
                    }
                    setAlbumArt(obj.response[0].album_art);
                }
            }
      );
}