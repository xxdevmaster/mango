<div class="table-responsive">
    <table class="table">
        <tr>
            <td>Favicon</td>
            <td>
                <img width="32" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ !empty($store->favicon) ? $store->favicon : 'def_favicon.png' }}" id="favicon_imgview">
            </td>
        </tr>
        <tr>
            <td>Search Engine Image</td>
            <td>
                <img width="150" src="http://cinecliq.assets.s3.amazonaws.com/files/{{ !empty($store->seo_image) ? $store->seo_image : 'def_seo_image.jpg' }}" id="seo_image_view">
            </td>
        </tr>
        <tr>
            <td>SEO Title</td>
            <td>{{ isset($store->seo_title) ? $store->seo_title : '' }}</td>
        </tr>
        <tr>
            <td>SEO Meta Keywords</td>
            <td>{{ isset($store->seo_keys) ? $store->seo_keys : '' }}</td>
        </tr>
        <tr>
            <td>SEO Meta Description</td>
            <td>{{ isset($store->seo_description) ? $store->seo_description : '' }}</td>
        </tr>
        <tr>
            <td>Facebook URL</td>
            <td>{{ isset($store->fbpage) ? urldecode($store->fbpage) : '' }}</td>
        </tr>
        <tr>
            <td>Twitter URL</td>
            <td>
                <a href="mailto:{{ isset($store->twpage) ? $store->twpage : '' }}" class="table-link val">{{ isset($store->twpage) ? $store->twpage : '' }}</a>
            </td>
        </tr>
        <tr>
            <td>Google Analytics Code</td>
            <td>
                <a href="{{ isset($store->ga_code) ? $store->ga_code : '' }}" target="blank" class="table-link val">{{ isset($store->ga_code) ? $store->ga_code : '' }}</a>
            </td>
        </tr>
        <tr>
            <td>Terms of Use</td>
            <td>{{ isset($store->terms) ? $store->terms : '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>
    <div class="pull-left">
        <button class="btn btn-primary btn-md" id="editSettings" type="button">Edit</button>
    </div>
</div>

<script>
    $(document).ready(function(){
        $("#editSettings").click(function(){
            autoCloseMsgHide();
            $(".loading").show();
            $.post('/store/settings/drawEditSettings', function(data){
                $("#settings").html(data);
                $(".loading").hide();
            });
        });
    });
</script>