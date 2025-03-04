<!DOCTYPE html>
<html>
<head>
    <title>{{ $data['subject'] ?? 'Notification' }}</title>
</head>
<body>
<h1>{{ $data['title'] ?? 'Notification' }}</h1>

<p>{!! $data['message'] ?? '' !!}</p>

@if(isset($data['additionalInfo']))
    <div>
        <h2>Additional Information</h2>
        <p>{!! $data['additionalInfo'] !!}</p>
    </div>
@endif

<p>Thank you for using our application.</p>

<p>Regards,<br>{{ config('app.name') }} Team</p>
</body>
</html>
