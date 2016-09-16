@include('doc.header')
<div id="api_documentation">
    <div class="wrapper">
        <form>
            Auth token:
            <input type="hidden" value="X-Auth-Token" placeholder="key" id="apikey_key">
            <div class="input-group">
                <input type="text" placeholder="Token" id="apikey_value">
                <div class="font-small font-italic">Auth token will be saved in localstorage.</div>
                <span class="button-group">
			    {{ Form::button('Save', array('type' => 'button', 'id' => 'save_auth_data')) }}
			</span>
            </div>
        </form>
        <input id="apiUrl" type="hidden" value="{{ $url }}">
        <div id="apiDocAccordion" class="accordion">
            <ul>
                {{ $content }}
            </ul>
        </div>
    </div>
</div>
@include('doc.footer')
</body>
</html>
