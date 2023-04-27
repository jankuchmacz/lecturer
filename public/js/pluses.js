$(document).ready(function() {
    
    $('.toogle-pluses').on('click', function(e) {
        e.preventDefault();
        var $link = $(e.currentTarget);

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function(data) {
            
            switch (data.action)
            {
                case 'givePlus':
                    var $plusesText= $('.number-pluses-'+data.id).text();
                    var $pluses = parseInt($plusesText);
                    $pluses+=1;
                    $('.number-pluses-'+data.id).html($pluses);
                break;
                case 'removePlus':
                    var $plusesText= $('.number-pluses-'+data.id).text();
                    var $pluses = parseInt($plusesText);
                    if($pluses>=1)
                    {
                          $pluses-=1;
                    }
                    $('.number-pluses-'+data.id).html($pluses);
                break;
                
            }
            
        })
    });

});