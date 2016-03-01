<div class="row">
    @if(isset($template))
        @foreach($template as $key => $val)
            <div class="storeTemplatesItem pull-left">
                <a href="https://s3.amazonaws.com/cinehost.assets/zero_templates/M1_Screenshot_Home.png" class="open-album" data-open-id="album-0">
                    <img width="213" src="{{ isset($val->image) ? $val->image : "" }}" alt="" title="" class="">
                    <p>{{ isset($val->name) ? $val->name : "" }}</p>
                </a>
                <a class="cp" onclick="activateTemplate('5554895ed4c6abc94d035fdd')">Activate</a>
                <a href="https://s3.amazonaws.com/cinehost.assets/zero_templates/M1_Screenshot_Home.png" rel="album-0" class="image-show"></a>
                <a href="https://s3.amazonaws.com/cinehost.assets/zero_templates/M1_Screenshot_Film.png" rel="album-0" class="image-show"></a>
            </div>
        @endforeach
    @endif
    <div class="cl"></div>
    <script>
        function activateTemplate(template_id){
            $.ajax({
                type: "POST",
                url: "classes/Platform/engine.php",
                data: "act=activateTemplate&template_id="+template_id,
                dataType: "json",
                success: function (data) {
                    $(".storeTemplates").html(data);
                }
            });
        }
        $(document).ready(function(){

            $(".image-show").fancybox();

            $(".open-album").click(function(e) {
                var el, id = $(this).data("open-id");
                if(id){
                    el = $(".image-show[rel=" + id + "]:eq(0)");
                    e.preventDefault();
                    el.click();
                }
            });
        });

    </script>
</div>