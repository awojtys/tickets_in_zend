$('form.ajax').on('submit', function(e) {
    e.preventDefault();
    var that = $(this),
        url = that.attr('action'),
        type = that.attr('method'),
        data = $(this).serialize();        
    
    $.ajax({
        url:url,
        type:type,
        data: data,
        dataType: 'json',
        beforeSend: function(){

        },
        success: function(response)
        {            
            $('#filter .ticket_table tbody tr').remove();
            for (i in response) {
                var ticket = response[i];
                $('#filter .ticket_table tbody').append('<tr></tr>');
                var tr = $('#filter .ticket_table tbody tr:last-child');
                for(z in ticket) {
                    var td = ticket[z];
                    tr.append('<td>' + td + '</td>');
                }
                tr.append('<td></td>');
            }
            
            
        }
    });
});
//sortowanie
$(document).ready(function()
{
    var clicked = "";
    var checked = 0;
    
    //pobranie ktore pole zostalo kliknięte
    $("th[data-col]").click(function()
    {
        if(clicked != $(this).data('col'))
        {
            
            var type = "asc";
            checked = 1;
        }
        if(clicked == $(this).data('col'))
        {
            if(checked == 0)
            {
                var type = "asc";
                checked = 1;
            }
            else
            {
                var type = "desc";
                checked = 0;
            }
        }
        
        
        var column = $(this).data('col');
        var i = null;
        var column_number = 0;
        
        column_number = $("th[data-col]").index($("th[data-col='"+column+"']"));
        
        

        //pobranie tablicy danych
        var j = 0;
        var i = 0;
        var g = 0;
        var info = [];

        $("tbody tr").each(function(){
           info[i] = [];
           $(this).find('td').each(function() {
             info[i][g] = $(this).text();
             g++;
             j++;
           });
           g = 0;
           i++;
        });
        
        //posortowanie danych
        i = 0;
        j = 0;
        g = 0;
        var safe = [];
        console.log(type);
        do
        {
            for(i=0;i<info.length-1;i++)
            {
                if(type == "asc")
                {
                    if(info[i][column_number] > info[i+1][column_number])
                    {
                        safe = info[i];
                        info[i] = info[i+1];
                        info[i+1] = safe;
                    }
                }
                else
                {
                    if(info[i][column_number] < info[i+1][column_number])
                    {
                        safe = info[i];
                        info[i] = info[i+1];
                        info[i+1] = safe;
                    }
                }
            }
            g++;
        }
        while (g != i);

        //wyczyszczenie tabeli
        $('#filter .ticket_table tbody tr').remove();
        
        //utworzenie posortowanej tabeli na nowo
        for (i in info) {
                var row = info[i];
                $('#filter .ticket_table tbody').append('<tr></tr>');
                var tr = $('#filter .ticket_table tbody tr:last-child');
                for(j in row) {
                    var td = row[j];
                    tr.append('<td>' + td + '</td>');
                }
            }

        clicked = $(this).data('col');
    });

});
