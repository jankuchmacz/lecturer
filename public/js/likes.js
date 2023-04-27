$(document).ready(function() {
   
    $('.studentPresent').show();
    $('.studentAbsent').show();
    $('.noActionYet').show();

    
    $('.toogle-presence').on('click', function(e) {
        e.preventDefault();
        //window.alert("dziala");
        

        var $link = $(e.currentTarget);
        //console.log($link);
       // window.alert($link);

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
                case 'present':
                    $('.click-to-give-presence-'+data.id).hide();
                    $('.click-to-give-absence-'+data.id).hide();
                    $('.click-to-undo-presence-'+data.id).show();
                    $('.click-to-undo-absence-'+data.id).hide();
    
                break;
                case 'absent':
                    $('.click-to-give-presence-'+data.id).hide();
                    $('.click-to-give-absence-'+data.id).hide();
                    $('.click-to-undo-presence-'+data.id).hide();
                    $('.click-to-undo-absence-'+data.id).show();
        

                break;
                case 'undo_present':
                    $('.click-to-give-presence-'+data.id).show();
                    $('.click-to-give-absence-'+data.id).show();
                    $('.click-to-undo-presence-'+data.id).hide();
                    $('.click-to-undo-absence-'+data.id).hide();

                break;

                case 'undo_absent':
                    $('.click-to-give-presence-'+data.id).show();
                    $('.click-to-give-absence-'+data.id).show();
                    $('.click-to-undo-presence-'+data.id).hide();
                    $('.click-to-undo-absence-'+data.id).hide();

                break;

            }
            
        })
    });

});
