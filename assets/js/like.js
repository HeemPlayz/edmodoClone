$(function(){

    $('.fa-thumbs-up').css("color","blue");
    $(document).on('click','.like-btn',function(){
        var tweet_id = $(this).data('tweet');
        var user_id = $(this).data('user');
        var counter = $(this).find('.likesCounter');
        var count = counter.text();
        var button =$(this);

        $.post('http://localhost/edmodoClone/core/ajax/like.php',{like:tweet_id,user_id:user_id},function(){
            counter.show();
            button.addClass('unlike-btn');
            button.removeClass('like-btn');
            count++;
            counter.text(count);
            button.find('.fa-thumbs-o-up').addClass('fa-thumbs-up');
            button.find('.fa-thumbs-up').removeClass('fa-thumbs-o-up');
        });
    });

    $(document).on('click','.unlike-btn',function(){
        var tweet_id = $(this).data('tweet');
        var user_id = $(this).data('user');
        var counter = $(this).find('.likesCounter');
        var count = counter.text();
        var button =$(this);

        $.post('http://localhost/edmodoClone/core/ajax/like.php',{unlike:tweet_id,user_id:user_id},function(){
            counter.show();
            button.addClass('like-btn');
            button.removeClass('unlike-btn');
            count--;
            if(count===0){
                counter.hide();
            }else{
                counter.text(count);
            }
            counter.text(count);
            button.find('.fa-thumbs-up').addClass('fa-thumbs-o-up');
            button.find('.fa-thumbs-o-up').removeClass('fa-thumbs-up');
        });
    });

});