<div id="footer">
    <div class="wrapper">
        <div class="float-left">
            Copyright &copy; 2014-{{ Date('Y') }} David Söderberg
        </div>
        <div class="menu float-right">
            <ul>
                <li class="divider"></li>
                <li>{{ HTML::actionlink($url = array('action' => 'DocController@index'), 'Doc', ['target' => '_blank'])  }}</li>
                <li class="divider"></li>

                <li>{{ HTML::actionlink($url = array('action' => 'DocController@api'), 'Api', ['target' => '_blank'])  }}</li>
                <li class="divider"></li>
            </ul>
        </div>
    </div>
</div>
<script>
    var appConfig = {};
</script>
<script src="{{ HTML::version('js/script.min.js') }}"></script>