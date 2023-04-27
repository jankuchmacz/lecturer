$(document).ready(function() {
   
    $('.got5').show();
    $('.got45').show();
    $('.got4').show();
    $('.got35').show();
    $('.got3').show();
    $('.got2').show();
    $('.noActionYet').show();

    
    $('.toogle-presence').on('click', function(e) {
        e.preventDefault();
        var $link = $(e.currentTarget);

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function(data) {
            //window.alert("dziala");
           /* $.ajax($link, function(data) {
                window.alert("dziala");
                console.log(data);*/
            switch (data.action)
            {
                case 'give5':
                    $('.click-to-undo-5-'+data.id).show();
                    $('.click-to-give-5-'+data.id).hide();
                    $('.click-to-give-45-'+data.id).hide();
                    $('.click-to-give-4-'+data.id).hide();
                    $('.click-to-give-35-'+data.id).hide();
                    $('.click-to-give-3-'+data.id).hide();
                    $('.click-to-give-2-'+data.id).hide();
                    $('.click-to-undo-45-'+data.id).hide();
                    $('.click-to-undo-4-'+data.id).hide();
                    $('.click-to-undo-35-'+data.id).hide();
                    $('.click-to-undo-3-'+data.id).hide();
                    $('.click-to-undo-2-'+data.id).hide();   
                break;
                case 'give4.5':
                    $('.click-to-give-5-'+data.id).hide();
                    $('.click-to-give-45-'+data.id).hide();
                    $('.click-to-give-4-'+data.id).hide();
                    $('.click-to-give-35-'+data.id).hide();
                    $('.click-to-give-3-'+data.id).hide();
                    $('.click-to-give-2-'+data.id).hide();
                    $('.click-to-undo-5-'+data.id).hide();
                    $('.click-to-undo-45-'+data.id).show();
                    $('.click-to-undo-4-'+data.id).hide();
                    $('.click-to-undo-35-'+data.id).hide();
                    $('.click-to-undo-3-'+data.id).hide();
                    $('.click-to-undo-2-'+data.id).hide();   
                break;
                case 'give4':
                    $('.click-to-give-5-'+data.id).hide();
                    $('.click-to-give-45-'+data.id).hide();
                    $('.click-to-give-4-'+data.id).hide();
                    $('.click-to-give-35-'+data.id).hide();
                    $('.click-to-give-3-'+data.id).hide();
                    $('.click-to-give-2-'+data.id).hide();
                    $('.click-to-undo-5-'+data.id).hide();
                    $('.click-to-undo-45-'+data.id).hide();
                    $('.click-to-undo-4-'+data.id).show();
                    $('.click-to-undo-35-'+data.id).hide();
                    $('.click-to-undo-3-'+data.id).hide();
                    $('.click-to-undo-2-'+data.id).hide();   
                break;
                case 'give3.5':
                    $('.click-to-give-5-'+data.id).hide();
                    $('.click-to-give-45-'+data.id).hide();
                    $('.click-to-give-4-'+data.id).hide();
                    $('.click-to-give-35-'+data.id).hide();
                    $('.click-to-give-3-'+data.id).hide();
                    $('.click-to-give-2-'+data.id).hide();
                    $('.click-to-undo-5-'+data.id).hide();
                    $('.click-to-undo-45-'+data.id).hide();
                    $('.click-to-undo-4-'+data.id).hide();
                    $('.click-to-undo-35-'+data.id).show();
                    $('.click-to-undo-3-'+data.id).hide();
                    $('.click-to-undo-2-'+data.id).hide();   
                break;
                case 'give3':
                    $('.click-to-give-5-'+data.id).hide();
                    $('.click-to-give-45-'+data.id).hide();
                    $('.click-to-give-4-'+data.id).hide();
                    $('.click-to-give-35-'+data.id).hide();
                    $('.click-to-give-3-'+data.id).hide();
                    $('.click-to-give-2-'+data.id).hide();
                    $('.click-to-undo-5-'+data.id).hide();
                    $('.click-to-undo-45-'+data.id).hide();
                    $('.click-to-undo-4-'+data.id).hide();
                    $('.click-to-undo-35-'+data.id).hide();
                    $('.click-to-undo-3-'+data.id).show();
                    $('.click-to-undo-2-'+data.id).hide();   
                break;
                case 'give2':
                    $('.click-to-give-5-'+data.id).hide();
                    $('.click-to-give-45-'+data.id).hide();
                    $('.click-to-give-4-'+data.id).hide();
                    $('.click-to-give-35-'+data.id).hide();
                    $('.click-to-give-3-'+data.id).hide();
                    $('.click-to-give-2-'+data.id).hide();
                    $('.click-to-undo-5-'+data.id).hide();
                    $('.click-to-undo-45-'+data.id).hide();
                    $('.click-to-undo-4-'+data.id).hide();
                    $('.click-to-undo-35-'+data.id).hide();
                    $('.click-to-undo-3-'+data.id).hide();
                    $('.click-to-undo-2-'+data.id).show();   
                break;
                case 'undo':
                    $('.click-to-give-5-'+data.id).show();
                    $('.click-to-give-45-'+data.id).show();
                    $('.click-to-give-4-'+data.id).show();
                    $('.click-to-give-35-'+data.id).show();
                    $('.click-to-give-3-'+data.id).show();
                    $('.click-to-give-2-'+data.id).show();
                    $('.click-to-undo-5-'+data.id).hide();
                    $('.click-to-undo-45-'+data.id).hide();
                    $('.click-to-undo-4-'+data.id).hide();
                    $('.click-to-undo-35-'+data.id).hide();
                    $('.click-to-undo-3-'+data.id).hide();
                    $('.click-to-undo-2-'+data.id).hide(); 
                break;
               

            }
            
        })
    });

});
