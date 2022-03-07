<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Company Invitation</title>
</head>
<body>
    <h1>Hi<br>Welcome to Our Company <span style="color:green">{{ $data['company'] }} </span>.Please Accept the invitation to proceed</h1>
    <a href="{{ $data['link'] }}/accept-invite/{{ $data['clientId']}}" style="background-color: green;padding:6px;color:white;border:1px solid rgb(15, 88, 15);text-decoration: none">Accept Invitation to {{ $data['company'] }}</a>
    <a href="{{ $data['link'] }}/decline-invite/{{ $data['clientId']}}" style="background-color: red;padding:6px;color:white;border:1px solid rgb(97, 5, 20);text-decoration: none">Decline Invitation </a>
</body>
</html>
