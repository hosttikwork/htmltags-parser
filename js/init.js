$('body').on('submit', '.form-link', function(event) {
    event.stopPropagation();
    event.preventDefault();
    var $this = $(this);
    var url = $this.find('input[name=url]').first().val();


    $.ajax({
        url: '/php/parser.php',
        type: 'POST',
        dataType: 'json',
        data: {
            url: url,
            action: 'link',
        },
        success: function(data) {
            $(".link-wrap").remove();
            // var tegs = "";
            var stat = "";
            var $i = 0;
            $.each(data.tags, function(key, value) {
                if ($i == 0) {
                    // tegs += key;
                    stat += '<br>' + key + ':' + value + '<br>';

                } else {
                    // tegs += "," + key;
                    stat += key + ':' + value + '<br>';
                }
                $i++;

            });
            $('.link-statistic').append(
                '<div class="link-wrap">'+
                '<div class="row">Количество использования тегов:' + stat +
                '</div>'+
                '</div>');

           
        }

    });
});
var files;
$('input[type=file]').change(function(){
    files = this.files;
    });
$('body').on('submit', '.form-upload', function(event) {
    event.stopPropagation();
    event.preventDefault();
    var file = new FormData();
    $.each( files, function( key, value ){
        file.append( key, value );
    });
    file.append('action','file');
   
    $.ajax({
        url: '/php/parser.php',
        type: 'POST',
        dataType: 'json',
        data:file,
        cache: false,
        processData: false, // Не обрабатываем файлы (Don't process the files)
        contentType: false, // Так jQuery скажет серверу что это строковой запрос
        success: function(data) {
            console.log(data);
            $(".link-wrap").remove();
            // var tegs = "";
            var stat = "";
            var $i = 0;
            $.each(data.tags, function(key, value) {
                if ($i == 0) {
                    // tegs += key;
                    stat += '<br>' + key + ':' + value + '<br>';

                } else {
                    // tegs += "," + key;
                    stat += key + ':' + value + '<br>';
                }
                $i++;

            });
            $('.file-statistic').append(
                '<div class="link-wrap">'+
                '<div class="row">Количество использования тегов:' + stat +
                '</div>'+
                '</div>');

           
        }

    });
});

