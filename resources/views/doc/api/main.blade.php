<li class="">
    <a href="">{{ $method }} {{ $route }}</a>
    <div class="content" id="php-apidocaccordion{{ $elt_id }}">

        <div id="php-apidoctab{{ $elt_id }}" class="tabs">
            <ul class="clearfix">
                <li class="open"><a href="">Info</a></li>
                <li><a href="">Sandbox</a></li>
                <li><a href="">Sample output</a></li>
            </ul>
            <ul>
                <li class="open">
                    <div class="well">
                        {{ $description }}
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Headers</strong></div>
                        <div class="panel-body">
                            {{ $headers }}
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Parameters</strong></div>
                        <div class="panel-body">
                            {{ $parameters }}
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Body</strong></div>
                        <div class="panel-body">
                            {{ $body }}
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            {{ $sandbox_form }}
                        </div>
                        <div class="col-md-12">
                            Response
                            <hr>
                            <div class="col-md-12" style="overflow-x:auto">
                                <pre id="response_headers{{ $elt_id }}"></pre>
                                <pre id="response{{ $elt_id }}"></pre>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row">
                        <div class="col-md-12">
                            {{ $sample_response_headers }}
                            {{ $sample_response_body }}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</li>
<!--
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            {{ $method }} <a data-toggle="collapse" data-parent="#accordion{{ $elt_id }}"
                             href="#collapseOne{{ $elt_id }}"> {{ $route }}</a>
        </h4>
    </div>
    <div id="collapseOne{{ $elt_id }}" class="panel-collapse collapse">
        <div class="panel-body">

            <ul class="nav nav-tabs" id="php-apidoctab{{ $elt_id }}">
                <li class="active"><a href="#info{{ $elt_id }}" data-toggle="tab">Info</a></li>
                <li><a href="#sandbox{{ $elt_id }}" data-toggle="tab">Sandbox</a></li>
                <li><a href="#sample{{ $elt_id }}" data-toggle="tab">Sample output</a></li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane active" id="info{{ $elt_id }}">
                    <div class="well">
                        {{ $description }}
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Headers</strong></div>
                        <div class="panel-body">
                            {{ $headers }}
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Parameters</strong></div>
                        <div class="panel-body">
                            {{ $parameters }}
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Body</strong></div>
                        <div class="panel-body">
                            {{ $body }}
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="sandbox{{ $elt_id }}">
                    <div class="row">
                        <div class="col-md-12">
                            {{ $sandbox_form }}
                        </div>
                        <div class="col-md-12">
                            Response
                            <hr>
                            <div class="col-md-12" style="overflow-x:auto">
                                <pre id="response_headers{{ $elt_id }}"></pre>
                                <pre id="response{{ $elt_id }}"></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="sample{{ $elt_id }}">
                    <div class="row">
                        <div class="col-md-12">
                            {{ $sample_response_headers }}
                            {{ $sample_response_body }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
-->