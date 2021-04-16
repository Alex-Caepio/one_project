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
<p>Hello, {{$receiver->first_name}} {{$receiver->last_name}} </p>
<p>{{$sender->first_name}} {{$sender->last_name}} just sent you a message below:</p>
<p>{{ $text }}</p>
</body>
</html>
