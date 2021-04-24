<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$replacedSubject}}</title>
</head>
<body>
<table>
    @if($logoContent)
        <tr>
            <td>
                <img src="{{ $message->embedData($logoContent, $emailData->logo_filename) }}"
                     alt="{{$replacedSubject}}">
            </td>
        </tr>
    @endif
    {!! $replacedContent !!}
    @if ($footer)
        {!! $footer !!}
    @endif
</table>
</body>
</html>
