<div class="table-responsive">
    <table class="table">
        <tr>
            <td>Favicon</td>
            <td>{{ isset($store->title) ? $store->title : '' }}</td>
        </tr>
        <tr>
            <td>Search Engine Image</td>
            <td>{{ isset($store->title) ? $store->title : '' }}</td>
        </tr>
        <tr>
            <td>SEO Title</td>
            <td>{{ isset($store->title) ? $store->title : '' }}</td>
        </tr>
        <tr>
            <td>SEO Meta Keywords</td>
            <td>{{ isset($store->person) ? $store->person : '' }}</td>
        </tr>
        <tr>
            <td>SEO Meta Description</td>
            <td>{{ isset($store->address) ? $store->address : '' }}</td>
        </tr>
        <tr>
            <td>Facebook URL</td>
            <td>{{ isset($store->phone) ? $store->phone : '' }}</td>
        </tr>
        <tr>
            <td>Twitter URL</td>
            <td>
                <a href="mailto:{{ isset($store->email) ? $store->email : '' }}" class="table-link val">{{ isset($store->email) ? $store->email : '' }}</a>
            </td>
        </tr>
        <tr>
            <td>Google Analytics Code</td>
            <td>
                <a href="{{ isset($store->website) ? $store->website : '' }}" target="blank" class="table-link val">{{ isset($store->website) ? $store->website : '' }}</a>
            </td>
        </tr>
        <tr>
            <td>Terms of Use</td>
            <td>{{ isset($store->brief) ? $store->brief : '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>
    <div class="pull-left">
        <button class="btn btn-primary btn-md" type="button">Edit</button>
    </div>
</div>

<script>
    $(document).ready(function(){
        $(".editStore").click(function(){
            autoCloseMsgHide();
            $(".loading").show();
            $.post('/store/profile/drawEditstore', function(data){
                $("#profileStore").html(data);
                $(".loading").hide();
            });
        });
    });
</script>