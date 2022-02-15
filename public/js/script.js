function status_user(class1,class2)
{
	var status_user = ['online','offline','dnd','bys'];
	$.each(status_user,function(k,v){
		$('.'+class1).removeClass(v);
	});


		$('.'+class1).addClass(class2);

}

$(document).ready(function(){

   		 var mylist = [];
   		 $('.user').each(function(){
				var uid = $(this).attr('uid');
				 mylist.push(uid);
			});

   		 //////
   		 var my_status = $('.current_status').attr('status');
   		// console.log(my_status);
   		var socket = io.connect('http://localhost:5000',{
   			query:'user_id='+user_id+'&username='+username+'&mylist='+mylist.join(',')+'&status='+my_status
   		});


   	 	var array_emit = ['is_online','iam_online','iam_offline','new_status'];
   		$.each(array_emit,function(k,v){
   			socket.on(v,function(data){
   				status_user(data.user_id,data.status);
   			});

   		});

   		socket.on('request_status',function(data){
   			console.log($('.current_status').attr('status'));
   			socket.emit('response_status',{
   				to_user:data.user_id,
   				my_status:$('.current_status').attr('status')
   			});
   		});



		socket.on('connect',function(data){
			$('.user').each(function(){
				var uid = $(this).attr('uid');
				socket.emit('check_online',{
					user_id:'user_'+uid
				});
			});
		});



		$(document).on('click','.status',function(){
			var status_user = $(this).attr('status');
			$('.current_status').attr('status',status_user);

			if(status_user == 'dnd')
			{
			 $('.current_status').text("don't disturb");
			}else if(status_user == 'bys'){
			 $('.current_status').text('Busy');
			}else{
			 $('.current_status').text(status_user);
			}

			socket.emit('change_status',{
				status:status_user
			});
		});




	 var arr = []; // List of users

	$(document).on('click', '.msg_head', function() {
		var chatbox = $(this).parents().attr("rel") ;
		$('[rel="'+chatbox+'"] .msg_wrap').slideToggle('slow');
		return false;
	});


	$(document).on('click', '.close', function() {
		var chatbox = $(this).parents().parents().attr("rel") ;
		$('[rel="'+chatbox+'"]').hide();
		arr.splice($.inArray(chatbox, arr), 1);
		displayChatBox();
		return false;
	});


	function private_chatbox(username,userID)
	{
	 if ($.inArray(userID, arr) != -1)
	 {
      arr.splice($.inArray(userID, arr), 1);
     }

	 arr.unshift(userID);
	 chatPopup =  '<div class="msg_box box'+userID+'" style="right:270px" name_to="' +username +'" rel="'+userID+'">'+
					'<div id ="msg_head" class="msg_head">'+username +
					'<div class="close">x</div> </div>'+
					'<div class="msg_wrap"> <div class="msg_body">	<div class="msg_push"></div> </div>'+
					'<div class="msg_footer"><span class="broadcast"></span><textarea class="msg_input" rows="4"></textarea></div> 	</div> 	</div>' ;

      $("body").append(  chatPopup  );
      var to_id = userID.replace("user_", "");
        //alert(to_id+'----'+user_id);
        var messages = getMessages(user_id, to_id);
	  displayChatBox();
	}



	$(document).on('click', '#sidebar-user-box', function() {
		var userID = $(this).attr("uid");
	   var username = $(this).children().text() ;
	   private_chatbox(username,'user_'+userID);

	});



	socket.on('new_private_msg',function(data){
		if(!$('.msg_box').hasClass('box'+data.from_uid))
		{
		   private_chatbox(data.username,data.from_uid);
		}

		$('.box'+data.from_uid+' .broadcast').html('');

		if(data.whois == 'user_'+user_id)
		{
			var textclass = 'msg-right';
		}else{
			var textclass = 'msg-left';
		}

		$('<div class="'+textclass+'">'+data.username+':'+data.message+'</div>').insertBefore('[rel="'+data.from_uid+'"] .msg_push');
		$('.msg_body').scrollTop($('.msg_body')[0].scrollHeight);
	});


	$(document).on('keypress', 'textarea' , function(e) {
		var chatbox = $(this).parents().parents().parents().attr("rel");
        if (e.keyCode == 13) {
            var msg = $(this).val();

			$(this).val('');
			if(msg.trim().length != 0){
					socket.emit('send_private_msg',{
						message:msg,
						to:chatbox
					});

					var name_to = $(this).parents().parents().parents().attr("name_to");
                var id_to = $(this).parents().parents().parents().attr("rel");
                id_to = id_to.replace("user_", "");
                //alert(user_id+'---'+id_to+'---'+username+'---'+name_to+'---'+msg);
             saveMessages(user_id, id_to, username, name_to, msg);
			}
        }else{
        	socket.emit('broadcast_private',{
        		to:chatbox,
        		username:username,
        	});
        }
        //alert(msg);
        //alert(username);
	 // savemesages(username,msg,chatbox);

    });

	socket.on('new_broadcast',function(data){

			$('.box'+data.from+' .broadcast').html('<span style="font-size:10px;float:left">'+data.username+'</span> <img class="pull-right" src="'+typingurl+'" />');

			setTimeout(function(){
					$('.box'+data.from+' .broadcast').html('');
				},10000);
	});


	function displayChatBox(){
	    i = 270 ; // start position
		j = 260;  //next position

		$.each( arr, function( index, value ) {
		   if(index < 4){
	         $('[rel="'+value+'"]').css("right",i);
			 $('[rel="'+value+'"]').show();
		     i = i+j;
		   }
		   else{
			 $('[rel="'+value+'"]').hide();
           }
           //alert(value);
        });


	}

function savemesages(name,message,to){
/*alert(name);
alert(message);
alert(to);
return;*/

    if(message) {
        $.ajax({
            url: 'http://localhost/mafqoud/savechat',
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            data:{name:name,message:message,to:to},
            dataType: "json",
            success:function(data) {


            }
        });
    }
}



function saveMessages(from_id, to_id, from_name, to_name, message) {
        //console.log('from_id: ' + from_id + ' to_id: '+to_id + ' from_name: '+from_name+ ' to_name: '+to_name + ' message: '+message );

        $.ajax({
            type: "POST",
            data: {
                from_id: from_id,
                to_id: to_id,
                from_name: from_name,
                to_name: to_name,
                message: message,
            },
            dataType: "json",
			url: 'http://localhost/mafqoud/savechat',
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            success: function (data) {
                console.log(data.status);
                //$("#success").html(data);
            },
        });
    }





    function getMessages(from_id, to_id) {
        $.ajax({
            type: "get",
            data: {
                from_id: from_id,
                to_id: to_id,
            },
            url: "chat",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                console.log(data.messages);
                $.each(data.messages, function (i, item) {
					//alert(user_id);
					if(item.from_id == user_id){
						$('<div class="msg-right"><i style="font-size:18px;color:red" class=" fa fa-arrow-circle-left"></i>&nbsp;&nbsp;'+item.from_name+':'+item.message+'</div>').insertBefore('[rel="user_'+item.to_id+'"] .msg_push');
					}
					else{
						$('<div class="msg-left"><i style="font-size:18px;color:blue" class=" fa fa-arrow-circle-right"></i>&nbsp;&nbsp;'+item.from_name+':'+item.message+'</div>').insertBefore('[rel="user_'+item.from_id+'"] .msg_push');
					}

                });


                $('.msg_body').scrollTop($('.msg_body')[0].scrollHeight);
            },
        });
    }
















});
