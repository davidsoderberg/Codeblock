<div class="col-md-6">
    Headers
    <hr/>
    <div class="headers">
        {{ $headers }}
    </div>
</div>
<div class="col-md-6">
    <form enctype="application/x-www-form-urlencoded" role="form" action="{{ $route }}" method="{{ $method }}" name="form{{ $elt_id }}" id="form{{ $elt_id }}">

        Parameters
        <hr/>
        {{ $params }}
        <button type="submit" class="btn btn-success send" rel="{{ $elt_id }}">Send</button>
    </form></div>