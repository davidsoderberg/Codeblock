<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Welcome {{$Username}},</h2>
        <p>
            You can activate your user {{ link_to('/activate/'.$id.'/'.$token, 'here') }}.
        </p>
        <p>From/<br /> David Southmountian</p>
    </body>
</html>