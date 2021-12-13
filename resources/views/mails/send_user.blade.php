<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Hello {{$receiver->first_name}}</p>
    <p>{{$sender->first_name}} has sent you a message through Holistify:</p>
    <p><b>Message:</b></p>
    <p><b>{{ $text }}</b></p>
    <p>To reply to this message, simply reply to this email and {{$sender->first_name}} will receive your message.</p>
    <p>Thanks<br/>The Holistify Team</p>
</body>
</html>
